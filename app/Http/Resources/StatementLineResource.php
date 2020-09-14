<?php

namespace App\Http\Resources;

use App\Models\StatementLine;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class StatementLineResource extends BasicResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        $data = parent::toArray($request);
        $data['data']['as_at_date'] = Date::excelToDateTimeObject($data['data']['as_at_date'])->format('d/m/Y');
        $data['data']['value_date'] = Date::excelToDateTimeObject($data['data']['value_date'])->format('d/m/Y');
        $data['status_name'] = $this->_getStatusName();
        return $data;
    }

    protected function _getStatusName()
    {
        $list = StatementLine::getListStatus();
        return @$list[$this->resource->status];
    }
}
