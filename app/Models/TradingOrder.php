<?php

namespace App\Models;

class TradingOrder extends Model
{
    const STATUS_NEW = 1; //Mới tạo
    const STATUS_WAIT_COLLATE = 2; //Đợi xác nhận đối soát
    const STATUS_VERIFY = 3; //Đã xác nhận
    const STATUS_CANCEL = 4; //Đã hủy
    const STATUS_PERFORM = 5; //Hoàn thành

    const VSD_STATUS_NOT_IMPORT = 1; // Chưa import VSD
    const VSD_STATUS_IMPORT = 2; // Đã import VSD
    const VSD_STATUS_NOT_MATCH = 3; // Không khớp với VSD
    const VSD_STATUS_MATCH = 4; // Khớp với VSD

    const TYPE_SIP = 1; // Loại SIP
    const TYPE_NORMAL = 2; // Loại thường

    const DEAL_TYPE_BUY_NORMAL = 'NS'; // Mua thường
    const DEAL_TYPE_SELL_NORMAL = 'NR'; // Bán thường
    const DEAL_TYPE_BUY_SIP = 'NP'; // Mua SIP
    const DEAL_TYPE_IPO = 'IPO'; // IPO
    const DEAL_TYPE_EXCHANGE = 'SW'; // Hoán đổi

