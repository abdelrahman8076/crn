<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Deal extends Model
{
    use HasFactory;

    protected $fillable = ['deal_name','amount','stage','client_id'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function tasks()
    {
        return $this->morphMany(Task::class,'related');
    }

    public function notes()
    {
        return $this->morphMany(Note::class,'related');
    }
     protected $casts = [
        'created_at' => 'date:Y-m-d',
    ];

    /**
     * Sync this deal with the responsible user's active target.
     * $previousStage and $previousAmount are the values before the change (if any).
     */
    public function syncTargets($previousStage = null, $previousAmount = null)
    {
        // Determine client and responsible user
        $client = $this->client ?? \App\Models\Client::find($this->client_id);
        if (!$client) return;

        $user = $client->assignedSale ?? $client->assignedManager ?? null;
        if (!$user) return;

        // Logging for debugging
        Log::info('Deal::syncTargets called', [
            'deal_id' => $this->id,
            'client_id' => $client->id,
            'user_id' => $user->id,
            'current_stage' => $this->stage,
            'current_amount' => $this->amount,
            'previous_stage' => $previousStage,
            'previous_amount' => $previousAmount,
        ]);

        // Prefer active target, otherwise fallback to latest target
        $target = $user->targets()->where('is_active', true)->first();
        if (!$target) {
            Log::info('Deal::syncTargets - no active target, falling back to latest', ['user_id' => $user->id]);
            $target = $user->targets()->orderBy('period', 'desc')->first();
        }
        if (!$target) {
            Log::warning('Deal::syncTargets - no target available for user', ['user_id' => $user->id]);
            return;
        }

        Log::info('Deal::syncTargets - target selected', ['target_id' => $target->id, 'target_remaining' => $target->target_remaining]);

        $beforeRemaining = $target->target_remaining;
        $beforeTotal = $target->target_total;
        Log::info('Deal::syncTargets - before values', ['target_id' => $target->id, 'before_remaining' => $beforeRemaining, 'before_total' => $beforeTotal]);

        $currentStage = $this->stage;
        $currentAmount = is_numeric($this->amount) ? (int) round($this->amount) : 0;
        $prevStage = $previousStage;
        $prevAmount = is_numeric($previousAmount) ? (int) round($previousAmount) : 0;

        // Case: created and now closed-won
        if (is_null($prevStage) && $currentStage === 'closed-won') {
            $target->target_remaining = max(0, $target->target_remaining - $currentAmount);
            $target->save();
            Log::info('Deal::syncTargets - deducted on create closed-won', ['deal_id' => $this->id, 'amount' => $currentAmount, 'new_remaining' => $target->target_remaining]);
            Log::info('Deal::syncTargets - after save', ['target_id' => $target->id, 'before_remaining' => $beforeRemaining, 'after_remaining' => $target->target_remaining, 'branch' => 'created closed-won']);
            return;
        }

        // Case: moved into closed-won
        if ($prevStage !== 'closed-won' && $currentStage === 'closed-won') {
            $target->target_remaining = max(0, $target->target_remaining - $currentAmount);
            $target->save();
            Log::info('Deal::syncTargets - deducted on stage change to closed-won', ['deal_id' => $this->id, 'amount' => $currentAmount, 'new_remaining' => $target->target_remaining]);
            Log::info('Deal::syncTargets - after save', ['target_id' => $target->id, 'before_remaining' => $beforeRemaining, 'after_remaining' => $target->target_remaining, 'branch' => 'stage to closed-won']);
            return;
        }

        // Case: moved out of closed-won -> add back previous amount
        if ($prevStage === 'closed-won' && $currentStage !== 'closed-won') {
            $target->target_remaining = min($target->target_total, $target->target_remaining + $prevAmount);
            $target->save();
            Log::info('Deal::syncTargets - reverted on stage change from closed-won', ['deal_id' => $this->id, 'amount' => $prevAmount, 'new_remaining' => $target->target_remaining]);
            Log::info('Deal::syncTargets - after save', ['target_id' => $target->id, 'before_remaining' => $beforeRemaining, 'after_remaining' => $target->target_remaining, 'branch' => 'revert from closed-won']);
            return;
        }

        // Case: both closed-won but amount changed -> adjust by delta
        if ($prevStage === 'closed-won' && $currentStage === 'closed-won') {
            $delta = $currentAmount - $prevAmount;
            // if delta > 0 -> deduct additional amount, if delta < 0 -> add back
            if ($delta > 0) {
                $target->target_remaining = max(0, $target->target_remaining - $delta);
                Log::info('Deal::syncTargets - deducted delta on amount increase', ['deal_id' => $this->id, 'delta' => $delta, 'new_remaining' => $target->target_remaining]);
            } elseif ($delta < 0) {
                $target->target_remaining = min($target->target_total, $target->target_remaining + abs($delta));
                Log::info('Deal::syncTargets - added back delta on amount decrease', ['deal_id' => $this->id, 'delta' => $delta, 'new_remaining' => $target->target_remaining]);
            }
            $target->save();
            Log::info('Deal::syncTargets - after save', ['target_id' => $target->id, 'before_remaining' => $beforeRemaining, 'after_remaining' => $target->target_remaining, 'branch' => 'delta closed-won']);
            return;
        }

        // Nothing to do otherwise
    }

    protected static function booted()
    {
        static::created(function ($deal) {
            // New deal created
            $deal->syncTargets(null, null);
        });

        static::updated(function ($deal) {
            $previousStage = $deal->getOriginal('stage');
            $previousAmount = $deal->getOriginal('amount');
            Log::info('Deal::updated observer fired', ['deal_id' => $deal->id, 'previous_stage' => $previousStage, 'previous_amount' => $previousAmount, 'current_stage' => $deal->stage, 'current_amount' => $deal->amount]);
            $deal->syncTargets($previousStage, $previousAmount);
        });

        static::deleted(function ($deal) {
            $previousStage = $deal->getOriginal('stage');
            $previousAmount = $deal->getOriginal('amount');
            // Revert if it was closed-won
            if ($previousStage === 'closed-won') {
                $deal->syncTargets('closed-won', $previousAmount);
            }
        });
    }
    /**
 * Get the stage formatted as a Nexus Badge HTML.
 */
public function getStageBadgeAttribute(): string
{
    $class = match($this->stage) {
        'closed-won'  => 'success',
        'closed-lost' => 'danger',
        'negotiation' => 'warning',
        'proposal'    => 'info',
        default       => 'secondary'
    };
    $label = __("deals.{$this->stage}");
    return "<span class='badge bg-soft-{$class} text-{$class} px-3 py-2 rounded-pill'>{$label}</span>";
}

/**
 * Get the amount formatted as currency.
 */
public function getFormattedAmountAttribute(): string
{
    return '<span class="fw-bold text-dark">$' . number_format($this->amount, 2) . '</span>';
}
}

