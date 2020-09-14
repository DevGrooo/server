<?php

namespace App\Models;

class InvestorBankAccount extends Model
{
    const STATUS_ACTIVE = 1; //Đang sử dụng
    const STATUS_LOCK = 2; //Đang khóa

    const DEFAULT = 1;
    const NOT_DEFAULT = 2;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'investor_bank_accounts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'bank_id', 'fund_company_id', 'fund_distributor_id', 'investor_id', 'account_holder', 'account_number', 'branch', 'description', 'is_default',
        'status', 'created_by', 'updated_by'
    ];

    protected $rules = [
        'bank_id' => 'required|integer|row_exists', 
        'fund_company_id' => 'required|integer|row_exists',
        'fund_distributor_id' => 'required|integer|row_exists',
        'investor_id' => 'required|integer|row_exists',
        'account_holder' => 'required|string|max:255',
        'account_number' => 'required|string|max:50',
        'branch' => 'string|max:255',
        'description' => 'string|max:500',
        'status' => 'required|integer',
        'created_by' => 'integer',
        'updated_by' => 'integer', 
    ];

    protected $rule_messages = [
        'bank_id.row_exists' => 'Ngân hàng không tồn tại',
        'investor_id.row_exists' => 'Nhà đầu tư không tồn tại',
    ];

    /**
     * Get the bank that owns the investor bank account.
     */
    public function bank()
    {
        return $this->belongsTo('App\Models\Bank', 'bank_id');
    }

    /**
     * Get the bank that owns the investor bank account.
     */
    public function fund_company()
    {
        return $this->belongsTo('App\Models\FundCompany', 'fund_company_id');
    }

    /**
     * Get the bank that owns the investor bank account.
     */
    public function fund_distributor()
    {
        return $this->belongsTo('App\Models\FundDistributor');
    }

    /**
     * Get the bank that owns the investor bank account.
     */
    public function investor()
    {
        return $this->belongsTo('App\Models\Investor');
    }
}
