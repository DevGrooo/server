<?php

namespace App\Http\Controllers\Traits;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;
use Maatwebsite\Excel\Facades\Excel;

trait TableTrait
{
    /**
     * Query builder with datatable
     *
     * @param Request $request
     * @param Builder $model
     * @param array $searchable
     * @param array $fields
     * @return Pagination
     */
    protected function _getAndValidateParamsQuery(Request $request)
    {
        $inputs = $this->validate($request, [
            'params.query' => 'array',
            'params.limit' => 'integer',
            'params.page' => 'integer',
            'params.orderBy' => 'string|max:100',
            'params.ascending' => 'integer',
            'params.byColumn' => 'integer',
        ]);
        $params = isset($inputs['params']) ? $inputs['params'] : $this->_getParamsQueryDefault();
        return $params;
    }

    protected function _getParamsQueryDefault()
    {
        return array(
            'query' => '',
            'limit' => 10,
            'page' => 1,
            'orderBy' => '',
            'ascending' => 'ASC',
            'byColumn' => 0
        );
    }

    /**
     * Query builder with datatable
     *
     * @param array $params
     * @param Builder $builder
     * @param string $class_resource
     * @param array $fields
     * @return Pagination
     */

    protected function getQuery($params, $builder, $fields = ['*'], $flagOrderBy = false)
    {
        $query = $params['query'];
        $orderBy = @$params['orderBy'];
        $ascending = $params['ascending'];
        $byColumn = $params['byColumn'];

        $query_builder = $builder->select($fields);

        if ($query && $byColumn == 1) {
            $query_builder = $this->filterByColumn($query_builder, $query);
        }

        if ($orderBy && !$flagOrderBy) {
            $direction = $ascending == 1 ? 'ASC' : 'DESC';
            $query_builder->orderBy($orderBy, $direction);
        }

        return $query_builder;
    }

    /**
     * Query builder with datatable
     *
     * @param Request $request
     * @param Builder $builder
     * @param string $class_resource
     * @param array $fields
     * @return Pagination
     */
    protected function getData(Request $request, $builder, $class_resource = '', $fields = ['*'], $isUnlimited = false, $flagOrderBy = false)
    {
        $data = [];
        $count = 0;

        $params = $this->_getAndValidateParamsQuery($request);
        $limit = $params['limit'];
        $page = $params['page'];

        $query_builder = $this->getQuery($params, $builder, $fields, $flagOrderBy);
        // echo $query_builder->toSql();  
        // var_dump($query_builder->getBindings());

        if ($isUnlimited) {
            $data = $query_builder->get();
            $count = count($data);
        } else {
            $count = $query_builder->count();
            $offset = ($page - 1) * $limit;
            $data = $query_builder->offset($offset)->limit($limit)->get();
        }
        if ($class_resource !== '') {
            $data = $class_resource::collection($data);
        }
        return array(
            'data' => $data,
            'count' => $count,
        );
    }

    protected function exportData(Request $request, $builder, $class_export, $fields = ['*'], $file_type = 'Xlsx')
    {
        $params = $this->_getAndValidateParamsQuery($request);
        $query_builder = $this->getQuery($params, $builder, $fields);
        $data = Excel::raw(new $class_export($query_builder, $request), $file_type);
        return 'data:@file/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($data);
    }

    /**
     * Filter by column
     *
     * @param Builder $query_builder
     * @param array $queries
     * @return Builder
     */
    protected function filterByColumn($query_builder, $queries)
    {
        return $query_builder->where(function ($q) use ($queries) {
            foreach ($queries as $field => $query) {
                if (is_array($query)) {
                    if (is_array(@$query['value'])) {
                        if (strtolower($query['compare']) == 'in') {
                            $q->whereIn($field, $query['value']);
                        } elseif (strtolower($query['compare']) == 'not in') {
                            $q->whereNotIn($field, $query['value']);
                        }
                    } elseif (trim(@$query['value']) !== '') {
                        if ($this->_isFieldDate($field)) {
                            if ($query['compare'] == '>=' || $query['compare'] == '>') {
                                $value = Carbon::createFromFormat('Y-m-d', $query['value'])->startOfDay();
                            } else {
                                $value = Carbon::createFromFormat('Y-m-d', $query['value'])->endOfDay();
                            }
                            $q->where($field, $query['compare'], $value);
                        } else {
                            if (strtoupper($query['compare']) == 'LIKE') {
                                $q->where($field, $query['compare'], "%".$query['value']."%");
                            } else {
                                $q->where($field, $query['compare'], $query['value']);
                            }
                        }
                    }
                } else {
                    if (trim($query) != '') {
                        if ($this->_isFieldID($field)) {
                            $q->where($field, '=', $query);
                        } else {
                            $q->where($field, 'LIKE', "%".$query."%");
                        }
                    }
                }
            }
        });
    }

    protected function _isFieldDate($field) {
        return substr($field, -3) === '_at';
    }

    protected function _isFieldID($field) {
        return substr($field, -3) === '_id';
    }
}
