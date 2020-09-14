<?php

namespace App\Models;

class FundDistributor extends Model
{
    const STATUS_ACTIVE = 1; //Đang sử dụng
    const STATUS_LOCK = 2; //Đang khóa

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'fund_distributors';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'fund_company_id', 'name', 'code', 'license_number', 'issuing_date', 'head_office',
        'phone', 'website', 'status', 'created_by', 'updated_by'
    ];

    protected $casts = [
        'issuing_date' => \App\Casts\Date::class,
    ];


    protected $rules = [
        'fund_company_id' => 'required|integer|row_exists',
        'name' => 'required|string|max:500',
        'code' => 'required|unique:fund_distributors|string|max:255',
        'license_number' => 'string|max:255',
        'issuing_date' => 'date',
        'head_office' => 'string',
        'phone' => 'string|max:50',
        'website' => 'string|max:255',
        'status' => 'required|integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];


    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = strtoupper($value);
    }

    /**
     * Get the investor bank accounts for the bank.
     */
    public function fund_company()
    {
        return $this->belongsTo('App\Models\FundCompany', 'fund_company_id');
    }

    public function fund_distributor_staff()
    {
        return $this->hasMany('App\Models\FundDistributorStaff', 'fund_distributor_id');
    }

    public function users()
    {
        return $this->morphMany('App\Models\User', 'ref');
    }

    public function investors()
    {
        return $this->hasMany('App\Models\Investor', 'fund_distributor_id');
    }

    public function fund_products()
    {
        return $this->belongsToMany('App\Models\FundProduct');
    }
}
