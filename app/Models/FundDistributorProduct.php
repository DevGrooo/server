<?php

namespace App\Models;

class FundDistributorProduct extends Model
{
    const STATUS_ACTIVE = 1; //Đang sử dụng
    const STATUS_LOCK = 2; //Đang khóa

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'fund_distributor_products';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['fund_company_id', 'fund_certificate_id', 'fund_distributor_id', 'fund_product_id', 'fund_product_type_id', 'fund_distributor_bank_account_id', 'status', 'created_by', 'updated_by'];


    protected $rules = [
        'fund_company_id' => 'required|integer|row_exists',
        'fund_certificate_id' => 'required|integer|row_exists',
        'fund_distributor_id' => 'required|integer|row_exists',
        'fund_product_type_id' => 'required|integer|row_exists',
        'fund_product_id' => 'required|integer|row_exists',
        'fund_distributor_bank_account_id' => 'required|integer|row_exists',
        'status' => 'required|integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];


    /**
     * Get the fund_distributors record associated with the fund_distributor_products.
     */
    public function fund_company()
    {
        return $this->belongsTo('App\Models\FundCompany');
    }

    /**
     * Get the fund_distributors record associated with the fund_distributor_products.
     */
    public function fund_distributor()
    {
        return $this->belongsTo('App\Models\FundDistributor');
    }

    /**
     * Get the fund_distributors record associated with the fund_distributor_products.
     */
    public function fund_distributor_bank_account()
    {
        return $this->belongsTo('App\Models\FundDistributorBankAccount');
    }

    /**
     * Get the fund_products record associated with the fund_distributor_products.
     */
    public function fund_product()
    {
        return $this->belongsTo('App\Models\FundProduct');
    }
    
    /**
     * Get the fund_products record associated with the fund_distributor_products.
     */
    public function fund_product_type()
    {
        return $this->belongsTo('App\Models\FundProductType');
    }
}
