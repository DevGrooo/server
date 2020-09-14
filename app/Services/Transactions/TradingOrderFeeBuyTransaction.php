<?php

namespace App\Services\Transactions;

use App\Models\FundCertificate;
use App\Models\FundCompany;
use App\Models\FundProduct;
use App\Models\TradingOrderFeeBuy;
use Illuminate\Support\Carbon;

/**
 * @author MSI
 * @version 1.0
 * @created 10-Aug-2020 10:47:16 AM
 */
class TradingOrderFeeBuyTransaction extends Transaction
{
    /**
     *
     * @param TradingOrderFeeBuy $trading_order_fee_buy
     * @return boolean
     */
    private function _updateTimeEndAfterActive($trading_order_fee_buy)
    {
        $time = Carbon::now()->timestamp;
        $trading_order_fee_buy->end_at = 0;
        return $trading_order_fee_buy;
    }

    /**
     *
     * @param TradingOrderFeeBuy $trading_order_fee_buy
     * @return boolean
     */
    private function _updateTimeEndAfterLock($trading_order_fee_buy)
    {
        $time = Carbon::now()->timestamp;
        $trading_order_fee_buy->end_at = 0;
        return $trading_order_fee_buy;
    }

    /**
     * @param TradingOrderFeeBuy $trading_order_fee_buy
     * @param params
     * @param allow_commit
     */
    public function active($trading_order_fee_buy)
    {
        $trading_order_fee_buy->status = $trading_order_fee_buy::STATUS_NEW;
        $this->_updateTimeEndAfterActive($trading_order_fee_buy);
        return $trading_order_fee_buy;
    }

    /**
     *
     * @param array params
     * @param bool allow_commit
     * @param TradingOrderFeeBuy trading_order_fee_buy
     */
    public function create(array $params, $allow_commit = false, $line)
    {
        $response = null;
        $this->beginTransaction($allow_commit);
        try {
            $fund_product = FundProduct::where('id', $params['fund_product_id'])->first();
            if ($fund_product) {
                        $trading_order_fee_buy = new TradingOrderFeeBuy();
                        $trading_order_fee_buy->fund_product_id = $fund_product->id;
                        $trading_order_fee_buy->fund_company_id = $fund_product->fund_company_id;
                        $trading_order_fee_buy->fund_certificate_id = $fund_product->fund_certificate_id;
                        $trading_order_fee_buy->min_amount = $line['min_amount'];
                        $trading_order_fee_buy->fee_amount = 0;
                        $trading_order_fee_buy->fee_percent = $line['fee_percent'];
                        $trading_order_fee_buy->start_at = $params['start_at'];
                        $this->active($trading_order_fee_buy);
                        $create = $trading_order_fee_buy->save();
                        if (!$create) {
                            $this->error('Có lỗi khi thêm phí mua chứng chỉ !');
                        }
            } else {
                $this->error('Sản phẩm quỹ không tồn tại !');
            }


        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
    }


    /**
     *
     * @param params
     * @param allow_commit
     */
    public function createMulti($params, $allow_commit = false)
    {
        $response = null;
        $this->beginTransaction($allow_commit);
        try {
            $lines = $params['lines'];
            foreach ($lines as $line) {
                $this->create($params, true, $line);
            }
        } catch (\Exception $e) {
            // rollback transaction
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
            $trading_order_fee_buy = TradingOrderFeeBuy::where('id', $params['trading_order_fee_buy_id'])->first();
            if ($trading_order_fee_buy) {
                if ( $trading_order_fee_buy->status == TradingOrderFeeBuy::STATUS_NEW ) {

                    $trading_order_fee_buy->status = TradingOrderFeeBuy::STATUS_LOCK;
                    $this->_updateTimeEndAfterLock($trading_order_fee_buy);
                    $lock = $trading_order_fee_buy->save();
                    if (!$lock) {
                        $this->error('Có lỗi khi khóa phí mua chứng chỉ !');
                    }
                }
            } else {
                $this->error('lệnh mua này không tồn tại');
            }

        } catch (\Exception $e) {
            //rollback transaction
            $this->rollback($allow_commit, $e);
        }
        //commit and response
        return $this->response($allow_commit, $response);
    }

}
