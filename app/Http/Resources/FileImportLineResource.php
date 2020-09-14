<?php

namespace App\Http\Resources;

use App\Models\FileImportLine;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class FileImportLineResource extends BasicResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        $data = parent::toArray($request);
        $data['status_name'] = $this->_getStatusName();
        return $data;
    }

    protected function _getStatusName()
    {
        $list = FileImportLine::getListStatus();
        return @$list[$this->resource->status];
    }
}
