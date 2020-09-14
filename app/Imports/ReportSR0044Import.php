<?php

namespace App\Imports;

use App\Models\FundCertificate;
use App\Models\FundProduct;
use App\Models\Investor;
use App\Models\TradingOrder;
use App\Models\TradingSession;
use App\Models\TransactionFund;
use App\Rules\IdNumberRule;
use App\Rules\TradingAccountNumberRule;
use App\Rules\VNDateRule;

class ReportSR0044Import extends BasicImport
{
    protected function _processValueInRow($row)
    {
        // check trading account number & name
        $investor = Investor::where('trading_account_number', $this->data_result['trading_account_number'])->first();
        if ($investor) {
            $this->data_result['investor_id'] = $investor->id;
            if ($investor->name != $row['fullname']) {
                $this->_addWarning('Tên nhà đầu tư không khớp với dữ liệu đã có');
            } elseif ($investor->id_number != $row['idcode']) {
                $this->_addWarning('Số đăng ký xác thực nhà đầu tư không khớp với dữ liệu đã có');
            } elseif ($investor->id_issuing_date != $row['iddate']) {
                $this->_addWarning('Ngày cấp giấy đăng ký xác thực nhà đầu tư không khớp với dữ liệu đã có');
            } else {
                if ($row['actype'] == 'Thông thường' && $investor->invest_type != Investor::INVEST_TYPE_NORMAL) {
                    $this->_addWarning('Loại tài khoản nhà đầu tư không khớp với dữ liệu đã có');
                } elseif ($row['actype'] == 'Chuyên nghiệp' && $investor->invest_type != Investor::INVEST_TYPE_PROFESSION) {
                    $this->_addWarning('Loại tài khoản nhà đầu tư không khớp với dữ liệu đã có');
                }
                $trading_order = $this->_getTradingOrder($row, $investor->id);
                if ($trading_order != false) {
                    $this->data_result['trading_order_id'] = $trading_order->id;
                } else {
                    $this->_addError('Không tìm thấy lệnh giao dịch quỹ tương ứng');
                }
            }
        } else {
            $this->_addError('Nhà đầu tư không tồn tại');
        }        
        if ($row['deal_type'] == TransactionFund::DEAL_TYPE_BUY_NORMAL) {
            $this->data_result['send_match_amount'] = $row['matchamtns'];
            $this->data_result['receive_match_amount'] = $row['matchqttyns'];
            $this->data_result['fee_send'] = $row['feedxx'];
            $this->data_result['fee_receive'] = 0;
        } elseif ($row['deal_type'] == TransactionFund::DEAL_TYPE_SELL_NORMAL) {
            $this->data_result['send_match_amount'] = $row['matchqttynr'];
            $this->data_result['receive_match_amount'] = $row['matchamtnr'];
            $this->data_result['fee_send'] = 0;
            $this->data_result['fee_receive'] = $row['feeamc'];
        }
        $this->data_result['fee'] = $row['feetotal'];        
        $this->data_result['tax'] = $row['taxamt'];
        $this->data_result['vsd_trading_id'] = $row['orderid'];
        $this->data_result['vsd_time_received'] = $row['tradingdate'];
        $this->data_result['nav'] = $row['nav'];
        $this->data_result['total_nav'] = $row['totalnav'];
        $this->data_result['created_date'] = $row['txdate'];
        $this->data_result['status'] = $this->_getTransactionFundStatus($row);
    }

    private function _getTransactionFundStatus($row)
    {
        if ($row['status'] == 'Khớp hết') {
            return TransactionFund::STATUS_PERFORM;
        } elseif ($row['status' == 'Không khớp']) {
            return TransactionFund::STATUS_CANCEL;
        }
        $this->_addError('Trạng thái không được hỗ trợ xử lý');
        return false;
    }

    private function _getTradingOrder($row, $investor_id)
    {
        $fund_certificate = FundCertificate::where('code', $row['symbol'])->first();
        $fund_product = FundProduct::where('code', $row['feeid'])->first();
        $trading_session = TradingSession::where('code', $row['pv_symbol'])->first();
        $trading_order_query_builder = TradingOrder::where('investor_id', $investor_id)
            ->where('fund_certificate_id', $fund_certificate->id)
            ->where('fund_product_id', $fund_product->id)
            ->where('trading_session_id', $trading_session->id)
            ->where('status', TradingOrder::STATUS_VERIFY)
            ->where('vsd_status', TradingOrder::VSD_STATUS_IMPORT);
        if ($row['deal_type'] == TransactionFund::DEAL_TYPE_BUY_NORMAL) {
            $trading_order_query_builder->where('deal_type', TradingOrder::DEAL_TYPE_BUY_NORMAL);
            $trading_order_query_builder->where('exec_type', TradingOrder::EXEC_TYPE_BUY);
            $trading_order_query_builder->where('send_amount', $row['orderamt']);
        } elseif ($row['deal_type'] == TransactionFund::DEAL_TYPE_SELL_NORMAL) {
            $trading_order_query_builder->where('deal_type', TradingOrder::DEAL_TYPE_SELL_NORMAL);
            $trading_order_query_builder->where('exec_type', TradingOrder::EXEC_TYPE_SELL);
            $trading_order_query_builder->where('send_amount', $row['orderqtty']);
        }
        $trading_order = $trading_order_query_builder->first();
        return $trading_order;
    }
    
    protected function _getRulesValidateRow($row)
    {
        $rules = array(
            'fundname' => 'string',
            'mbname' => 'required|string',
            'fullname' => 'required|string',
            'idcode' => ['required', new IdNumberRule],
            'dbcode' => 'required|regex:/(^[A-Z0-9]{3}$)/u|exists:fund_distributors,code',
            'iddate' => ['required', new VNDateRule],
            'custodycd' => ['required', new TradingAccountNumberRule($row['dbcode'])],
            'srexetype' => 'required|string',
            'dealtype' => ['required', 'regex:/(NS|NR)/u'],
            'exectype' => 'required|string',
            'txdate' => ['required', new VNDateRule],
            'feeamt' => 'required|numeric',
            'taxamt' => 'required|numeric',
            'pv_symbol' => 'required|exists:trading_sessions,code',
            'matchamt' => 'required|numeric',
            'matchamtnr' => 'required|numeric',
            'matchamtns' => 'required|numeric',
            'orderamt' => 'required|numeric',
            'matchqttynr' => 'required|numeric',
            'matchqttyns' => 'required|numeric',
            'orderqtty' => 'required|numeric',
            'matchamtt' => 'required|numeric',
            'orderid' => 'required|string',
            'status' => 'required|string',
            'actype' => 'required|string',
            'feeamc' => 'required|numeric',
            'feedxx' => 'required|numeric',
            'feefund' => 'required|numeric',
            'feetotal' => 'required|numeric',
            'nav' => 'required|numeric',
            'totalnav' => 'required|numeric',
            'tradingdate' => ['required', new VNDateRule],
            'name' => 'required|string',
            'symbol' => 'required|exists:fund_certificates,code',
            'feename' => 'required|string',
            'feeid' => 'required|exists:fund_products,code',
        );
        return $rules;
    }
}