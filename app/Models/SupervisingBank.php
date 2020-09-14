<?php

namespace App\Models;

class SupervisingBank extends Model
{
    const STATUS_ACTIVE = 1; //Đang sử dụng
    const STATUS_LOCK = 2; //Đang khóa

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'supervising_banks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'trade_name', 'logo', 'status', 'created_by', 'updated_by'
    ];

    protected $rules = [
        'name' => 'required|string|max:255',
        'trade_name' => 'required|string|max:255',
        'logo' => 'string|max:255',
        'status' => 'required|integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    public function setTradeNameAttribute($value)
    {
        $this->attributes['trade_name'] = strtoupper($value);
    }
}
