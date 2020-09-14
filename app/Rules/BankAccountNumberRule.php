<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class BankAccountNumberRule implements Rule
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
        if (preg_match('/^[0-9\-–\s]{6,30}$/', $value)) {
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
        return trans('Số tài khoản ngân hàng không hợp lệ');
    }
}
