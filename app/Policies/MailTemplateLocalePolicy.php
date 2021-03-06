<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserGroup;

class MailTemplateLocalePolicy extends Policy
{
    /**
     * Determine whether the user can create users.
     *
     * @param User  $user
     * @param array $post
     * @return boolean
     */
    public function create(User $user, $post)
    {        
        return true;
    }

    /**
     * Determine whether the user can update the user.
     *
     * @param  User  $user
     * @param  array  $post
     * @return boolean
     */
    public function update(User $user, $post)
    {
        return $this->_inUserGroups($user, [
            UserGroup::GROUP_ADMIN
        ]);
    }
}
