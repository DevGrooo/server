<?php

namespace App\Models;

class TradingOrderFeeSell extends Model
{
    const STATUS_NEW = 1; //Mới tạo
    const STATUS_REQUEST = 2; //Đợi duyệt
    const STATUS_REJECT = 3; //Từ chối
    const STATUS_ACCEPT = 4; //Đã duyệt
    const STATUS_LOCK = 5; //Đã khóa


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'trading_order_fee_sells';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['fund_company_id', 'fund_certificate_id', 'fund_product_id', 'investor_id', 'start_at', 'end_at', 'holding_period', 'fee_amount', 'fee_percent', 'status', 'requested_at', 'rejected_at', 'accepted_at', 'locked_at', 'created_by', 'updated_by', 'requested_by', 'rejected_by', 'accepted_by', 'locked_by'];

    protected $casts = [
        'start_at' => \App\Casts\Date::class,
        'end_at' => \App\Casts\DateTime::class,
        'requested_at' => \App\Casts\DateTime::class,
        'rejected_at' => \App\Casts\DateTime::class,
        'accepted_at' => \App\Casts\DateTime::class,
        'locked_at' => \App\Casts\DateTime::class,
    ];


    protected $rules = [
        'fund_company_id' => 'required|integer|row_exists',
        'fund_certificate_id' => 'required|integer|row_exists',
        'fund_product_id' => 'required|integer|row_exists',
        'investor_id' => 'required|integer|row_exists',
        'start_at' => 'required|date',
        'end_at' => 'date',
        'holding_period' => 'required|integer',
        'fee_amount' => 'required|numeric',
        'fee_percent' => 'required|float',
        'status' => 'required|integer',
        'requested_at' => 'date',
        'rejected_at' => 'date',
        'accepted_at' => 'date',
        'locked_at' => 'date',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'requested_by' => 'integer',
        'rejected_by' => 'integer',
        'accepted_by' => 'integer',
        'locked_by' => 'integer',
    ];




    /**
     * Get the fund_company record associated with the trading_order_fee_sell.
     */
    public function fund_company()
    {
        return $this->belongsTo('App\Models\FundCompany');
    }



    /**
     * Get the fund_certificates record associated with the trading_order_fee_sell.
     */
    public function fund_certificate()
    {
        return $this->belongsTo('App\Models\FundCertificate');
    }



    /**
     * Get the fund_products record associated with the trading_order_fee_sell.
     */
    public function fund_product()
    {
        return $this->belongsTo('App\Models\FundProduct');
    }



    /**
     * Get the investors record associated with the trading_order_fee_sell.
     */
    public function investor()
    {
        return $this->belongsTo('App\Models\Investor');
    }

    public static function getFee($vsd_time_perform, $holding_period, $amount) 
    {
        return 0;
    }

    public static function getListStatus() {
        return array(
            TradingOrderFeeSell::STATUS_NEW => trans('status.trading_order_fee_buy.active'),
            TradingOrderFeeSell::STATUS_LOCK => trans('status.trading_order_fee_buy.lock'),
        );
    }
}
