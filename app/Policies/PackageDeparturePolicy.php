<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PackageDeparture;
use Illuminate\Auth\Access\HandlesAuthorization;

class PackageDeparturePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PackageDeparture');
    }

    public function view(AuthUser $authUser, PackageDeparture $packageDeparture): bool
    {
        return $authUser->can('View:PackageDeparture');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PackageDeparture');
    }

    public function update(AuthUser $authUser, PackageDeparture $packageDeparture): bool
    {
        return $authUser->can('Update:PackageDeparture');
    }

    public function delete(AuthUser $authUser, PackageDeparture $packageDeparture): bool
    {
        return $authUser->can('Delete:PackageDeparture');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PackageDeparture');
    }

    public function restore(AuthUser $authUser, PackageDeparture $packageDeparture): bool
    {
        return $authUser->can('Restore:PackageDeparture');
    }

    public function forceDelete(AuthUser $authUser, PackageDeparture $packageDeparture): bool
    {
        return $authUser->can('ForceDelete:PackageDeparture');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PackageDeparture');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PackageDeparture');
    }

    public function replicate(AuthUser $authUser, PackageDeparture $packageDeparture): bool
    {
        return $authUser->can('Replicate:PackageDeparture');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PackageDeparture');
    }

}