<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;

class QueryExport implements FromQuery
{
    protected $query_builder = '';    

    function __construct($query_builder)
    {
        $this->query_builder = $query_builder;
    }

    public function query()
    {
        return $this->query_builder;
    }
}