<?php

namespace App\Rules;

use App\Models\Investor;
use Illuminate\Contracts\Validation\Rule;

class TradingAccountNumberRule implements Rule
{
    protected $fund_distributor_code;
    protected $zone_type;

    function __construct($fund_distributor_code = '', $zone_type = '')
    {
        $this->fund_distributor_code = $fund_distributor_code;
        $this->zone_type = $zone_type;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (preg_match('/^([A-Z0-9]{3})([PCF])([A-Z0-9]{6})$/u', $value, $matches)) {
            if ($this->fund_distributor_code != '' && $matches[1] != $this->fund_distributor_code) {
                return false;
            }
            if ($this->zone_type == Investor::ZONE_TYPE_INTERNAL && $matches[2] == 'F') {
                return false;
            }
            if ($this->zone_type == Investor::ZONE_TYPE_EXTERNAL && $matches[2] != 'F') {
                return false;
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
        return trans('Số tài khoản giao dịch không hợp lệ');
    }
}
