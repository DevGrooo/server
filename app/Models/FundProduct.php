<?php

namespace App\Models;

class FundProduct extends Model
{
    const STATUS_ACTIVE = 1; //Đang sử dụng
    const STATUS_LOCK = 2; //Đang khóa

    const FREQUENCY_TYPE_MONTH = 1;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'fund_products';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['fund_company_id', 'fund_certificate_id', 'name', 'frequency_type', 'minimum_period', 'code', 'description', 'status', 'created_by', 'updated_by'];

    protected $rules = [
        'fund_company_id' => 'required|integer|row_exists',
        'fund_certificate_id' => 'required|integer|row_exists',
        'name' => 'required|string|max:255',
        'code' => 'required|string|max:255',
        'frequency_type' => 'integer',
        'minimum_period' => 'integer',
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
     * Get the fund_company record associated with the fund_products.
     */
    public function fund_company()
    {
        return $this->belongsTo('App\Models\FundCompany');
    }

    /**
     * Get the fund_certificates record associated with the fund_products.
     */
    public function fund_certificate()
    {
        return $this->belongsTo('App\Models\FundCertificate');
    }

    /**
     * Get the fund_certificates record associated with the fund_products.
     */
    public function fund_product_type()
    {
        return $this->belongsTo('App\Models\FundProductType');
    }

    public static function getListStatus() {
        return array(
            FundProduct::STATUS_ACTIVE => trans('status.trading_frequency.active'),
            FundProduct::STATUS_LOCK => trans('status.trading_frequency.lock'),
        );
    }
}
