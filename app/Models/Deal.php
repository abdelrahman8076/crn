<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB; // Add this line
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
    $this->loadMissing(['client.assignedSale', 'client.assignedManager']);
    $client = $this->client;
    if (!$client) return;

    $saleUser = $client->assignedSale;
    $managerUser = $client->assignedManager;

    // Determine users involved in the deal
    $usersToSync = collect([$saleUser, $managerUser])->filter()->unique('id');

    if ($usersToSync->isEmpty()) return;

    DB::transaction(function () use ($usersToSync, $previousStage, $previousAmount) {
        foreach ($usersToSync as $user) {
            // Find the correct target record for the user
            $target = $user->targets()->where('is_active', true)->first() 
                      ?? $user->targets()->orderBy('period', 'desc')->first();

            if (!$target) continue;

            $currentAmount = (float) $this->amount;
            $prevAmount = (float) $previousAmount;

            /**
             * 1. Calculate Delta (Difference)
             * This handles new deals, deleted deals, and amount changes.
             */
            $delta = 0;
            if ($this->stage === 'closed-won' && $previousStage !== 'closed-won') {
                $delta = $currentAmount;
            } elseif ($this->stage !== 'closed-won' && $previousStage === 'closed-won') {
                $delta = -$prevAmount;
            } elseif ($this->stage === 'closed-won' && $previousStage === 'closed-won') {
                $delta = $currentAmount - $prevAmount;
            }

            if ($delta !== 0) {
                $roleName = strtolower($user->role->name ?? '');

                /**
                 * 2. Apply Strict Separation Logic
                 * For both Sales and Manager, we update target_remaining
                 * (decrease remaining when deal is won)
                 */
                // SALESPERSON & MANAGER TRACKER: Decreases remaining quota
                // Every dollar won is SUBTRACTED from target_remaining
                $target->target_remaining = max(0, min($target->target_total, (float)$target->target_remaining - $delta));

                $target->save();
                
                Log::info("SyncTargets: User {$user->id} ({$roleName}) | Delta: {$delta} | New Remaining: {$target->target_remaining}");
            }
        }
    });
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

