<?php

namespace App\Services\Transactions;

use App\Models\SettingCommission;
use Illuminate\Support\Facades\Auth;

/**
 * @author MSI
 * @version 1.0
 * @created 13-Jul-2020 2:52:41 PM
 */
class SettingCommissionTransaction extends Transaction
{

    /**
     * 
     * @param params
     * @param allow_commit
     */
    public function accept(array $params, $allow_commit = false)
    {
    }

    /**
     * 
     * @param params
     * @param allow_commit
     */
    public function create(array $params, $allow_commit = false)
    {
        $lines            = $params['lines'];
        $params['status'] = SettingCommission::STATUS_NEW;
        unset($params['lines'], $params['fund_distributor_id']);
        $this->beginTransaction($allow_commit);
        try {
            $model = SettingCommission::create($params);
            if (!$model) {
                $this->error('Có lỗi khi thiết lập hoa hồng');
            }
            $sameData = [
                'setting_commission_id' => $model->id,
                'status'                => $params['status'],
                'ref_id'                => $params['ref_id'],
                'start_at'              => '2020-08-12 09:51:37',
                'ref_type'              => 'setting_commission_line',
                'fund_company_id'       => $params['fund_company_id'],
                'fund_product_id'       => $params['fund_product_id'],
                'fund_certificate_id'   => $params['fund_certificate_id']
            ];
            $params = [];
            foreach ($lines as $item) {
                $params[] = array_merge($sameData, $item);
            }
            (new SettingCommissionLineTransaction())->create($params);
            $model->update([
                'accepted_at' => date('d/m/Y H:i:s'),
                'accepted_by' => Auth::user()->id
            ]);
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        //commit and response
        return $this->response($allow_commit);
    }

    /**
     * 
     * @param params
     * @param allow_commit
     */
    public function lock(array $params, $allow_commit = false)
    {
        $this->beginTransaction($allow_commit);
        try {
            (new SettingCommissionLineTransaction())->lock($params);
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }

        // commit and response
        return $this->response($allow_commit);
    }

    /**
     * 
     * @param params
     * @param allow_commit
     */
    public function reject(array $params, $allow_commit = false)
    {
    }

    /**
     * 
     * @param params
     * @param allow_commit
     */
    public function request(array $params, $allow_commit = false)
    {
    }

    /**
     * 
     * @param params
     * @param allow_commit
     */
    public function update(array $params, $allow_commit = false)
    {
    }
}
