<?php

namespace App\Http\Resources;

use App\Models\FundCertificate;
use App\Models\Investor;
use App\Models\InvestorBankAccount;
use Illuminate\Http\Request;

class InvestorResource extends BasicResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        $data = parent::toArray($request);
        $this->_setDataRelationship($request, $data, 'fund_distributor_id', ['id', 'code', 'name']);
        $this->_setDataRelationship($request, $data, 'id_type_id', ['id', 'code', 'name']);
        $this->_setDataRelationship($request, $data, 're_country_id', ['id', 'code', 'name']);
        $this->_setDataRelationship($request, $data, 'au_country_id', ['id', 'code', 'name']);
        $this->_setInvestorBankAccountDefault($data);
        $data['scale_type_name'] = $this->_getScaleTypeName();
        $data['gender_name'] = $this->_getGenderName();
        $data['status_name'] = $this->_getStatusName();
        $data['vsd_status_name'] = $this->_getVsdStatusName();
        $data['actions'] = $this->_getActions($this->resource);
        return $data;
    }

    /**
     * @param Investor $model
     */
    protected function _getActions($model) {
        $actions = [];
        if ($model->status == Investor::STATUS_NEW) {
            $actions[] = 'import-vsd';
            $actions[] = 'update';
            $actions[] = 'cancel';
        } elseif ($model->status == Investor::STATUS_IMPORT_VSD) {            
            $actions[] = 'closed';
            if ($model->vsd_status == Investor::VSD_STATUS_NEW) {
                $actions[] = 'active';
                $actions[] = 'reject';
            } elseif ($model->vsd_status == Investor::VSD_STATUS_REJECT) {
                $actions[] = 'active';
                // $actions[] = 'send-mail';
            } elseif ($model->vsd_status == Investor::VSD_STATUS_ACTIVE) {
                // $actions[] = 'send-mail';
            }
        } elseif ($model->status == Investor::STATUS_CLOSED) {            
            $actions[] = 'reopen';
        }
        return $actions;
    }

    protected function _getStatusName()
    {
        $list = Investor::getListStatus();
        return @$list[$this->resource->status];
    }

    protected function _getVsdStatusName()
    {
        $list = Investor::getListVsdStatus();
        return @$list[$this->resource->vsd_status];
    }

    protected function _getScaleTypeName()
    {
        $list = Investor::getListScaleTypes();
        return @$list[$this->resource->scale_type];
    }

    protected function _getGenderName()
    {
        $list = Investor::getListGenders();
        return @$list[$this->resource->gender];
    }

    protected function _setInvestorBankAccountDefault(&$data)
    {
        $bank_accounts = $this->resource->investor_bank_accounts()->with('bank:id,name')
            ->select(['id', 'bank_id', 'investor_id', 'account_holder', 'account_number', 'branch'])
            ->where('is_default', InvestorBankAccount::DEFAULT)->getResults();
        if ($bank_accounts) {
            $data['bank_id'] = $bank_accounts[0]['bank_id'];
            $data['bank'] = $bank_accounts[0]->bank()->select(['id','name','trade_name', 'code'])->getResults();
            $data['account_holder'] = $bank_accounts[0]['account_holder'];
            $data['account_number'] = $bank_accounts[0]['account_number'];
            $data['branch'] = $bank_accounts[0]['branch'];
            $data['description'] = $bank_accounts[0]['description'];
        }
    }
}
