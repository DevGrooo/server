<?php

namespace App\Http\Resources;

use App\Models\TradingSession;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TradingSessionResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                          => $this->id,
            'previous_trading_session_id' => $this->previous_trading_session_id,
            'fund_company'                => $this->fund_company()->select('id', 'name')->getResults(),
            'trading_frequency'           => $this->trading_frequency()->select('id', 'name')->getResults(),
            'start_at'                    => $this->start_at,
            'end_at'                      => $this->end_at,
            'limit_order_at'              => $this->limit_order_at,
            'nav'                         => $this->nav,
            'status'                      => $this->status,
            'status_name'                 => $this->_getStatusName(),
            'created_at'                  => $this->created_at,
            'updated_at'                  => $this->updated_at
        ];
    }
    protected function _getStatusName() {
        $list_status = TradingSession::getListStatus();
        return @$list_status[$this->status];
    }
}
