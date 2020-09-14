<?php

namespace App\Models;

class FundCertificate extends Model
{
    const STATUS_ACTIVE = 1; //Đang sử dụng
    const STATUS_LOCK = 2; //Đang khóa


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'fund_certificates';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['fund_company_id', 'name', 'code', 'description', 'status', 'created_by', 'updated_by'];


    protected $rules = [
        'fund_company_id' => 'required|integer|row_exists',
        'name' => 'required|string|max:255',
        'code' => 'required|string|max:255',
        'description' => 'string',
        'status' => 'required|integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = strtolower($value);
    }



    /**
     * Get the fund_company record associated with the fund_certificates.
     */
    public function fund_company()
    {
        return $this->hasOne('App\Models\FundCompany');
    }
}
