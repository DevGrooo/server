<?php


namespace App\Http\Resources;


use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BankResource extends JsonResource
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
            'trade_name'  => $this->trade_name,
            'logo'        => $this->logo,
            'status'      => $this->status,
            'status_name' => $this->_getStatusName(),
            'created_at'  => $this->created_at,
            'updated_at'  => $this->updated_at
        ];
    }

    protected function _getStatusName() {
        $list_status = Bank::getListStatus();
        return @$list_status[$this->status];
    }
}

