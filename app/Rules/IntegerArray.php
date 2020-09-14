<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class IntegerArray implements Rule
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
        if (is_array($value) && !empty($value)) {
            foreach($value as $item) {
                if (!is_integer($item)) {
                    return false;
                }
            }
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
        return trans('validation.integer_array');
    }
}
