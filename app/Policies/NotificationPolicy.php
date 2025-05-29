<?php

namespace App\Policies;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class NotificationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, $notification): bool
    {
        return $user->id === $notification->notifiable_id && $notification->notifiable_type === User::class;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, $notification): bool
    {
        return $user->id === $notification->notifiable_id && $notification->notifiable_type === User::class;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, $notification): bool
    {
        return $user->id === $notification->notifiable_id && $notification->notifiable_type === User::class;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Notification $notification): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Notification $notification): bool
    {
        return false;
    }
}
