<?php

namespace App\Policies;

use App\Models\Folder;
use App\Models\User;

class FolderPolicy
{
    protected function belongsToWorkspace(User $user, Folder $folder): bool
    {
        return $folder->user_id === $user->workspaceOwner()->id;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Folder $folder): bool
    {
        return $this->belongsToWorkspace($user, $folder);
    }

    public function create(User $user): bool
    {
        return $user->isLeader();
    }

    public function update(User $user, Folder $folder): bool
    {
        return $user->isLeader() && $this->belongsToWorkspace($user, $folder);
    }

    public function delete(User $user, Folder $folder): bool
    {
        return $user->isLeader() && $this->belongsToWorkspace($user, $folder);
    }

    public function restore(User $user, Folder $folder): bool
    {
        return false;
    }

    public function forceDelete(User $user, Folder $folder): bool
    {
        return false;
    }
}
