<?php

namespace App\Policies;

use App\User;
use App\models\ExportProduct;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExportProductPolicy
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
     * Determine whether the user can view the exportProduct.
     *
     * @param  \App\User  $user
     * @param  \App\models\ExportProduct  $exportProduct
     * @return mixed
     */
    public function view(User $user, ExportProduct $exportProduct)
    {
        if ($user->isAdmin() || $user->isWare()) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can create exportProducts.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        if ($user->isAdmin() || $user->isWare()) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can update the exportProduct.
     *
     * @param  \App\User  $user
     * @param  \App\models\ExportProduct  $exportProduct
     * @return mixed
     */
    public function update(User $user, ExportProduct $exportProduct)
    {
        if ($user->isAdmin() || $user->isWare()) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the exportProduct.
     *
     * @param  \App\User  $user
     * @param  \App\models\ExportProduct  $exportProduct
     * @return mixed
     */
    public function delete(User $user, ExportProduct $exportProduct)
    {
        if ($user->isAdmin() || $user->isWare()) {
            return true;
        }
        return false;
    }
}
