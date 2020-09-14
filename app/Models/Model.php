<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model as BasicModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class Model extends BasicModel
{
    protected $rules = [];
    protected $rule_messages = [];

    public static function boot()
    {
        parent::boot();
        self::saving(function (Model $model) {
            // $model->validate();
        });
    }

    public function validate()
    {
        if ($this->exists) {
            $attributes = $this->_getChangeAttributes();
        } else {
            $attributes = $this->getAttributes();
        }
        if (!empty($attributes)) {
            $validator = Validator::make($attributes, $this->getRules($attributes), $this->getRuleMessages());
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }
        }
    }

    public function getRules($change_attributes = [])
    {
        if (empty($change_attributes)) {
            $change_attributes = $this->getAttributes();
        }
        if ($this->_hasAttributeRefId($change_attributes, $ref_type)) {
            $this->_setValidatorRowExistsForRefId($ref_type);
        }
        $result = array();
        foreach ($change_attributes as $key => $value) {
            if (array_key_exists($key, $this->rules)) {
                $result[$key] = $this->rules[$key];
            }
        }
        return $result;
    }

    public function getRuleMessages()
    {
        return $this->rule_messages;
    }

    public function setRuleMessages(array $rule_messages)
    {
        return $this->rule_messages = $rule_messages;
    }

    public function addRule($attribute, $rule)
    {
        $old_rules = explode('|', $this->rules[$attribute]);
        $new_rules = $old_rules;
        $new_rules[] = $rule;
        $this->rules[$attribute] = implode('|', $new_rules);
    }

    public function removeRule($attribute, $rule_name)
    {
        $old_rules = explode('|', $this->rules[$attribute]);
        $new_rules = [];
        foreach ($old_rules as $rule) {
            if (strpos($rule, $rule_name) !== 0) {
                $new_rules[] = $rule;
            }
        }
        $this->rules[$attribute] = implode('|', $new_rules);
    }

    protected function _getChangeAttributes()
    {
        $result = [];
        if ($this->getAttributes()) {
            $old_values = $this->getOriginal();
            foreach ($this->getAttributes() as $key => $value) {
                if (@$old_values[$key] != $value) {
                    $result[$key] = $value;
                }
            }
        }
        return $result;
    }

    protected function _hasAttributeRefId($attributes, &$ref_type = '')
    {
        if (!empty($attributes)) {
            foreach ($attributes as $key => $attribute) {
                if ($key == 'ref_id' && isset($attributes['ref_type']) && trim($attributes['ref_type']) != '') {
                    $ref_type = $attributes['ref_type'];
                    return true;
                }
            }
        }
        return false;
    }

    protected function _setValidatorRowExistsForRefId($ref_type)
    {
        $this->removeRule('ref_id', 'row_exists');
        $this->addRule('ref_id', "row_exists:$ref_type");
    }

    public function loadParams($params) {
        $keys = $this->fillable;        
        foreach ($params as $key => $value) {
            if (in_array($key, $keys)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * Update Raw query PDO
     * @param string $query_update
     * @param array $bindings
     * @return integer
     */
    public static function updateRawQuery($query_update, $bindings)
    {
        $params = $bindings;
        $query = $query_update;
        if (preg_match_all('/\:([a-zA-Z0-9_\-]+)/', $query_update, $matchs)) {
            $finds = $matchs[0];
            $keys = $matchs[1];
            foreach ($finds as $index => $find) {
                $replace = '?';
                if (is_array($bindings[$keys[$index]])) {
                    $replace = substr(str_repeat(',?', count($bindings[$keys[$index]])), 1);
                }
                $query = str_replace($find, $replace, $query);           
            }
            $params = array();
            foreach ($keys as $index => $key) {
                if (is_array($bindings[$key])) {
                    foreach ($bindings[$key] as $value) {
                        $params[] = $value;
                    }                    
                } else {
                    $params[] = $bindings[$key];
                }                
            }
        }
        return DB::update($query, $params);
    }

    public static function getArrayForSelectBox($models, $field_value = '', $field_title = '')
    {
        $result = [];
        if ($models && !empty($models)) {
            foreach ($models as $key => $row) {
                if (is_a($row, '\Illuminate\Database\Eloquent\Model')) {
                    $result[] = ['title' => $row->$field_title, 'value' => $row->$field_value];
                } else {
                    $result[] = ['title' => $row, 'value' => $key];
                }
            }
        }
        return $result;
    }
}
