<?php

namespace App\Policies;

use App\Models\User;

class Policy
{
    /**
     * Check User Group
     * @param User $user
     * @param array $group_codes
     * @return boolean
     */
    protected function _inUserGroups($user, $group_codes)
    {
        return in_array($user->getUserGroupCode(), $group_codes);
    }
}
