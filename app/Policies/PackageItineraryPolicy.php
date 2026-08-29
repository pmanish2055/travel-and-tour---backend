<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PackageItinerary;
use Illuminate\Auth\Access\HandlesAuthorization;

class PackageItineraryPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PackageItinerary');
    }

    public function view(AuthUser $authUser, PackageItinerary $packageItinerary): bool
    {
        return $authUser->can('View:PackageItinerary');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PackageItinerary');
    }

    public function update(AuthUser $authUser, PackageItinerary $packageItinerary): bool
    {
        return $authUser->can('Update:PackageItinerary');
    }

    public function delete(AuthUser $authUser, PackageItinerary $packageItinerary): bool
    {
        return $authUser->can('Delete:PackageItinerary');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PackageItinerary');
    }

    public function restore(AuthUser $authUser, PackageItinerary $packageItinerary): bool
    {
        return $authUser->can('Restore:PackageItinerary');
    }

    public function forceDelete(AuthUser $authUser, PackageItinerary $packageItinerary): bool
    {
        return $authUser->can('ForceDelete:PackageItinerary');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PackageItinerary');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PackageItinerary');
    }

    public function replicate(AuthUser $authUser, PackageItinerary $packageItinerary): bool
    {
        return $authUser->can('Replicate:PackageItinerary');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PackageItinerary');
    }

}