<?php


namespace App\Http\Resources;
use App\Models\FundProductType;
use Illuminate\Http\Resources\Json\JsonResource;

class FundProductTypeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'code'        => $this->code,
            'description' => $this->description,
            'status'      => $this->status,
            'status_name' => $this->_getStatusName(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
    protected function _getStatusName() {
        $list_status = FundProductType::getListStatus();
        return @$list_status[$this->status];
    }
}

