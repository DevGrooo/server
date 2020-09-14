<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserGroup;

class InvestorPolicy extends Policy
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
     * @param User  $user
     * @param array $post
     * @return boolean
     */
    public function create(User $user, $post)
    {
        return $this->_inUserGroups($user, [
            UserGroup::GROUP_ADMIN, 
            UserGroup::GROUP_FUND_COMPANY, 
            UserGroup::GROUP_FUND_DISTRIBUTOR, 
            UserGroup::GROUP_FUND_DISTRIBUTOR_STAFF
        ]);
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
        return true;
    }

    /**
     * Determine whether the user can update the user.
     *
     * @param  User  $user
     * @param  array  $post
     * @return boolean
     */
    public function closed(User $user, $post)
    {
        return true;
    }
}
