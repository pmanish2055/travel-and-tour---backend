<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PackageInclusion;
use Illuminate\Auth\Access\HandlesAuthorization;

class PackageInclusionPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PackageInclusion');
    }

    public function view(AuthUser $authUser, PackageInclusion $packageInclusion): bool
    {
        return $authUser->can('View:PackageInclusion');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PackageInclusion');
    }

    public function update(AuthUser $authUser, PackageInclusion $packageInclusion): bool
    {
        return $authUser->can('Update:PackageInclusion');
    }

    public function delete(AuthUser $authUser, PackageInclusion $packageInclusion): bool
    {
        return $authUser->can('Delete:PackageInclusion');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PackageInclusion');
    }

    public function restore(AuthUser $authUser, PackageInclusion $packageInclusion): bool
    {
        return $authUser->can('Restore:PackageInclusion');
    }

    public function forceDelete(AuthUser $authUser, PackageInclusion $packageInclusion): bool
    {
        return $authUser->can('ForceDelete:PackageInclusion');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PackageInclusion');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PackageInclusion');
    }

    public function replicate(AuthUser $authUser, PackageInclusion $packageInclusion): bool
    {
        return $authUser->can('Replicate:PackageInclusion');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PackageInclusion');
    }

}