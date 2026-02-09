<?php

namespace App\Policies;

use App\Models\Scan;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ScanPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Scan $scan): bool
    {
        return $user->id === $scan->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasScansRemaining();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Scan $scan): bool
    {
        return $user->id === $scan->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Scan $scan): bool
    {
        return $user->id === $scan->user_id;
    }
}
