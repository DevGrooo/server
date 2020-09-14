<?php

namespace App\Models;

class FundCompany extends Model
{
    const STATUS_ACTIVE = 1; //Đang sử dụng
    const STATUS_LOCK = 2; //Đang khóa
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'fund_company';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'code', 'address', 'phone', 'website', 'status', 'created_by', 'updated_by'
    ];

    /**
     * Get the fund company that owns the referral bank.
     */
    public function refferal_bank()
    {
        return $this->hasMany('App\Models\ReferralBank', 'fund_company_id');
    }

    /**
     * Get the fund company that owns the investor.
     */
    public function investor()
    {
        return $this->hasMany('App\Models\Investor', 'fund_company_id');
    }

    /**
     * Get the fund company that owns the investor.
     */
    public function fund_distributor()
    {
        return $this->hasMany('App\Models\FundDistributor', 'fund_company_id');
    }

    public function users()
    {
        return $this->morphMany('App\Models\User', 'ref');
    }
}
