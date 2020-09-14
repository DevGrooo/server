<?php

namespace App\Models;

class InvestorSipLine extends Model
{
    const STATUS_NEW = 1; //Chưa tạo lệnh
    const STATUS_ORDER_CREATED = 2; //Đã tạo lệnh chưa thanh toán
    const STATUS_PAID = 3; //Đã thanh toán


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'investor_sip_lines';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['investor_sip_id', 'fund_company_id', 'fund_certificate_id', 'fund_product_type_id', 'fund_product_id', 'fund_distributor_id', 'investor_id', 'period_index', 'period_start_at', 'period_end_at', 'amount', 'trading_session_id', 'trading_order_id', 'status', 'created_by', 'updated_by'];

    protected $casts = [
        'period_start_at' => \App\Casts\DateTime::class,
        'period_end_at' => \App\Casts\DateTime::class,
    ];


    protected $rules = [
        'investor_sip_id' => 'required|integer|exists:investor_sips,id',
        'fund_company_id' => 'required|integer|exists:fund_company,id',
        'fund_certificate_id' => 'required|integer|exists:fund_certificates,id',
        'fund_product_type_id' => 'required|integer|exists:fund_product_types,id',
        'fund_product_id' => 'required|integer|exists:fund_products,id',
        'fund_distributor_id' => 'required|integer|exists:fund_distributors,id',
        'investor_id' => 'required|integer|exists:investors,id',
        'period_index' => 'required|integer',
        'period_start_at' => 'date',
        'period_end_at' => 'date',
        'amount' => 'required|numeric',
        'trading_session_id' => 'integer|exists:trading_sessions,id',
        'trading_order_id' => 'integer|exists:trading_orders,id',
        'status' => 'required|integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];




    /**
     * Get the investor_sips record associated with the investor_sip_lines.
     */
    public function investor_sip()
    {
        return $this->belongsTo('App\Models\InvestorSip');
    }



    /**
     * Get the fund_company record associated with the investor_sip_lines.
     */
    public function fund_company()
    {
        return $this->belongsTo('App\Models\FundCompany');
    }



    /**
     * Get the fund_certificates record associated with the investor_sip_lines.
     */
    public function fund_certificate()
    {
        return $this->belongsTo('App\Models\FundCertificate');
    }



    /**
     * Get the fund_product_types record associated with the investor_sip_lines.
     */
    public function fund_product_type()
    {
        return $this->belongsTo('App\Models\FundProductType');
    }



    /**
     * Get the fund_products record associated with the investor_sip_lines.
     */
    public function fund_product()
    {
        return $this->belongsTo('App\Models\FundProduct');
    }



    /**
     * Get the fund_distributors record associated with the investor_sip_lines.
     */
    public function fund_distributor()
    {
        return $this->belongsTo('App\Models\FundDistributor');
    }



    /**
     * Get the investors record associated with the investor_sip_lines.
     */
    public function investor()
    {
        return $this->belongsTo('App\Models\Investor');
    }



    /**
     * Get the trading_sessions record associated with the investor_sip_lines.
     */
    public function trading_session()
    {
        return $this->belongsTo('App\Models\TradingSession');
    }



    /**
     * Get the trading_orders record associated with the investor_sip_lines.
     */
    public function trading_order()
    {
        return $this->belongsTo('App\Models\TradingOrder');
    }
}
