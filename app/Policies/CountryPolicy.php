<?php
namespace App\Policies;

use App\Models\Country;

class CountryPolicy  extends Policy
{


    /**
     * Determine whether the user can view the user.
     *
     * @param  User  $user
     * @param  array  $params
     * @return boolean
     */
    public function view(Country $user, array $params)
    {
        return true;
    }

    /**
     * Determine whether the user can create users.
     *
     * @param  User  $user
     * @return boolean
     */
    public function create(Country $user)
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
    public function update(Country $user, $post)
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
    public function delete(Country $user, Country $post)
    {
        //
    }
}
;
