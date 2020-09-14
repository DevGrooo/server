<?php

namespace App\Http\Resources;

 use App\Models\TradingFrequency;
 use Illuminate\Http\Request;
 use Illuminate\Http\Resources\Json\JsonResource;

class TradingFrequencyResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'               => $this->id,
            'fund_company'     => $this->fund_company()->select('id', 'name')->getResults(),
            'type'             => $this->type,
            'name'             => $this->name,
            'wday'             => $this->wday,
            'mday'             => $this->mday,
            'cut_off_date'     => $this->cut_off_date,
            'cut_off_hour'     => $this->cut_off_hour,
            'cut_off_time'     => $this->cut_off_time,
            'status'           => $this->status,
            'status_name'      => $this->_getStatusName(),
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at
        ];
    }
    protected function _getStatusName() {
        $list_status = TradingFrequency::getListStatus();
        return @$list_status[$this->status];
    }


}
