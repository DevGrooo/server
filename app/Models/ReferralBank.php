<?php

namespace App\Models;

class ReferralBank extends Model
{
    const STATUS_ACTIVE = 1; //Đang sử dụng
    const STATUS_LOCK = 2; //Đang khóa

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'referral_banks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fund_company_id', 'name', 'trade_name', 'logo', 'status', 'created_by', 'updated_by'
    ];

    protected $casts = [
        'updated_time' => \App\Casts\Date::class,
    ];


    protected $rules = [
        'fund_company_id' => 'required|integer|row_exists',
        'name' => 'required|string|max:255',
        'trade_name' => 'required|string|max:255',
        'logo' => 'string|max:255',
        'status' => 'required|integer',
        'created_by' => 'integer',
        'updated_time' => 'date',
        'updated_by' => 'integer',
    ];

    /**
     * Get the fund company that owns the referral bank.
     */
    public function fund_company()
    {
        return $this->belongsTo('App\Models\FundCompany', 'fund_company_id');
    }
    public static function getListStatus() {
        return array(
            ReferralBank::STATUS_ACTIVE => trans('status.bank.active'),
            ReferralBank::STATUS_LOCK => trans('status.bank.lock'),
        );
    }
}
