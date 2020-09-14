<?php
namespace App\Policies;

use App\Models\User;

class FundProductPolicy extends Policy
{


    /**
     * Determine whether the user can view the user.
     *
     * @param  User  $user
     * @param  array  $params
     * @return boolean
     */
    public function view(User $user, array $params)
    {
        return true;
    }

    /**
     * Determine whether the user can create users.
     *
     * @param  User  $user
     * @return boolean
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the user.
     *
     * @param  User  $user
     * @param  User  $post
     * @return boolean
     */
    public function update(User $user, $post)
    {
        return true;
    }

    /**
     * Determine whether the user can update the user.
     *
     * @param  User  $user
     * @param  $post
     * @return boolean
     */
    public function updateRole(User $user,$post)
    {
        return true;
    }

    /**
     * Determine whether the user can delete the user.
     *
     * @param  User  $user
     * @param  User  $post
     * @return boolean
     */
    public function delete(User $user, User $post)
    {
        //
    }
}
;
