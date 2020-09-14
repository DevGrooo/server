<?php

namespace App\Models;

class FundDistributorStaff extends Model
{
    const STATUS_ACTIVE = 1; //Đang sử dụng
    const STATUS_LOCK = 2; //Đang khóa

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'fund_distributor_staffs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fund_company_id', 'fund_distributor_id', 'name', 'certificate_number', 'issuing_date',
        'phone', 'email', 'status', 'created_by', 'updated_by'
    ];

    protected $casts = [
        'issuing_date' => \App\Casts\Date::class,
    ];


    protected $rules = [
        'fund_company_id' => 'required|integer|row_exists',
        'fund_distributor_id' => 'required|integer|row_exists',
        'name' => 'required|string|max:255',
        'certificate_number' => 'string|max:255',
        'issuing_date' => 'date',
        'phone' => 'string|max:50',
        'email' => 'string|max:255',
        'status' => 'required|integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    /**
     * Get the fund_company record associated with the fund_distributor_staffs.
     */
    public function fund_company()
    {
        return $this->belongsTo('App\Models\FundCompany');
    }

    /**
     * Get the fund_distributors record associated with the fund_distributor_staffs.
     */
    public function fund_distributor()
    {
        return $this->belongsTo('App\Models\FundDistributor');
    }

    public function users()
    {
        return $this->morphMany('App\Models\User', 'ref');
    }
}
