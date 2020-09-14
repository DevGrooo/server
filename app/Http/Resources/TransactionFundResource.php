<?php

namespace App\Http\Resources;

use App\Models\FundCertificate;
use App\Models\FundDistributorBankAccount;
use App\Models\SupervisingBank;
use App\Models\TradingOrder;
use App\Models\TransactionFund;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionFundResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                                             => $this->id,
            'investors_name'                                 => $this->investor()->select('id', 'name')->getResults(),
            'id_code'                                        => $this->investor()->select('id', 'id_number')->getResults(),
            'trading_date'                                   => $this->vsd_time_received,
            'fund_distributors'                              => $this->fund_distributor()->select('id', 'name')->getResults(),
            'trading_account'                                => $this->investor()->select('id', 'trading_account_number')->getResults(),
            'order_type'                                     => $this->_getExecType(),
            'transaction_type'                               => $this->deal_type,
            'order_date'                                     => $this->trading_order()->select('id', 'created_at')->getResults(),
            'ref'                                            => $this->vsd_trading_id,
            'order_status_name'                              => $this->_getOrderStatusName(),
            'order_status'                                   => $this->trading_order()->select('vsd_status')->getResults(),
            'trading_date_code'                              => $this->trading_session()->select('id', 'code')->getResults(),
            'matched_amount'                                 => $this->_getMatched_Amount($this->resource),
            'amount_of_sell_order'                           => $this->_getAmount_Of_Sell_Order($this->resource),
            'amount_of_buy_order'                            => $this->_getAmount_Of_Buy_Order($this->resource),
            'purchase_order_amount'                          => $this->_getAmount_Of_Buy_Order($this->resource), //Gọi ý hệt cái trên do yêu cầu giống nhau
            'number_of_fund_certificates_registered_to_sale' => $this->_getNumber_Of_Fund_Certificates_Registered_To_Sale($this->resource),
            'number_of_fund_certificates_sold'               => $this->_getNumber_Of_Fund_Certificates_Sold($this->resource),
            'number_of_fund_certificates_allocated'          => $this->_getNumber_Of_Fund_Certificates_Allocated($this->resource),
            'transaction_fee_or_sell_order'                  => $this->_getTransaction_Fee_For_Sell($this->resource),
            'transaction_fee_or_buy_order'                   => $this->_getTransaction_Fee_For_Buy($this->resource),
            'send_currency'                                  => $this->send_currency,
            'receive_currency'                               => $this->receive_currency,
            'fee'                                            => $this->fee,
            'fee_send'                                       => $this->fee_send,
            'fee_receive'                                    => $this->fee_receive,
            'tax'                                            => $this->tax,
            'product_type_name'                              => $this->send_fund_product_type()->select('id', 'name')->getResults(),
            'product_code'                                   => $this->receive_fund_product()->select('id', 'code')->getResults(),
            'bank_account'                                   => $this->fund_distributor_bank_account()->select('id', 'account_number')->getResults(),
            'bank_name'                                      => $this->_getBankName(), //Show theo supervisionbank
            'branch'                                         => $this->fund_distributor_bank_account()->select('id', 'branch')->getResults(),
            'status_name'                                    => $this->_getStatusName(),
            'status'                                         => $this->status,
            'fund_certificates'                              => $this->_getFundCertificatesCode($this->resource),
            'created_at'                                     => $this->created_at,
            'updated_at'                                     => $this->updated_at,
        ];
    }
    /**
     * @return TransactionFund $model
     */
    protected function _getExecType() {
        $list_extec_type = TransactionFund::getListExecType();
            return @$list_extec_type[$this->exec_type];
    }

    /**
     * @return TransactionFund $model
     */
    protected function _getStatusName() {
        $list_status = TransactionFund::getListStatus();
        return @$list_status[$this->status];
    }

    /**
     * @return TransactionFund $model
     */
    protected function _getOrderStatusName() {
        $list_status = TradingOrder::getListVsdStatus();
        $getNameStatus = $this->trading_order()->select('id', 'vsd_status')->getResults();
        return @$list_status[$getNameStatus->vsd_status];
    }

    /**
     * @return TransactionFund $model
     */
    protected function _getBankName() {
        $getIdBank = $this->fund_distributor_bank_account()->select('supervising_bank_id')->getResults();
        $getBank = SupervisingBank::where('id',$getIdBank->supervising_bank_id)->first();
        return $getBank->name;
    }


    /**
     * @return TransactionFund $model
     */
    protected function _getFundCertificatesCode($model) {
        if($model->exec_type == TransactionFund::EXEC_TYPE_SELL) {  // Lệnh bán
            $getFundCertificate = $this->send_fund_certificate()->select('id', 'code')->getResults($this->send_fund_certificate_id);
        } elseif($model->exec_type == TransactionFund::EXEC_TYPE_BUY) { // Lệnh mua
            $getFundCertificate = $this->receive_fund_certificate()->select('id', 'code')->getResults($this->receive_fund_certificate_id);
        } else {
            $getFundCertificate = $this->receive_fund_certificate()->select('id', 'code')->getResults($this->receive_fund_certificate_id);
        }
       return $getFundCertificate['code'];
    }

    /**
     * @return TransactionFund $model
     * bán = receive_match_amount , mua = send_match_amount
     */
    protected function _getMatched_Amount($model){
        if($model->exec_type == TransactionFund::EXEC_TYPE_SELL) { // Lệnh bán
            $getMatched_amount = $model->receive_match_amount ;
        } elseif($model->exec_type == TransactionFund::EXEC_TYPE_BUY) { // Lệnh mua
            $getMatched_amount = $model->send_match_amount ;
        } else { // Lệnh hoán đổi không biết làm gì
            $getMatched_amount = '' ;
        }
        return $getMatched_amount;
    }

    /**
     * @return TransactionFund $model
     * bán = receive_amount , mua = null
     */
    protected function _getAmount_Of_Sell_Order($model){
        if($model->exec_type == TransactionFund::EXEC_TYPE_SELL) { // Lệnh bán
            $getAmount_Of_Sell_Order = $model->receive_amount ; ;
        } elseif($model->exec_type == TransactionFund::EXEC_TYPE_BUY) { // Lệnh mua
            $getAmount_Of_Sell_Order = '';
        } else { // Lệnh hoán đổi không biết làm gì
            $getAmount_Of_Sell_Order = '' ;
        }
        return $getAmount_Of_Sell_Order;
    }

    /**
     * @return TransactionFund $model
     * bán = null , mua = send_amount
     */
    protected function _getAmount_Of_Buy_order($model){
        if($model->exec_type == TransactionFund::EXEC_TYPE_SELL) { // Lệnh bán
            $getAmount_of_Buy_order = '' ;
        } elseif($model->exec_type == TransactionFund::EXEC_TYPE_BUY) { // Lệnh mua
            $getAmount_of_Buy_order = $model->send_amount ;
        } else { // Lệnh hoán đổi không biết làm gì
            $getAmount_of_Buy_order = '' ;
        }
        return $getAmount_of_Buy_order;
    }

    /**
     * @return TransactionFund $model
     * bán = send_amount , mua = null
     */
    protected function _getNumber_Of_Fund_Certificates_Registered_To_Sale($model){
        if($model->exec_type == TransactionFund::EXEC_TYPE_SELL) { // Lệnh bán
            $getNumber_Of_Fund_Certificates_Registered_To_Sale = $model->send_amount ;
        } elseif($model->exec_type == TransactionFund::EXEC_TYPE_BUY) { // Lệnh mua
            $getNumber_Of_Fund_Certificates_Registered_To_Sale = '';
        } else { // Lệnh hoán đổi không biết làm gì
            $getNumber_Of_Fund_Certificates_Registered_To_Sale = '' ;
        }
        return $getNumber_Of_Fund_Certificates_Registered_To_Sale;
    }

    /**
     * @return TransactionFund $model
     * bán = send_match_amount , mua = null
     */
    protected function _getNumber_Of_Fund_Certificates_Sold($model){
        if($model->exec_type == TransactionFund::EXEC_TYPE_SELL) { // Lệnh bán
            $getNumber_Of_Fund_Certificates_Sold = $model->send_match_amount ;
        } elseif($model->exec_type == TransactionFund::EXEC_TYPE_BUY) { // Lệnh mua
            $getNumber_Of_Fund_Certificates_Sold = '' ;
        } else { // Lệnh hoán đổi không biết làm gì
            $getNumber_Of_Fund_Certificates_Sold = '' ;
        }
        return $getNumber_Of_Fund_Certificates_Sold;
    }

    /**
     * @return TransactionFund $model
     * bán = null , mua = receive_match_amount
     */
    protected function _getNumber_Of_Fund_Certificates_Allocated($model){
        if($model->exec_type == TransactionFund::EXEC_TYPE_SELL) { // Lệnh bán
            $getNumber_Of_Fund_Certificates_Allocated = '' ;
        } elseif($model->exec_type == TransactionFund::EXEC_TYPE_BUY) { // Lệnh mua
            $getNumber_Of_Fund_Certificates_Allocated = $model->receive_match_amount ;
        } else { // Lệnh hoán đổi không biết làm gì
            $getNumber_Of_Fund_Certificates_Allocated = '' ;
        }
        return $getNumber_Of_Fund_Certificates_Allocated;
    }

    /**
     * @return TransactionFund $model
     * bán = fee , mua = null
     */
    protected function _getTransaction_Fee_For_Sell($model){
        if($model->exec_type == TransactionFund::EXEC_TYPE_SELL) { // Lệnh bán
            $getTransaction_Fee_For_Sell = $model->fee ;
        } elseif($model->exec_type == TransactionFund::EXEC_TYPE_BUY) { // Lệnh mua
            $getTransaction_Fee_For_Sell = '' ;
        } else { // Lệnh hoán đổi không biết làm gì
            $getTransaction_Fee_For_Sell = '' ;
        }
        return $getTransaction_Fee_For_Sell;
    }

    /**
     * @return TransactionFund $model
     * bán = null  , mua = fee
     */
    protected function _getTransaction_Fee_For_Buy($model){
        if($model->exec_type == TransactionFund::EXEC_TYPE_SELL) { // Lệnh bán
            $getTransaction_Fee_For_Sell = '';
        } elseif($model->exec_type == TransactionFund::EXEC_TYPE_BUY) { // Lệnh mua
            $getTransaction_Fee_For_Sell = $model->fee  ;
        } else { // Lệnh hoán đổi không biết làm gì
            $getTransaction_Fee_For_Sell = '' ;
        }
        return $getTransaction_Fee_For_Sell;
    }
}
