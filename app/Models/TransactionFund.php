<?php

namespace App\Models;

class TransactionFund extends Model
{
    const STATUS_NEW = 1; //Mới tạo
    const STATUS_VERIFY = 2; //Đã xác nhận
    const STATUS_CANCEL = 3; //Đã hủy
    const STATUS_PERFORM = 4; //Hoàn thành
    const EXEC_TYPE_BUY = 1; // Lệnh mua
    const EXEC_TYPE_SELL = 2; // Lệnh bán
    const EXEC_TYPE_EXCHANGE = 3; // Lệnh hóan


    const DEAL_TYPE_BUY_NORMAL = 'NS'; // Mua thường
    const DEAL_TYPE_SELL_NORMAL = 'NR'; // Bán thường
    const DEAL_TYPE_BUY_SIP = 'NP'; // Mua SIP
    const DEAL_TYPE_IPO = 'IPO'; // IPO
    const DEAL_TYPE_EXCHANGE = 'SW'; // Hoán đổi


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transaction_funds';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['file_import_id', 'file_import_line_id', 'trading_order_id', 'fund_company_id', 'fund_distributor_id', 'fund_distributor_bank_account_id',
        'investor_id', 'investor_id_type', 'investor_id_number', 'trading_frequency_id', 'trading_session_id', 'trading_account_number', 'investor_sip_id', 'deal_type',
        'exec_type', 'send_fund_certificate_id', 'send_fund_product_id', 'send_fund_product_type_id', 'send_investor_fund_product_id', 'send_amount',
        'send_match_amount', 'send_currency', 'receive_fund_certificate_id', 'receive_fund_product_id', 'receive_fund_product_type_id',
        'receive_investor_fund_product_id', 'receive_amount', 'receive_match_amount', 'receive_currency', 'fee', 'fee_send', 'fee_receive', 'tax',
        'vsd_trading_id', 'vsd_time_received', 'nav', 'total_nav', 'created_date', 'status', 'reason_cancel', 'created_by', 'updated_by'];

    protected $casts = [
        'created_date' => \App\Casts\Date::class,
    ];


    protected $rules = [
        'file_import_id' => 'required|integer|exists:file_imports,id',
        'file_import_line_id' => 'required|integer|exists:file_import_lines,id',
        'trading_order_id' => 'required|integer|exists:trading_orders,id',
        'fund_company_id' => 'required|integer|exists:fund_company,id',
        'fund_distributor_id' => 'required|integer|exists:fund_distributors,id',
        'fund_distributor_bank_account_id' => 'required|integer|exists:fund_distributor_bank_accounts,id',
        'investor_id' => 'required|integer|exists:investors,id',
        'investor_id_type' => 'required|integer',
        'investor_id_number' => 'required|string|max:50',
        'trading_frequency_id' => 'required|integer|exists:trading_frequency,id',
        'trading_session_id' => 'required|integer|exists:trading_sessions,id',
        'trading_account_number' => 'required|string|max:50',
        'investor_sip_id' => 'integer|exists:investor_sips,id',
        'deal_type' => 'required|string|max:10',
        'exec_type' => 'required|integer',
        'send_fund_certificate_id' => 'integer|exists:fund_certificates,id',
        'send_fund_product_id' => 'integer|exists:fund_products,id',
        'send_fund_product_type_id' => 'integer|exists:fund_product_types,id',
        'send_investor_fund_product_id' => 'integer|exists:investor_fund_products,id',
        'send_amount' => 'required|numeric',
        'send_match_amount' => 'numeric',
        'send_currency' => 'required|string|max:50',
        'receive_fund_certificate_id' => 'integer|exists:fund_certificates,id',
        'receive_fund_product_id' => 'integer|exists:fund_products,id',
        'receive_fund_product_type_id' => 'integer|exists:fund_product_types,id',
        'receive_investor_fund_product_id' => 'integer|exists:investor_fund_products,id',
        'receive_amount' => 'numeric',
        'receive_match_amount' => 'numeric',
        'receive_currency' => 'string|max:50',
        'fee' => 'numeric',
        'fee_send' => 'numeric',
        'fee_receive' => 'numeric',
        'tax' => 'numeric',
        'vsd_trading_id' => 'string|max:50',
        'vsd_time_received' => 'integer',
        'nav' => 'numeric',
        'total_nav' => 'numeric',
        'created_date' => 'required|date',
        'status' => 'required|integer',
        'reason_cancel' => 'string',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];




    /**
     * Get the file_imports record associated with the transaction_funds.
     */
    public function file_import()
    {
        return $this->belongsTo('App\Models\FileImport');
    }



    /**
     * Get the file_import_lines record associated with the transaction_funds.
     */
    public function file_import_line()
    {
        return $this->belongsTo('App\Models\FileImportLine');
    }



    /**
     * Get the trading_orders record associated with the transaction_funds.
     */
    public function trading_order()
    {
        return $this->belongsTo('App\Models\TradingOrder');
    }



    /**
     * Get the fund_company record associated with the transaction_funds.
     */
    public function fund_company()
    {
        return $this->belongsTo('App\Models\FundCompany');
    }



    /**
     * Get the fund_certificates record associated with the transaction_funds.
     */
    public function fund_certificates()
    {
        return $this->belongsTo('App\Models\FundCertificate');
    }



    /**
     * Get the fund_distributors record associated with the transaction_funds.
     */
    public function fund_distributor()
    {
        return $this->belongsTo('App\Models\FundDistributor');
    }



    /**
     * Get the investors record associated with the transaction_funds.
     */
    public function investor()
    {
        return $this->belongsTo('App\Models\Investor');
    }



    /**
     * Get the trading_frequency record associated with the transaction_funds.
     */
    public function trading_frequency()
    {
        return $this->belongsTo('App\Models\TradingFrequency');
    }



    /**
     * Get the trading_sessions record associated with the transaction_funds.
     */
    public function trading_session()
    {
        return $this->belongsTo('App\Models\TradingSession');
    }



    /**
     * Get the investor_sips record associated with the transaction_funds.
     */
    public function investor_sip()
    {
        return $this->belongsTo('App\Models\InvestorSip');
    }



    /**
     * Get the fund_products record associated with the transaction_funds.
     */
    public function send_fund_product()
    {
        return $this->belongsTo('App\Models\FundProduct');
    }



    /**
     * Get the fund_product_types record associated with the transaction_funds.
     */
    public function send_fund_product_type()
    {
        return $this->belongsTo('App\Models\FundProductType');
    }



    /**
     * Get the investor_fund_products record associated with the transaction_funds.
     */
    public function send_investor_fund_product()
    {
        return $this->belongsTo('App\Models\InvestorFundProduct');
    }




    /**
     * Get the fund_certificates record associated with the transaction_funds.
     */
    public function send_fund_certificate()
    {
        return $this->belongsTo('App\Models\FundCertificate');
    }



    /**
     * Get the fund_certificates record associated with the transaction_funds.
     */
    public function receive_fund_certificate()
    {
        return $this->belongsTo('App\Models\FundCertificate');
    }



    /**
     * Get the fund_products record associated with the transaction_funds.
     */
    public function receive_fund_product()
    {
        return $this->belongsTo('App\Models\FundProduct');
    }



    /**
     * Get the fund_product_types record associated with the transaction_funds.
     */
    public function receive_fund_product_type()
    {
        return $this->belongsTo('App\Models\FundProductType');
    }



    /**
     * Get the investor_fund_products record associated with the transaction_funds.
     */
    public function receive_investor_fund_product()
    {
        return $this->belongsTo('App\Models\InvestorFundProduct');
    }

    /**
     * Get the investor_fund_products record associated with the transaction_funds.
     */
    public function fund_distributor_bank_account()
    {
        return $this->belongsTo('App\Models\FundDistributorBankAccount');
    }



    /**
     * Get the vsd_tradings record associated with the transaction_funds.
     */
    public function vsd_trading()
    {
        return $this->belongsTo('App\Models\VsdTrading');
    }

    public static function getListExecType(){
        return array(
            TransactionFund::EXEC_TYPE_BUY => trans('status.transaction_fund.exec_type_buy'),
            TransactionFund::EXEC_TYPE_SELL => trans('status.transaction_fund.exec_type_sell'),
            TransactionFund::EXEC_TYPE_EXCHANGE => trans('status.transaction_fund.exec_type_change'),
        );
    }
    public static function getListStatus()
    {
        return array(
            TransactionFund::STATUS_NEW => trans('status.transaction_fund.new'),
            TransactionFund::STATUS_VERIFY => trans('status.transaction_fund.verify'),
            TransactionFund::STATUS_CANCEL => trans('status.transaction_fund.cancel'),
            TransactionFund::STATUS_PERFORM => trans('status.transaction_fund.perform'),
        );
    }
}
