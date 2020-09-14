<?php

namespace App\Policies;

use App\Models\User;

class UserGroupPolicy extends Policy
{

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
//        dd($post);
        return true;
    }
}
