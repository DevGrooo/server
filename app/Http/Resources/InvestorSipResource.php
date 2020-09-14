<?php

namespace App\Http\Resources;

use App\Models\InvestorSip;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvestorSipResource extends JsonResource {
    /**
     * @param Request $request
     * @return array
     */

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'distribution_agents' => $this->fund_distributor->name,
            'trading_account_number' => $this->investor->trading_account_number,
            'investor_name' => $this->investor->name,
            'id_code' => $this->investor->id_number,
            'id_issuing_date' => $this->investor->id_issuing_date,
            'fund_code' => $this->fund_certificate->code,
            'payment_type' => $this->payment_type,
            'payment_type_name' => $this->_getSipTypeName(),
            'date_of_sip_registraion' => $this->create_at,
            'periodic_amount' => $this->periodic_amount,
            'start_time' => $this->start_at,
            'mininum_period' => 1,
            'transaction_cycle' => 1,
            'description' => $this->fund_product->description,
            'status' => $this->status,
            'status_name' => $this->_getStatusName()
        ];
    }

    protected function _getStatusName() {
        $list_status = InvestorSip::getListStatus();
        return @$list_status[$this->status];
    }

    protected function _getSipTypeName() {
        $sipTypes = InvestorSip::getSipTypes();
        return @$sipTypes[$this->payment_type];
    }

}
