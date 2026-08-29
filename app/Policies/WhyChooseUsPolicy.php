<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\WhyChooseUs;
use Illuminate\Auth\Access\HandlesAuthorization;

class WhyChooseUsPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:WhyChooseUs');
    }

    public function view(AuthUser $authUser, WhyChooseUs $whyChooseUs): bool
    {
        return $authUser->can('View:WhyChooseUs');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:WhyChooseUs');
    }

    public function update(AuthUser $authUser, WhyChooseUs $whyChooseUs): bool
    {
        return $authUser->can('Update:WhyChooseUs');
    }

    public function delete(AuthUser $authUser, WhyChooseUs $whyChooseUs): bool
    {
        return $authUser->can('Delete:WhyChooseUs');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:WhyChooseUs');
    }

    public function restore(AuthUser $authUser, WhyChooseUs $whyChooseUs): bool
    {
        return $authUser->can('Restore:WhyChooseUs');
    }

    public function forceDelete(AuthUser $authUser, WhyChooseUs $whyChooseUs): bool
    {
        return $authUser->can('ForceDelete:WhyChooseUs');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:WhyChooseUs');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:WhyChooseUs');
    }

    public function replicate(AuthUser $authUser, WhyChooseUs $whyChooseUs): bool
    {
        return $authUser->can('Replicate:WhyChooseUs');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:WhyChooseUs');
    }

}