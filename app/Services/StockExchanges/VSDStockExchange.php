<?php
namespace App\Services\StockExchanges;


/**
 * @author MSI
 * @version 1.0
 * @created 13-Jul-2020 2:52:40 PM
 */
class VSDStockExchange
{
    protected $data_result;
    protected $warnings = [];
    protected $errors = [];

    function __construct()
    {
        
    }

    public function checkInvestor($data) 
    {
        
    }

    public static function validateInvestorData($data)
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

    protected function _addError($message)
    {
        $this->errors[] = $message;
    }

    protected function _addWarning($message)
    {
        $this->warnings[] = $message;
    }
}