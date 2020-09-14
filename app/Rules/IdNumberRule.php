<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class IdNumberRule implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (preg_match('/^[A-Za-z0-9]{6,20}$/', $value)) {
            return true;
        }
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('Số xác thực không hợp lệ');
    }
}
