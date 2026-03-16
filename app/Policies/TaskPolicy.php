<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    protected function belongsToWorkspace(User $user, Task $task): bool
    {
        return $task->user_id === $user->workspaceOwner()->id;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Task $task): bool
    {
        return $this->belongsToWorkspace($user, $task)
            && ($user->isLeader() || $task->assigned_to_user_id === $user->id);
    }

    public function create(User $user): bool
    {
        return $user->isLeader();
    }

    public function update(User $user, Task $task): bool
    {
        return $user->isLeader() && $this->belongsToWorkspace($user, $task);
    }

    public function delete(User $user, Task $task): bool
    {
        return $user->isLeader() && $this->belongsToWorkspace($user, $task);
    }

    public function toggle(User $user, Task $task): bool
    {
        return $this->belongsToWorkspace($user, $task)
            && ($user->isLeader() || $task->assigned_to_user_id === $user->id);
    }

    public function restore(User $user, Task $task): bool
    {
        return false;
    }

    public function forceDelete(User $user, Task $task): bool
    {
        return false;
    }
}
