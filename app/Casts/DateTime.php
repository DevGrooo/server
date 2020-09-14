<?php
namespace App\Casts;

use DateTime as BasicDateTime;
use DateTimeZone;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class DateTime implements CastsAttributes
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
        $date = BasicDateTime::createFromFormat('Y-m-d H:i:s', $value, new DateTimeZone(config('global.time_zone')));
        if ($date !== false) {
            return date('d/m/Y H:i:s', $date->getTimestamp());
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
        if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}\s+\d{1,2}:\d{1,2}:\d{1,2}$/', $value)) {
            $date = BasicDateTime::createFromFormat('d/m/Y H:i:s', $value, new DateTimeZone(config('global.time_zone')));
            if ($date !== false) {
                return date('Y-m-d H:i:s', $date->getTimestamp());
            }
        } elseif (preg_match('/^\d{4}-\d{1,2}-\d{1,2}\s+\d{1,2}:\d{1,2}:\d{1,2}$/', $value)) {
            $date = BasicDateTime::createFromFormat('Y-m-d H:i:s', $value, new DateTimeZone(config('global.time_zone')));
            if ($date !== false) {
                return date('Y-m-d H:i:s', $date->getTimestamp());
            }
        } elseif (is_numeric($value)) {
            return date('Y-m-d H:i:s', $value);
        }      
        return null;  
    }
}