    const EXEC_TYPE_BUY = 1; // Lệnh mua
    const EXEC_TYPE_SELL = 2; // Lệnh bán
    const EXEC_TYPE_EXCHANGE = 3; // Hoán đổi

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'trading_orders';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'file_import_id', 'file_import_line_id', 'fund_company_id', 'fund_distributor_id', 'fund_distributor_bank_account_id', 'investor_id', 'investor_id_type',
        'investor_id_number', 'trading_frequency_id', 'trading_session_id', 'trading_account_number', 'investor_sip_id', 'deal_type', 'exec_type', 'send_fund_product_id',
        'send_fund_product_type_id', 'send_investor_fund_product_id', 'send_amount', 'send_match_amount', 'send_currency', 'send_fund_certificate_id',
        'receive_fund_product_id', 'receive_fund_product_type_id', 'receive_investor_fund_product_id', 'receive_fund_certificate_id',
        'receive_amount', 'receive_match_amount', 'receive_currency', 'fee', 'fee_send', 'fee_receive', 'tax', 'vsd_trading_id', 'vsd_time_received',
        'nav', 'total_nav', 'transaction_system_id', 'transaction_fund_id', 'created_date', 'status', 'vsd_status', 'created_by', 'updated_by'];

    protected $casts = [
        'created_date' => \App\Casts\Date::class,
    ];


    protected $rules = [
        'file_import_id' => 'integer|exists:file_imports,id',
        'file_import_line_id' => 'integer|exists:file_import_lines,id',
        'fund_company_id' => 'required|integer|row_exists',
        'fund_distributor_id' => 'required|integer|row_exists',
        'fund_distributor_bank_account_id' => 'required|integer|exists:fund_distributor_bank_accounts,id',
        'investor_id' => 'required|integer|row_exists',
        'investor_id_type' => 'required|integer',
        'investor_id_number' => 'required|string|max:50',
        'trading_frequency_id' => 'required|integer|row_exists',
        'trading_session_id' => 'required|integer|row_exists',
        'trading_account_number' => 'required|string|max:50',
        'investor_sip_id' => 'integer|row_exists',
        'deal_type' => 'required|string|max:10',
        'exec_type' => 'required|integer',
        'send_account_system_id' => 'integer|row_exists:account_systems',
        'send_fund_certificate_id' => 'integer|row_exists:fund_certificates',
        'send_fund_product_id' => 'integer|row_exists:fund_products',
        'send_fund_product_type_id' => 'integer|row_exists:fund_product_types',
        'send_investor_fund_product_id' => 'integer|row_exists:investor_fund_products',
        'send_amount' => 'required|numeric',
        'send_match_amount' => 'numeric',
        'send_currency' => 'required|string|max:50',
        'receive_account_system_id' => 'integer|row_exists:account_systems',
        'receive_fund_product_id' => 'integer|row_exists:fund_products',
        'receive_fund_product_type_id' => 'integer|row_exists:fund_product_types',
        'receive_investor_fund_product_id' => 'integer|row_exists:investor_fund_products',
        'receive_fund_certificate_id' => 'integer|row_exists:fund_certificates',
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
        'transaction_system_id' => 'integer|row_exists:transaction_systems',
        'transaction_fund_id' => 'integer|exists:transaction_funds,id',
        'created_date' => 'date',
        'status' => 'required|integer',
        'vsd_status' => 'required|integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    /**
     * Get the fund_company record associated with the trading_orders.
     */
    public function fund_company()
    {
        return $this->belongsTo('App\Models\FundCompany');
    }



    /**
     * Get the fund_distributors record associated with the trading_orders.
     */
    public function fund_distributor()
    {
        return $this->belongsTo('App\Models\FundDistributor');
    }



    /**
     * Get the investors record associated with the trading_orders.
     */
    public function investor()
    {
        return $this->belongsTo('App\Models\Investor');
    }



    /**
     * Get the trading_frequency record associated with the trading_orders.
     */
    public function trading_frequency()
    {
        return $this->belongsTo('App\Models\TradingFrequency');
    }



    /**
     * Get the trading_sessions record associated with the trading_orders.
     */
    public function trading_session()
    {
        return $this->belongsTo('App\Models\TradingSession');
    }



    /**
     * Get the sips record associated with the trading_orders.
     */
    public function sip()
    {
        return $this->belongsTo('App\Models\Sip');
    }



    /**
     * Get the fund_products record associated with the trading_orders.
     */
    public function send_fund_product()
    {
        return $this->belongsTo('App\Models\FundProduct');
    }



    /**
     * Get the fund_product_types record associated with the trading_orders.
     */
    public function send_fund_product_type()
    {
        return $this->belongsTo('App\Models\FundProductType');
    }



    /**
     * Get the fund_products record associated with the trading_orders.
     */
    public function receive_fund_product()
    {
        return $this->belongsTo('App\Models\FundProduct');
    }



    /**
     * Get the fund_product_types record associated with the trading_orders.
     */
    public function receive_fund_product_type()
    {
        return $this->belongsTo('App\Models\FundProductType');
    }



    /**
     * Get the vsd_tradings record associated with the trading_orders.
     */
    public function vsd_trading()
    {
        return $this->belongsTo('App\Models\VsdTrading');
    }



    /**
     * Get the transaction_systems record associated with the trading_orders.
     */
    public function transaction_system()
    {
        return $this->belongsTo('App\Models\TransactionSystem');
    }

    /**
     * Get the fund_products record associated with the trading_orders.
     */
    public function send_account_system()
    {
        return $this->belongsTo('App\Models\AccountSystem');
    }


    /**
     * Get the fund_product_types record associated with the trading_orders.
     */
    public function receive_account_system()
    {
        return $this->belongsTo('App\Models\AccountSystem');
    }

    /**
     * Get the fund_certificates record associated with the trading_orders.
     */
    public function receive_fund_certificate()
    {
        return $this->belongsTo('App\Models\FundCertificate');
    }

    /**
     * Get the fund_certificates record associated with the trading_orders.
     */
    public function send_fund_certificate()
    {
        return $this->belongsTo('App\Models\FundCertificate');
    }



    public static function getListStatus() {
        return array(
            TradingOrder::STATUS_NEW => trans('status.trading_order.new'),
            TradingOrder::STATUS_VERIFY => trans('status.trading_order.verify'),
            TradingOrder::STATUS_CANCEL => trans('status.trading_order.cancel'),
            TradingOrder::STATUS_PERFORM => trans('status.trading_order.perform')
        );
    }

    public static function getListVsdStatus() {
        return array(
            TradingOrder::VSD_STATUS_NOT_IMPORT => trans('status.trading_order.not import'),
            TradingOrder::VSD_STATUS_IMPORT => trans('status.trading_order.import.'),
            TradingOrder::VSD_STATUS_NOT_MATCH => trans('status.trading_order.not match'),
            TradingOrder::VSD_STATUS_MATCH => trans('status.trading_order.match')
        );
    }

    public static function getListExecType() {
        return array(
          TradingOrder::EXEC_TYPE_BUY => trans('status.trading_order.type buy'),
          TradingOrder::EXEC_TYPE_SELL => trans('status.trading_order.type sell'),
          TradingOrder::EXEC_TYPE_EXCHANGE => trans('status.trading_order.type exchange'),
        );
    }
}
