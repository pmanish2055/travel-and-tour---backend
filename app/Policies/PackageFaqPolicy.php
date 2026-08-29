<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PackageFaq;
use Illuminate\Auth\Access\HandlesAuthorization;

class PackageFaqPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PackageFaq');
    }

    public function view(AuthUser $authUser, PackageFaq $packageFaq): bool
    {
        return $authUser->can('View:PackageFaq');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PackageFaq');
    }

    public function update(AuthUser $authUser, PackageFaq $packageFaq): bool
    {
        return $authUser->can('Update:PackageFaq');
    }

    public function delete(AuthUser $authUser, PackageFaq $packageFaq): bool
    {
        return $authUser->can('Delete:PackageFaq');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PackageFaq');
    }

    public function restore(AuthUser $authUser, PackageFaq $packageFaq): bool
    {
        return $authUser->can('Restore:PackageFaq');
    }

    public function forceDelete(AuthUser $authUser, PackageFaq $packageFaq): bool
    {
        return $authUser->can('ForceDelete:PackageFaq');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PackageFaq');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PackageFaq');
    }

    public function replicate(AuthUser $authUser, PackageFaq $packageFaq): bool
    {
        return $authUser->can('Replicate:PackageFaq');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PackageFaq');
    }

}