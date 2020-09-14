<?php


namespace App\Http\Resources;


use App\Models\TradingOrder;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class TradingOrderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
        'id' => $this->id,
        'fund_company' => $this->fund_company()->select('id', 'name')->getResults(),
        'fund_distributor' => $this->fund_distributor()->select('id', 'name')->getResults(),
        'investor' => $this->investor()->select('id', 'name', 'trading_account_number', 'id_number')->getResults(),
        'trading_frequency' => $this->trading_frequency()->select('id', 'name')->getResults(),
        'trading_session' => $this->trading_session()->getResults(),
        'sip' => $this->sip()->select('id', 'name')->getResults(),
        'deal_type' => $this->deal_type,
        'exec_type' => $this->exec_type,
        'fund' => $this->_getFund($this->resource),
         'product_code' => $this->_getProductCode($this->resource),
         'trading_order' => $this->_getTradingDate($this->resource),
         'sub_amount' => $this->_getSubAmount($this->resource),
        'order_type' => $this->deal_type,
            'order_quantity' => $this->_getOrderQuantity($this->resource),
            'fund_product_type' => $this->receive_fund_product_type()->select('id', 'name')->getResults(),
            'switch_fund' => $this->_getSwitchingFund($this->resource),
        'product_of_switch_fund' => $this->_getProductOfSwitchingFund($this->resource),
        'transaction_type' => $this->_getTransactionType(),
        'ref'       => $this->_getRef($this->resource),
        'status' => $this->status,
        'status_name' => $this->_getStatusName(),
         'vsd_status' => $this->vsd_status,
         'vsd_status_name' => $this->_getVsdStatusName(),
         'action' => $this->action
        ];
    }

    /**
     * @param TradingOrder $model
     * @return mixed
     */

    protected function _getFund($model) {
         if($model->exec_type == TradingOrder::EXEC_TYPE_BUY) {
             $getfund = $this->receive_fund_certificate()->select('id', 'name', 'code')->getResults($this->receive_fund_certificate_id);
         }
        else{
             $getfund = $this->send_fund_certificate()->select('id', 'name', 'code')->getResults($this->send_fund_certificate_id);

        }
        return $getfund;
    }

    protected function _getTradingDate($model) {
        $time = Carbon::now()->format('dmYY');
        if($model->exec_type == TradingOrder::EXEC_TYPE_BUY) {
            $getfund = $this->receive_fund_certificate()->select('id', 'name', 'code')->getResults($this->receive_fund_certificate_id);
        }
        else{
            $getfund = $this->send_fund_certificate()->select('id', 'name', 'code')->getResults($this->send_fund_certificate_id);

        }
//        dd($getfund['code'].$time);
        return $getfund['code'].$time;
    }

  protected function _getProductCode($model) {
      if($model->exec_type == TradingOrder::EXEC_TYPE_BUY) {
          $getProductCode = $this->receive_fund_product()->select('id', 'name', 'code')->getResults($this->receive_fund_product_id);
      }
      else{
          $getProductCode = $this->send_fund_product()->select('id', 'name', 'code')->getResults($this->send_fund_product_id);

      }
      return $getProductCode;
    }


    protected function _getTransactionType() {
        $list_transaction_type = $this->getListExecType();
        return @$list_transaction_type[$this->exec_type];
    }

    protected function _getSwitchingFund($model) {
      if ($model->exec_type == TradingOrder::EXEC_TYPE_EXCHANGE) {
          $getSwitchingFund = $this->receive_fund_certificate()->select('id', 'name', 'code')->getResults($this->receive_fund_certificate_id);
          return $getSwitchingFund;
      }
    }
    protected function _getProductOfSwitchingFund($model) {
        if ($model->exec_type == TradingOrder::EXEC_TYPE_EXCHANGE) {
            $getProductOfSwitching = $this->receive_fund_product()->select('id', 'name', 'code')->getResults($this->receive_fund_product_id);
            return $getProductOfSwitching;
        }
    }

    protected function _getRef($model) {
        $time = Carbon::now()->format('dmYY');
        if($model->deal_type == TradingOrder::DEAL_TYPE_BUY_NORMAL) {
            $getfund = $this->receive_fund_certificate()->select('id', 'name', 'code')->getResults($this->receive_fund_certificate_id);
        }
        else{
            $getfund = $this->send_fund_certificate()->select('id', 'name', 'code')->getResults($this->send_fund_certificate_id);

        }
        return $getfund['code'].$time.$model->id;
    }

    protected function _getStatusName() {
        $list_status = TradingOrder::getListStatus();
        return @$list_status[$this->status];
    }
    protected function _getVsdStatusName() {
        $list_vsd_status = TradingOrder::getListVsdStatus();
        return @$list_vsd_status[$this->vsd_status];
    }

    protected function _getSubAmount($model) {
                if ($model->exec_type == TradingOrder::EXEC_TYPE_BUY) {
                    $getSubAmount =  $this->send_amount;
                } else {
                    $getSubAmount =  $this->receive_amount;
                }
        return $getSubAmount;
    }

    protected function _getOrderQuantity($model)
    {
        if ($model->exec_type == TradingOrder::EXEC_TYPE_BUY) {
            $getOrderQuantity = $this->receive_amount;
        } else {
            $getOrderQuantity = $this->send_amount;
        }
        return $getOrderQuantity;
    }
}
