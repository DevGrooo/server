<?php

namespace App\Models;

class InvestorFundProduct extends Model
{
    const STATUS_ACTIVE = 1; //Đang sử dụng
    const STATUS_LOCK = 2; //Đang khóa


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'investor_fund_products';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['investor_id', 'fund_distributor_id', 'fund_company_id', 'fund_certificate_id', 'fund_product_id', 'balance', 'balance_available', 'balance_freezing', 'currency', 'status', 'created_by', 'update_by'];




    protected $rules = [
        'investor_id' => 'required|integer|row_exists',
        'fund_distributor_id' => 'required|integer|row_exists',
        'fund_company_id' => 'required|integer|row_exists',
        'fund_certificate_id' => 'required|integer|row_exists',
        'fund_product_id' => 'required|integer|row_exists',
        'balance' => 'required|numeric',
        'balance_available' => 'required|numeric',
        'balance_freezing' => 'required|numeric',
        'currency' => 'required|string|max:50',
        'status' => 'required|integer',
        'created_by' => 'integer',
        'update_by' => 'integer',
    ];




    /**
     * Get the investors record associated with the investor_fund_products.
     */
    public function investor()
    {
        return $this->belongsTo('App\Models\Investor');
    }



    /**
     * Get the fund_distributors record associated with the investor_fund_products.
     */
    public function fund_distributor()
    {
        return $this->belongsTo('App\Models\FundDistributor');
    }



    /**
     * Get the fund_company record associated with the investor_fund_products.
     */
    public function fund_company()
    {
        return $this->belongsTo('App\Models\FundCompany');
    }



    /**
     * Get the fund_certificates record associated with the investor_fund_products.
     */
    public function fund_certificate()
    {
        return $this->belongsTo('App\Models\FundCertificate');
    }



    /**
     * Get the fund_products record associated with the investor_fund_products.
     */
    public function fund_product()
    {
        return $this->belongsTo('App\Models\FundProduct');
    }

    public static function getlistStatus() {
        return array(
            InvestorFundProduct::STATUS_ACTIVE => trans('status.investor_fund_product.active'),
            InvestorFundProduct::STATUS_LOCK => trans('status.investor_fund_product.lock')
        );
    }
}
