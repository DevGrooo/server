<?php


namespace App\Http\Resources;


use App\Models\InvestorFundProduct;
use Illuminate\Http\Resources\Json\JsonResource;

class InvestorFundProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
          'id'                   => $this->id,
          'investor'             => $this->investor()->select('id', 'name')->getResults(),
          'fund_distributor'     => $this->fund_distributor()->select('id', 'name')->getResults(),
          'fund_company'         => $this->fund_company()->select('id', 'name')->getResults(),
          'fund_certificate'     => $this->fund_certificate()->select('id', 'name')->getResults(),
          'fund_product'         => $this->fund_product()->select('id', 'name')->getResults(),
          'balance'              => $this->balance,
          'balance_available'    => $this->balance_available,
          'balance_freezing'     => $this->balance_freezing,
          'currency'             => $this->currency,
          'status'               => $this->status,
          'status_name'          => $this->_getStatusName(),
          'created_at'           => $this->created_at,
          'updated_at'           => $this->updated_at
        ];
    }

    protected function _getStatusName()
    {
        $list_status = InvestorFundProduct::getListStatus();
        return @$list_status[$this->status];
    }
}
