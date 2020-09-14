<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class VNDateRule implements Rule
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
        if (preg_match('/\d{1,2}\/\d{1,2}\/\d{4}/', $value)) {
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
        return trans('Ngày không hợp lệ');
    }
}
