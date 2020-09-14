<?php

namespace App\Http\Resources;

use App\Models\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BasicResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        $data = parent::toArray($request);
        if (!empty($data)) {
            $data[$this->_getNameFieldId()] = $data['id'];
        }
        return $data;
    }

    /**
     * @param Request $request
     * @param array $data
     * @param string $field_name
     * @param array $fields     
     */
    final protected function _setDataRelationship($request, &$data, $field_name, $fields = ['*']) {
        if (substr($field_name, -3) == '_id') {
            $method_name = substr($field_name, 0, strlen($field_name) - 3);
            if (method_exists($this->resource, $method_name)) {
                $re_model = $this->resource->$method_name()->select($fields)->getResults();
                if ($re_model) {
                    $re_data = $re_model->toArray();
                    foreach ($re_data as $key => $value) {
                        if (!isset($data[$method_name.'_'.$key])) {
                            $data[$method_name.'_'.$key] = $value;
                        }
                    }
                }
            }
        }
    }

    final protected function _getNameFieldId() {
        $class_name = get_class($this->resource);
        if (($position = strrpos($class_name, '\\')) !== false) {
            $class_name = substr($class_name, $position + 1);
        }
        return strtolower($class_name).'_id';
    }

    /**
     * @param Model $model
     */
    final protected function _getResourceClass($model) {
        $class_name = get_class($model);
        if (($position = strrpos($class_name, '\\')) !== false) {
            $class_name = substr($class_name, $position + 1);
        }
        $resource_class = '\App\Http\Resources\\'.$class_name.'Resource';
        if (class_exists($resource_class)) {
            return $resource_class;
        }
        return '\App\Http\Resources\BasicResource';
    }
}
