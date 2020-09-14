<?php

namespace App\Services\Transactions;

use Illuminate\Support\Facades\DB;
use App\Models\SettingCommissionLine;

/**
 * @author MSI
 * @version 1.0
 * @created 06-Aug-2020 10:46:07 AM
 */
class SettingCommissionLineTransaction extends Transaction
{
    /**
     * 
     * @param model
     */
    private function _updateTimeEndAfterAccept($model)
    {
    }

    /**
     * 
     * @param model
     */
    private function _updateTimeEndAfterLock($model)
    {
        $dt = date('Y-m-d H:i:s');
        foreach ($model as $item) {
            $settingCommissionLine = SettingCommissionLine::whereDate('start_at', '<', $dt)
                ->where('min_amount', $item->min_amount)
                ->orderBy('start_at', 'desc')
                ->first();

            if ($settingCommissionLine) {
                $settingCommissionLine->update(['end_at' => $dt, 'status' => SettingCommissionLine::STATUS_LOCK]);
            }
        }
    }

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
        DB::table('setting_commission_lines')->insert($params);
    }

    /**
     * 
     * @param params
     * @param allow_commit
     */
    public function lock(array $params, $allow_commit = false)
    {
        $settingCommissionLine = SettingCommissionLine::where('setting_commission_id', $params['setting_commission_id'])->get();
        $this->_updateTimeEndAfterLock($settingCommissionLine);
    }
}
