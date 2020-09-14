<?php

namespace App\Models;

class AccountCommission extends Model
{
    const STATUS_ACTIVE = 1; //Đang sử dụng
    const STATUS_LOCK = 2; //Đang khóa

    const MASTER_ID = 1;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'account_commissions';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['ref_id', 'ref_type', 'balance', 'balance_available', 'balance_freezing', 'balance_waiting', 'status', 'created_by', 'updated_by'];




    protected $rules = [
        'ref_id' => 'required|integer|row_exists',
        'ref_type' => 'required|string|max:100',
        'balance' => 'required|numeric',
        'balance_available' => 'required|numeric',
        'balance_freezing' => 'required|numeric',
        'balance_waiting' => 'required|numeric',
        'status' => 'required|integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];


    public function ref()
    {
        return $this->morphTo();
    }
}
