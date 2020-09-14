<?php

namespace App\Models;

class FundProductType extends Model
{
    const STATUS_ACTIVE = 1; //Đang sử dụng
    const STATUS_LOCK = 2; //Đang khóa


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'fund_product_types';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'code', 'description', 'status', 'created_by', 'updated_by'];




    protected $rules = [
        'name' => 'required|string|max:255',
        'code' => 'required|string|max:50',
        'description' => 'string',
        'status' => 'required|integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];


    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = strtoupper($value);
    }

    public static function getListStatus() {
        return array(
          FundProductType::STATUS_ACTIVE => trans('status.fund_product_type.active'),
          FundProductType::STATUS_LOCK => trans('status.fund_product_type.lock'),
        );
    }
}
