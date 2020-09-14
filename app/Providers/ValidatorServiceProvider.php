<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class ValidatorServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        
    }

    public function boot()
    {
        Validator::extend('integer_array', function($attribute, $value, $parameters, $validator) {
            return $this->_validateIntegerArray($attribute, $value, $parameters, $validator);
        });
        Validator::extend('row_exists', function($attribute, $value, $parameters, $validator) {
            return $this->_validateRowExists($attribute, $value, $parameters, $validator);
        });
    }

    /**
     * @param string $attribute
     * @param mixed $value
     * @param mixed $parameters
     * @param mixed $validator
     * @return boolean
     */
    private function _validateIntegerArray($attribute, $value, $parameters, $validator)
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
     * @param string $attribute
     * @param mixed $value
     * @param mixed $parameters
     * @param mixed $validator
     * @return boolean
     */
    private function _validateRowExists($attribute, $value, $parameters, $validator)
    {
        if (intval($value) > 0) {
            $table_name = $this->_getTableNameByForeignKey($attribute, $parameters);
            if ($table_name !== false) {
                $row = DB::table($table_name)->find($value);
                if ($row) {
                    return true;
                }
            }
        }
        return false;
    }

    private function _getTableNameByForeignKey($field_name, $parameters)
    {
        if (preg_match('/^(.+)_id$/', $field_name, $matchs)) {
            if (!empty($parameters)) {
                $table_name = $parameters[0];
            } else {
                $table_name = $matchs[1];
            }            
            if (substr($table_name, -1) != 'y') {
                $table_name.= 's';
            }
            return $table_name;
            //return $this->_getTableMapping($table_name);
        }
        return false;
    }

    private function _getTableMapping($table_name)
    {
        $mapping = config('validator.table_mapping');
        if (array_key_exists($table_name, $mapping)) {
            return $mapping[$table_name];
        }
        foreach ($mapping as $reg => $table) {
            if (preg_match($reg, $table_name)) {
                return $table;
            }
        }
        return $table_name;
    }
}
