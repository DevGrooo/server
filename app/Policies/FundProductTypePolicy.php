<?php


namespace App\Policies;


use App\Models\User;

class FundProductTypePolicy extends Policy
{
    /**
     * Determine whether the user can create fund product type.
     *
     * @param User  $user
     * @param array $param
     * @return boolean
     */
    public function create(User $user, array $param)
    {
        return true;
    }

    /**
     * Determine whether the user can update fund product type.
     *
     * @param User  $user
     * @param array $param
     * @return boolean
     */
    public function update(User $user, array $param)
    {
        return true;
    }

    /**
     * Determine whether the user can view fund product type.
     *
     * @param User  $user
     * @param array $param
     * @return boolean
     */
    public function view(User $user, array $param)
    {
        return true;
    }

    /**
     * Determine whether the user can lock fund product type.
     *
     * @param User  $user
     * @param array $param
     * @return boolean
     */
    public function lock(User $user, array $param)
    {
        return true;
    }

    /**
     * Determine whether the user can active fund product type.
     *
     * @param User  $user
     * @param array $post
     * @return boolean
     */
    public function active(User $user, array $post)
    {
        return true;
    }
}
