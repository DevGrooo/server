<?php


namespace App\Http\Resources;


use App\Models\ReferralBank;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReferralBankResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */

    public function toArray($request)
    {
        return [
            'id'           => $this->id,
            'fund_company' => $this->fund_company()->select('id', 'name')->getResults(),
            'name'        => $this->name,
            'trade_name'  => $this->trade_name,
            'logo'        => $this->logo,
            'status'      => $this->status,
            'status_name' => $this->_getStatusName(),
            'created_at'  => $this->created_at,
            'updated_at'  => $this->updated_at
        ];
    }

    protected function _getStatusName() {
        $list_status = ReferralBank::getListStatus();
        return @$list_status[$this->status];
    }
}
