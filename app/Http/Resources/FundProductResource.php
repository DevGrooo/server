<?php

namespace App\Http\Resources;

use App\Models\FundProduct;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FundProductResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                   => $this->id,
            'fund_certificate'     => $this->fund_certificate()->select('id', 'name')->getResults(),
            'fund_product_type'    => $this->fund_product_type()->select('id', 'name')->getResults(),
            'fund_company'         => $this->fund_company()->select('id', 'name')->getResults(),
            'name'                 => $this->name,
            'code'                 => $this->code,
            'description'          => $this->description,
            'status'               => $this->status,
            'status_name'          => $this->_getStatusName(),
            'created_at'           => $this->created_at,
            'updated_at'           => $this->updated_at
        ];
    }
    protected function _getStatusName() {
        $list_status = FundProduct::getListStatus();
        return @$list_status[$this->status];
    }


}
