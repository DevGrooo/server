<?php

namespace App\Models;

class Country extends Model
{
    const STATUS_ACTIVE = 1; //Đang sử dụng
    const STATUS_LOCK = 2; //Đang khóa


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'country';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'code', 'status', 'created_by', 'updated_by'];


    protected $rules = [
        'name' => 'required|string|max:255',
        'code' => 'required|unique:country|string|max:255',
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
            Country::STATUS_ACTIVE => trans('status.bank.active'),
            Country::STATUS_LOCK => trans('status.bank.lock'),
        );
    }
}
