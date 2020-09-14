<?php

namespace App\Models;

class CashoutRequest extends Model
{
    const STATUS_REQUEST = 1; //Đợi duyệt
    const STATUS_REJECT = 2; //Từ chối
    const STATUS_ACCEPT = 3; //Duyệt
    const STATUS_PERFORM = 4; //Đã chi tiền

    const TYPE_ORDER_SELL = 1;
    const TYPE_ORDER_BUY = 2;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cashout_requests';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['type', 'transaction_system_id', 'amount', 'bank_id', 'account_holder', 'account_number', 'branch', 'description', 'status', 'accepted_at', 'rejected_at', 'created_by', 'updated_by', 'accepted_by', 'rejected_by'];


    protected $casts = [
        'accepted_at' => \App\Casts\Date::class,
        'rejected_at' => \App\Casts\Date::class,
    ];

    protected $rules = [
        'type' => 'required|integer',
        'transaction_system_id' => 'required|integer|row_exists',
        'amount' => 'required|numeric',
        'bank_id' => 'required|integer|row_exists',
        'account_holder' => 'required|string|max:255',
        'account_number' => 'required|string|max:50',
        'branch' => 'string|max:255',
        'description' => 'string',
        'status' => 'required|integer',
        'accepted_at' => 'numeric',
        'rejected_at' => 'numeric',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'accepted_by' => 'integer',
        'rejected_by' => 'integer',
    ];


    /**
     * Get the transaction_systems record associated with the cashout_request.
     */
    public function transaction_system()
    {
        return $this->hasOne('App\Models\TransactionSystem');
    }



    /**
     * Get the banks record associated with the cashout_request.
     */
    public function bank()
    {
        return $this->hasOne('App\Models\Bank');
    }
}
