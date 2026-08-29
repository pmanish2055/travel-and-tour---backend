<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\CustomTrip;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomTripPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CustomTrip');
    }

    public function view(AuthUser $authUser, CustomTrip $customTrip): bool
    {
        return $authUser->can('View:CustomTrip');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CustomTrip');
    }

    public function update(AuthUser $authUser, CustomTrip $customTrip): bool
    {
        return $authUser->can('Update:CustomTrip');
    }

    public function delete(AuthUser $authUser, CustomTrip $customTrip): bool
    {
        return $authUser->can('Delete:CustomTrip');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:CustomTrip');
    }

    public function restore(AuthUser $authUser, CustomTrip $customTrip): bool
    {
        return $authUser->can('Restore:CustomTrip');
    }

    public function forceDelete(AuthUser $authUser, CustomTrip $customTrip): bool
    {
        return $authUser->can('ForceDelete:CustomTrip');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CustomTrip');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CustomTrip');
    }

    public function replicate(AuthUser $authUser, CustomTrip $customTrip): bool
    {
        return $authUser->can('Replicate:CustomTrip');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CustomTrip');
    }

}