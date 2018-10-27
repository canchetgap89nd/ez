<?php

namespace App\Policies;

use App\User;
use App\models\Category;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoryPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->isAdmin()) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can view the category.
     *
     * @param  \App\User  $user
     * @param  \App\models\Category  $category
     * @return mixed
     */
    public function view(User $user, Category $category)
    {
        if ($user->isManager() || $user->isWare()) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can create categories.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        if ($user->isManager() || $user->isWare()) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can update the category.
     *
     * @param  \App\User  $user
     * @param  \App\models\Category  $category
     * @return mixed
     */
    public function update(User $user, Category $category)
    {
        if ($user->isManager() || $user->isWare()) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the category.
     *
     * @param  \App\User  $user
     * @param  \App\models\Category  $category
     * @return mixed
     */
    public function delete(User $user, Category $category)
    {
        if ($user->isManager() || $user->isWare()) {
            return true;
        }
        return false;
    }
}
