<?php

namespace App\Models;

class TransactionCommission extends Model
{
    const STATUS_NEW = 1; //Mới tạp
    const STATUS_VERIFY = 2; //Đã xác nhận
    const STATUS_CANCEL = 3; //Đã hủy
    const STATUS_PERFORM = 4; //Đã thực hiện

    const TYPE_MAINTANCE = 1; // phí duy trì
    const TYPE_BUY = 2; // Phí mua

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transaction_commissions';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['type', 'ref_id', 'ref_type', 'fund_company_id', 'fund_certificate_id', 'fund_product_id', 'trading_order_id', 'trading_session_id', 'setting_commission_id', 'send_account_commission_id', 'receive_account_commission_id', 'amount', 'currency', 'status', 'canceled_at', 'verified_at', 'performed_at', 'created_by', 'updated_by', 'canceled_by', 'verified_by', 'performed_by'];

    protected $casts = [
        'canceled_at' => \App\Casts\DateTime::class,
        'verified_at' => \App\Casts\DateTime::class,
        'performed_at' => \App\Casts\DateTime::class,
    ];


    protected $rules = [
        'type' => 'required|integer',
        'ref_id' => 'required|integer|row_exists',
        'ref_type' => 'required|string|max:100',
        'fund_company_id' => 'required|integer|row_exists',
        'fund_certificate_id' => 'required|integer|row_exists',
        'fund_product_id' => 'required|integer|row_exists',
        'trading_order_id' => 'integer|row_exists',
        'trading_session_id' => 'integer|row_exists',
        'setting_commission_id' => 'required|integer|row_exists',
        'send_account_commission_id' => 'required|integer|row_exists',
        'receive_account_commission_id' => 'required|integer|row_exists',
        'amount' => 'required|numeric',
        'currency' => 'required|string|max:10',
        'status' => 'required|integer',
        'canceled_at' => 'date',
        'verified_at' => 'date',
        'performed_at' => 'date',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'canceled_by' => 'integer',
        'verified_by' => 'integer',
        'performed_by' => 'integer',
    ];




    /**
     * Get the refs record associated with the transaction_commissions.
     */
    public function ref()
    {
        return $this->belongsTo('App\Models\Ref');
    }



    /**
     * Get the fund_company record associated with the transaction_commissions.
     */
    public function fund_company()
    {
        return $this->belongsTo('App\Models\FundCompany');
    }



    /**
     * Get the fund_certificates record associated with the transaction_commissions.
     */
    public function fund_certificate()
    {
        return $this->belongsTo('App\Models\FundCertificate');
    }



    /**
     * Get the fund_products record associated with the transaction_commissions.
     */
    public function fund_product()
    {
        return $this->belongsTo('App\Models\FundProduct');
    }



    /**
     * Get the trading_orders record associated with the transaction_commissions.
     */
    public function trading_order()
    {
        return $this->belongsTo('App\Models\TradingOrder');
    }



    /**
     * Get the setting_commissions record associated with the transaction_commissions.
     */
    public function setting_commission()
    {
        return $this->belongsTo('App\Models\SettingCommission');
    }



    /**
     * Get the account_commissions record associated with the transaction_commissions.
     */
    public function send_account_commission()
    {
        return $this->belongsTo('App\Models\AccountCommission');
    }



    /**
     * Get the account_commissions record associated with the transaction_commissions.
     */
    public function receive_account_commission()
    {
        return $this->belongsTo('App\Models\AccountCommission');
    }
}
