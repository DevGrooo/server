<?php

namespace App\Http\Resources;

use App\Models\Cashin;
use Illuminate\Http\Request;

class CashinResource extends BasicResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        $data = parent::toArray($request);
        $this->_setDataRelationship($request, $data, 'fund_distributor_id', ['id', 'code', 'name']);
        $this->_setDataRelationship($request, $data, 'fund_distributor_bank_account_id', ['id', 'account_holder', 'account_number', 'branch']);
        $this->_setDataRelationship($request, $data, 'fund_product_type_id', ['id', 'code', 'name']);
        $this->_setDataRelationship($request, $data, 'fund_certificate_id', ['id', 'code', 'name']);
        $this->_setDataRelationship($request, $data, 'fund_product_id', ['id', 'code', 'name']);
        $this->_setDataRelationship($request, $data, 'investor_id', ['id', 'trading_account_number', 'name']);
        $this->_setDataRelationship($request, $data, 'supervising_bank_id', ['id', 'trade_name', 'name']);
        $this->_setDataRelationship($request, $data, 'statement_id', ['id', 'file_name']);
        $data['status_name'] = $this->_getStatusName();
        $data['actions'] = $this->_getActions($this->resource);
        return $data;
    }

    protected function _getStatusName()
    {
        $list = Cashin::getListStatus();
        return @$list[$this->resource->status];
    }

    /**
     * @param Cashin $model
     */
    protected function _getActions($model) {
        $actions = [];
        if ($model->status == Cashin::STATUS_NEW) {
            $actions[] = 'cancel';
            $actions[] = 'paid';
            if (intval($model->investor_id) == 0) {
                $actions[] = 'update-investor';
            }
        } elseif ($model->status == Cashin::STATUS_PAID) {
            if (intval($model->investor_id) == 0) {
                $actions[] = 'update-investor';
            }
        }
        return $actions;
    }
}
