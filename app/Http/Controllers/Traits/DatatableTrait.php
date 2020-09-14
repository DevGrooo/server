<?php

namespace App\Http\Controllers\Traits;

use Carbon\Carbon;
use Illuminate\Http\Request;

trait DatatableTrait
{
    protected $defaultPerpage = 25;

    /**
     * Query builder with datatable
     *
     * @param Request $request
     * @param Builder $model
     * @param array $searchable
     * @param array $fields
     * @return Pagination
     */
    protected function getData(Request $request, $model, $searchable = [], $isUnlimited = false, $fields = ['*'])
    {
        $params = json_decode($request->params);
        $query = $params->query;
        $limit = $params->limit;
        $orderBy = $params->orderBy ?? null;
        $ascending = $params->ascending;
        $byColumn = $params->byColumn;

        $data = $model->select($fields);

        if ($query) {
            $data = $byColumn == 1 ?
                $this->filterByColumn($data, $query) :
                $this->filter($data, $query, $searchable);
        }

        if ($orderBy) {
            $direction = $ascending == 1 ? 'ASC' : 'DESC';
            $data->orderBy($orderBy, $direction);
        }

        if ($isUnlimited) {
            return $data->get();
        }

        return $data->paginate(intval($limit) ?: $this->defaultPerpage);
    }

    /**
     * Filter by column
     *
     * @param Builder $data
     * @param array $queries
     * @return Builder
     */
    protected function filterByColumn($data, $queries)
    {
        $queries = json_decode($queries, true);
        return $data->where(function ($q) use ($queries) {
            foreach ($queries as $field => $query) {
                if (is_string($query)) {
                    $q->where($field, 'LIKE', "%{$query}%");
                } else {
                    $start = Carbon::createFromFormat('Y-m-d', $query['start'])->startOfDay();
                    $end = Carbon::createFromFormat('Y-m-d', $query['end'])->endOfDay();

                    $q->whereBetween($field, [$start, $end]);
                }
            }
        });
    }

    /**
     * Filter by string
     *
     * @param Builder $data
     * @param String $query
     * @param array $fields
     * @return Builder
     */
    protected function filter($data, $query, $fields)
    {
        return $data->where(function ($q) use ($query, $fields) {
            foreach ($fields as $index => $field) {
                $method = $index ? 'orWhere' : 'where';
                $q->{$method}($field, 'LIKE', "%{$query}%");
            }
        });
    }
}
