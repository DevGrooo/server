<?php

namespace App\Models;

class FundProductBuyHistory extends Model
{
    const STATUS_NOT_SOLD_OUT = 1; //Chưa bán hết
    const STATUS_SOLD_OUT = 2; //Đã bán hết


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'fund_product_buy_history';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['investor_id', 'fund_distributor_id', 'fund_company_id', 'fund_certificate_id', 'fund_product_id', 'investor_fund_product_id', 'tranding_order_id', 'buy_amount', 'sell_amount', 'current_amount', 'currency', 'status'];

    protected $rules = [
        'investor_id' => 'required|integer|row_exists',
        'fund_distributor_id' => 'required|integer|row_exists',
        'fund_company_id' => 'required|integer|row_exists',
        'fund_certificate_id' => 'required|integer|row_exists',
        'fund_product_id' => 'required|integer|row_exists',
        'investor_fund_product_id' => 'required|integer|row_exists',
        'tranding_order_id' => 'required|integer|row_exists',
        'buy_amount' => 'required|numeric',
        'sell_amount' => 'required|numeric',
        'current_amount' => 'required|numeric',
        'currency' => 'required|string|max:50',
        'status' => 'required|integer',
    ];

    /**
     * Get the investors record associated with the fund_product_buy_history.
     */
    public function investor()
    {
        return $this->belongsTo('App\Models\Investor');
    }



    /**
     * Get the fund_distributors record associated with the fund_product_buy_history.
     */
    public function fund_distributor()
    {
        return $this->belongsTo('App\Models\FundDistributor');
    }



    /**
     * Get the fund_company record associated with the fund_product_buy_history.
     */
    public function fund_company()
    {
        return $this->belongsTo('App\Models\FundCompany');
    }



    /**
     * Get the fund_certificates record associated with the fund_product_buy_history.
     */
    public function fund_certificate()
    {
        return $this->belongsTo('App\Models\FundCertificate');
    }



    /**
     * Get the fund_products record associated with the fund_product_buy_history.
     */
    public function fund_product()
    {
        return $this->belongsTo('App\Models\FundProduct');
    }



    /**
     * Get the investor_fund_products record associated with the fund_product_buy_history.
     */
    public function investor_fund_product()
    {
        return $this->belongsTo('App\Models\InvestorFundProduct');
    }



    /**
     * Get the tranding_orders record associated with the fund_product_buy_history.
     */
    public function tranding_order()
    {
        return $this->belongsTo('App\Models\TrandingOrder');
    }

    public function getHoldingPeriod($time_sell) {
        return floor((strtotime($time_sell) - $this->created_at->timestamp) / 86400);
    }
}
