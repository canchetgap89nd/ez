<?php

namespace App\Policies;

use App\User;
use App\models\ImportProduct;
use Illuminate\Auth\Access\HandlesAuthorization;

class ImportProductPolicy
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
     * Determine whether the user can view the importProduct.
     *
     * @param  \App\User  $user
     * @param  \App\models\ImportProduct  $importProduct
     * @return mixed
     */
    public function view(User $user, ImportProduct $importProduct)
    {
        if ($user->isAdmin() || $user->isWare()) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can create importProducts.
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
     * Determine whether the user can update the importProduct.
     *
     * @param  \App\User  $user
     * @param  \App\models\ImportProduct  $importProduct
     * @return mixed
     */
    public function update(User $user, ImportProduct $importProduct)
    {
        if ($user->isAdmin() || $user->isWare()) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the importProduct.
     *
     * @param  \App\User  $user
     * @param  \App\models\ImportProduct  $importProduct
     * @return mixed
     */
    public function delete(User $user, ImportProduct $importProduct)
    {
        if ($user->isAdmin() || $user->isWare()) {
            return true;
        }
        return false;
    }
}
