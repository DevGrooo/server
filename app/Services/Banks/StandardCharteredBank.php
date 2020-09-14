<?php
namespace App\Services\Banks;

use App\Models\CashinReceipt;
use App\Models\FundDistributor;
use App\Models\FundDistributorBankAccount;
use App\Models\FundProduct;
use App\Models\Investor;
use App\Models\StatementLine;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Shared\Date;

/**
 * @author MSI
 * @version 1.0
 * @created 13-Jul-2020 2:52:40 PM
 */
class StandardCharteredBank
{
    protected $statement_line;
    protected $fund_distributor_bank_account;
    
    protected $data_result;
    protected $warnings = [];
    protected $errors = [];

    const MIN_AMOUNT = 1000000;

    function __construct(StatementLine $statement_line)
    {
        $this->statement_line = $statement_line;
    }

    public function checkData()
    {
        if (!$this->_checkTransactionAmount()) {
            $this->_addError('Số tiền giao dịch đang nhỏ hơn số tiền tối thiểu cho phép');
        }
        if ($this->_checkAccountNumber()) {
            $this->_processTransactionDetail();
        } else {
            $this->_addError('Số tài khoản ngân hàng không tồn tại trên hệ thống');
        }
        if (empty($this->errors)) {
            return true;
        }
        return false;
    }

    public function getDataResult()
    {
        $this->data_result['bank_paid_at'] = Date::excelToDateTimeObject($this->statement_line->data['value_date'])->format('Y-m-d H:i:s');
        return $this->data_result;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getWarnings()
    {
        return $this->warnings;
    }

    protected function _checkAccountNumber()
    {
        $account_number = substr($this->statement_line->data['account_number'], 3);
        $this->fund_distributor_bank_account = FundDistributorBankAccount::where('fund_distributor_id', $this->statement_line->fund_distributor_id)
            ->where('supervising_bank_id', $this->statement_line->supervising_bank_id)
            ->where('account_number', $account_number)->first();
        if ($this->fund_distributor_bank_account) {
            $this->data_result['fund_distributor_bank_account_id'] = $this->fund_distributor_bank_account->id;
            $this->data_result['fund_product_id'] = $this->fund_distributor_bank_account->fund_product_id;
            return true;
        }   
        return false;
    }

    protected function _checkTransactionAmount()
    {
        if ($this->statement_line->data['transaction_amount'] >= self::MIN_AMOUNT) {
            $this->data_result['amount'] = $this->statement_line->data['transaction_amount'];
            $this->data_result['fee'] = 0;
            $this->data_result['currency'] = $this->statement_line->data['currency_code'];
            return true;
        }
        return false;
    }

    protected function _processTransactionDetail()
    {
        // Example: IL99992002182797|00000409  General Posting Credit SHIN KWANG JIN 701FIC2904-SHINKWANGJIN-BUY MAGEF         701FIC2904-SHINKWANGJIN-BUY MAGEF
        $this->data_result['bank_trans_note'] = $this->statement_line->data['transaction_detail'];
        $this->data_result['cashin_receipt'] = $this->statement_line->data['transaction_detail'];
        $this->data_result['investor_id'] = 0;
        //-------------
        $cashin_receipt = CashinReceipt::where('supervising_bank_id', $this->statement_line->supervising_bank_id)
            ->where('receipt', $this->data_result['cashin_receipt'])->first();
        if ($cashin_receipt) {
            $this->_addError('Chứng từ thu đã tồn tại trên hệ thống');
        }
        $fund_product = FundProduct::find($this->fund_distributor_bank_account->fund_product_id);
        if (!$fund_product || strpos($this->statement_line->data['transaction_detail'], $fund_product->code) === false) {
            $this->_addWarning('Mã sản phẩm không tồn tại trong chi tiết giao dịch');
        }
        $investor = $this->_getInvestor($this->statement_line->fund_distributor);
        if ($investor) {
            if (strpos($this->statement_line->data['transaction_detail'], strtoupper(Str::ascii($investor->name))) !== false) {
                $this->data_result['investor_id'] = $investor->id;
            } else {
                $this->_addWarning('Tên nhà đầu tư trong chi tiết giao dịch không khớp');
            }
        } else {
            $this->_addWarning('Không tìm thấy thông tin nhà đầu tư trong chi tiết giao dịch');
        }
    }

    protected function _getInvestor(FundDistributor $fund_distributor)
    {
        if ($fund_distributor->investors) {
            foreach ($fund_distributor->investors as $investor) {
                if (strpos($this->statement_line->data['transaction_detail'], $investor->trading_account_number) !== false) {
                    return $investor;
                }
            }
        }
        return false;     
    }

    protected function _addError($message)
    {
        $this->errors[] = $message;
    }

    protected function _addWarning($message)
    {
        $this->warnings[] = $message;
    }

    public static function isDepositTransaction($data)
    {
        if ($data['dr_cr_flag'] === 'C') {
            return true;
        }
        return false;
    }

    public static function validateData($data)
    {
        if (isset($data['dr_cr_flag']) 
            && isset($data['account_number'])
            && isset($data['value_date'])
            && isset($data['transaction_amount'])
            && isset($data['currency_code'])
            && isset($data['transaction_amount'])) {
            return true;
        }
        return false;
    }
}