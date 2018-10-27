<?php

namespace App\Policies;

use App\User;
use App\models\Product;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
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
     * Determine whether the user can view the product.
     *
     * @param  \App\User  $user
     * @param  \App\models\Product  $product
     * @return mixed
     */
    public function view(User $user, Product $product)
    {
        if ($user->isAdmin() || $user->isWare()) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can create products.
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
     * Determine whether the user can update the product.
     *
     * @param  \App\User  $user
     * @param  \App\models\Product  $product
     * @return mixed
     */
    public function update(User $user, Product $product)
    {
        if ($user->isAdmin() || $user->isWare()) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the product.
     *
     * @param  \App\User  $user
     * @param  \App\models\Product  $product
     * @return mixed
     */
    public function delete(User $user, Product $product)
    {
        if ($user->isAdmin() || $user->isWare()) {
            return true;
        }
        return false;
    }

    public function exportFile(User $user)
    {
        return false;
    }
}
