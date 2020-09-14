<?php

namespace App\Http\Resources;

use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'code'        => $this->code,
            'status'      => $this->status,
            'status_name' => $this->_getStatusName(),
            'created_at'  => $this->created_at,
            'updated_at'  => $this->updated_at
        ];
    }
    protected function _getStatusName() {
        $list_status = Country::getListStatus();
        return @$list_status[$this->status];
    }


}
