<?php
namespace App\Casts;

use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class Date implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return array
     */
    public function get($model, $key, $value, $attributes)
    {
        $date = DateTime::createFromFormat('Y-m-d H:i:s', $value, new DateTimeZone(config('global.time_zone')));
        if ($date !== false) {
            return date('d/m/Y', $date->getTimestamp());
        }
        return '';
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  array  $value
     * @param  array  $attributes
     * @return string
     */
    public function set($model, $key, $value, $attributes)
    {
        if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $value)) {            
            $date = DateTime::createFromFormat('d/m/Y H:i:s', $value.' 00:00:00', new DateTimeZone(config('global.time_zone')));
            if ($date !== false) {
                return date('Y-m-d H:i:s', $date->getTimestamp());
            }
        } elseif (preg_match('/^\d{4}-\d{1,2}-\d{1,2}$/', $value)) {            
            $date = DateTime::createFromFormat('Y-m-d H:i:s', $value.' 00:00:00', new DateTimeZone(config('global.time_zone')));
            if ($date !== false) {
                return date('Y-m-d H:i:s', $date->getTimestamp());
            }
        }
        return null;
    }
}