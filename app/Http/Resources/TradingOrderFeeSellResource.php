<?php


namespace App\Http\Resources;


use App\Models\TradingOrderFeeSell;
use Illuminate\Http\Resources\Json\JsonResource;

class TradingOrderFeeSellResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'               => $this->id,
            'fund_company'     => $this->fund_company()->select('id', 'name')->getResults(),
            'fund_certificate' => $this->fund_certificate()->select('id', 'name')->getResults(),
            'fund_product'     => $this->fund_product()->select('id', 'name')->getResults(),
            'start_at'         => $this->start_at,
            'end_at'           => $this->end_at,
            'holding_period'   => $this->holding_period,
            'fee_amount'       => $this->fee_amount,
            'fee_percent'      => $this->fee_percent,
            'status'           => $this->status,
            'status_name'      => $this->_getStatusName(),
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,
        ];
    }

    protected function _getStatusName()
    {
        $list_status = TradingOrderFeeSell::getListStatus();
        return @$list_status[$this->status];
    }
}
