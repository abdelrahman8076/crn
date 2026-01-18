<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PositionsController extends Controller
{
    /**
     * Display a listing of positions.
     */
    public function index()
    {
        $positions = Position::with('parent', 'children')
            ->orderBy('level')
            ->orderBy('sort_order')
            ->get();
        
        return view('admin.positions.index', compact('positions'));
    }

    /**
     * Show the form for creating a new position.
     */
    public function create()
    {
        $positions = Position::orderBy('level')->orderBy('sort_order')->get();
        return view('admin.positions.create', compact('positions'));
    }

    /**
     * Store a newly created position.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:positions,name',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:positions,id',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        DB::transaction(function () use ($validated) {
            // Calculate level based on parent
            $level = 0;
            if ($validated['parent_id']) {
                $parent = Position::findOrFail($validated['parent_id']);
                $level = $parent->level + 1;
            }

            Position::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'parent_id' => $validated['parent_id'] ?? null,
                'level' => $level,
                'sort_order' => $validated['sort_order'] ?? 0,
            ]);
        });

        return redirect()->route('admin.positions.index')
            ->with('success', 'Position created successfully.');
    }

    /**
     * Show the form for editing the specified position.
     */
    public function edit(Position $position)
    {
        $positions = Position::where('id', '!=', $position->id)
            ->orderBy('level')
            ->orderBy('sort_order')
            ->get();
        
        return view('admin.positions.edit', compact('position', 'positions'));
    }

    /**
     * Update the specified position.
     */
    public function update(Request $request, Position $position)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:positions,name,' . $position->id,
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:positions,id|not_in:' . $position->id,
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // Prevent circular hierarchy (position cannot be its own ancestor)
        if ($validated['parent_id']) {
            $parent = Position::findOrFail($validated['parent_id']);
            if ($parent->isAncestorOf($position)) {
                return back()->withErrors(['parent_id' => 'Cannot assign parent: would create circular hierarchy.'])->withInput();
            }
        }

        DB::transaction(function () use ($validated, $position) {
            // Calculate level based on parent
            $level = 0;
            if ($validated['parent_id']) {
                $parent = Position::findOrFail($validated['parent_id']);
                $level = $parent->level + 1;
            }

            $position->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'parent_id' => $validated['parent_id'] ?? null,
                'level' => $level,
                'sort_order' => $validated['sort_order'] ?? 0,
            ]);

            // Update children levels recursively
            $this->updateChildrenLevels($position);
        });

        return redirect()->route('admin.positions.index')
            ->with('success', 'Position updated successfully.');
    }

    /**
     * Remove the specified position.
     */
    public function destroy(Position $position)
    {
        // Check if position has users/admins assigned
        if ($position->users()->count() > 0 || $position->admins()->count() > 0) {
            return back()->with('error', 'Cannot delete position: it has users or admins assigned. Please reassign them first.');
        }

        DB::transaction(function () use ($position) {
            // Reassign children to parent's parent (or null)
            $newParentId = $position->parent_id;
            $position->children()->update(['parent_id' => $newParentId]);
            
            // Recalculate levels for children
            foreach ($position->children as $child) {
                $this->updateChildrenLevels($child);
            }

            $position->delete();
        });

        return redirect()->route('admin.positions.index')
            ->with('success', 'Position deleted successfully.');
    }

    /**
     * Recursively update levels for position and its children
     */
    private function updateChildrenLevels(Position $position)
    {
        $level = $position->parent ? $position->parent->level + 1 : 0;
        $position->update(['level' => $level]);

        foreach ($position->children as $child) {
            $this->updateChildrenLevels($child);
        }
    }
}
