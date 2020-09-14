<?php

namespace App\Policies;

class FundCertificatePolicy extends Policy
{
    /**
     * Determine whether the user can create fund certificate.
     *
     * @param User $user
     * @param $data
     * @return boolean
     */
    public function create(User $user, $data)
    {
        return true;
    }

    /**
     * Determine whether the user can update the fund certificate.
     *
     * @param  User $user
     * @param  $data
     * @return boolean
     */
    public function update(User $user, $data)
    {
        return true;
    }

    /**
     * Determine whether the user can view the fund certificate.
     *
     * @param  User  $user
     * @param  array  $data
     * @return boolean
     */
    public function view(User $user, array $params)
    {
        return true;
    }
}
