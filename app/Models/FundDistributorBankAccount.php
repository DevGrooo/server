<?php

namespace App\Models;

class FundDistributorBankAccount extends Model
{
    const STATUS_ACTIVE = 1; //Đang sử dụng
    const STATUS_LOCK = 2; //Đang khóa

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'fund_distributor_bank_accounts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['fund_product_id', 'fund_product_type_id', 'fund_distributor_id', 'fund_distributor_product_id', 'supervising_bank_id', 
    'fund_company_id', 'fund_certificate_id', 
    'account_system_id', 'account_holder', 'account_number', 'branch', 'status', 'created_by', 'updated_by'];

    protected $rules = [
        'fund_company_id' => 'required|integer|row_exists',
        'fund_certificate_id' => 'required|integer|row_exists',
        'fund_product_id' => 'required|integer|row_exists',
        'fund_product_type_id' => 'required|integer|row_exists',
        'fund_distributor_id' => 'required|integer|row_exists',
        'fund_distributor_product_id' => 'required|integer|row_exists',
        'supervising_bank_id' => 'required|integer|row_exists',
        'account_system_id' => 'required|unique:fund_distributor_bank_accounts|integer|row_exists',
        'account_holder' => 'string|max:255',
        'account_number' => 'string|max:50',
        'branch' => 'string|max:255',
        'status' => 'required|integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    public function setAccountHolderAttribute($value)
    {
        $this->attributes['account_holder'] = strtoupper($value);
    }

    /**
     * Get the fund_products record associated with the fund_distributor_bank_accounts.
     */
    public function fund_product()
    {
        return $this->belongsTo('App\Models\FundProduct');
    }



    /**
     * Get the fund_product_types record associated with the fund_distributor_bank_accounts.
     */
    public function fund_product_type()
    {
        return $this->belongsTo('App\Models\FundProductType');
    }



    /**
     * Get the fund_distributors record associated with the fund_distributor_bank_accounts.
     */
    public function fund_distributor()
    {
        return $this->belongsTo('App\Models\FundDistributor');
    }



    /**
     * Get the fund_distributor_products record associated with the fund_distributor_bank_accounts.
     */
    public function fund_distributor_product()
    {
        return $this->belongsTo('App\Models\FundDistributorProduct');
    }



    /**
     * Get the supervising_banks record associated with the fund_distributor_bank_accounts.
     */
    public function supervising_bank()
    {
        return $this->belongsTo('App\Models\SupervisingBank');
    }



    /**
     * Get the account_systems record associated with the fund_distributor_bank_accounts.
     */
    public function account_system()
    {
        return $this->belongsTo('App\Models\AccountSystem');
    }
}
