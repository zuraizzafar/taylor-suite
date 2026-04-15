<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasBranchScope
{
    /**
     * Scope a query to the current user's branch (branch_manager only).
     * Admins see all data.
     */
    protected function branchQuery(Builder $query, string $column = 'branch_id'): Builder
    {
        $user = auth()->user();
        if ($user && $user->role === 'branch_manager' && $user->branch_id) {
            return $query->where($column, $user->branch_id);
        }
        return $query;
    }

    /**
     * Return the branch_id to stamp on new records, or null for admins.
     */
    protected function currentBranchId(): ?int
    {
        $user = auth()->user();
        if ($user && $user->role === 'branch_manager') {
            return $user->branch_id;
        }
        return null;
    }
}
