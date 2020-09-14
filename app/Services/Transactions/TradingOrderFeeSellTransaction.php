<?php

namespace App\Services\Transactions;

use App\Models\FundProduct;
use App\Models\TradingOrderFeeSell;
use Illuminate\Support\Carbon;

/**
 * @author MSI
 * @version 1.0
 * @created 10-Aug-2020 10:47:18 AM
 */
class TradingOrderFeeSellTransaction extends Transaction
{
    /**
     *
     * @param TradingOrderFeeSell $model
     * @return boolean
     * @param trading_order_fee_sell
     */
    private function _updateTimeEndAfterActive($trading_order_fee_sell)
    {
        $time = Carbon::now()->timestamp;
        $trading_order_fee_sell->end_at = 0;
        return $trading_order_fee_sell;
    }

    /**
     *
     * @param TradingOrderFeeSell $model
     * @return boolean
     * @param trading_order_fee_sell
     */
    private function _updateTimeEndAfterLock($trading_order_fee_sell)
    {
        $time = Carbon::now()->timestamp;
        $trading_order_fee_sell->end_at = 0;
        return $trading_order_fee_sell;
    }

    /**
     *
     * @param params
     * @param allow_commit
     * @param trading_order_fee_sell
     */
    public function active($trading_order_fee_sell)
    {
        $trading_order_fee_sell->status = $trading_order_fee_sell::STATUS_NEW;
        $this->_updateTimeEndAfterActive($trading_order_fee_sell);
        return $trading_order_fee_sell;
    }

    /**
     *
     * @param params
     * @param allow_commit
     * @param line
     */
    public function create(array $params, $allow_commit = false, $line)
    {
        $response = null;
        $this->beginTransaction($allow_commit);
        try {
            $fund_product = FundProduct::where('id', $params['fund_product_id'])->first();
            if ($fund_product) {
                $trading_order_fee_sell = new TradingOrderFeeSell();
                $trading_order_fee_sell->fund_product_id = $fund_product->id;
                $trading_order_fee_sell->fund_company_id = $fund_product->fund_company_id;
                $trading_order_fee_sell->fund_certificate_id = $fund_product->fund_certificate_id;
                $trading_order_fee_sell->start_at = $params['start_at'];
                $trading_order_fee_sell->holding_period = $line['holding_period'];
                $trading_order_fee_sell->fee_amount = $line['fee_amount'];
                $trading_order_fee_sell->fee_percent = $line['fee_percent'];
                $this->active($trading_order_fee_sell);
                $create = $trading_order_fee_sell->save();
                if (!$create) {
                    $this->error('Có lỗi khi thêm phí bán chứng chỉ !');
                }
            } else {
                $this->error('Sản phẩm quỹ không tồn tại !');
            }
        } catch (\Exception $e) {
            //rollback transaction
            $this->rollback($allow_commit, $e);
        }
        //commit and response
        $this->response($allow_commit, $response);
    }

    /**
     *
     * @param params
     * @param allow_commit
     */
    public function createMulti(array $params, $allow_commit = false)
    {
        $response = null;
        $this->beginTransaction($allow_commit);
        try {
            $lines = $params['lines'];
            foreach ($lines as $line) {
                $this->create($params, $allow_commit, $line);
            }
        }catch (\Exception $e) {
            //rollback transaction
            $this->rollback($allow_commit, $e);
        }
        //commit and response
        return $this->response($allow_commit, $response);
    }

    /**
     *
     * @param params
     * @param allow_commit
     */
    public function lock(array $params, $allow_commit = false)
    {
        $response = null;
        $this->beginTransaction($allow_commit);
        try {
            $trading_order_fee_sell = TradingOrderFeeSell::where('id', $params['trading_order_fee_sell_id'])->first();
            if ($trading_order_fee_sell) {
                if ( $trading_order_fee_sell->status == TradingOrderFeeSell::STATUS_NEW ) {

                    $trading_order_fee_sell->status = TradingOrderFeeSell::STATUS_LOCK;
                    $this->_updateTimeEndAfterLock($trading_order_fee_sell);
                    $trading_order_fee_sell->save();
                }
            } else {
                $this->error('Lệnh bán này không tồn tại');
            }

        } catch (\Exception $e) {
            //rollback transaction
            $this->rollback($allow_commit, $e);
        }
        //commit and response
        return $this->response($allow_commit, $response);
    }
}
