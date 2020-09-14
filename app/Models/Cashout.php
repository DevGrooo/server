<?php

namespace App\Models;


class Cashout extends Model
{
    const STATUS_NEW = 1; //Chưa chi tiền
    const STATUS_PERFORM = 2; //Đã chi tiền
    const STATUS_ERROR = 3; //Lỗi


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cashouts';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['cashout_request_id', 'transaction_system_id', 'amount', 'fee', 'bank_id', 'account_holder', 'account_number', 'branch', 'cashout_receipt', 'status', 'performed_at', 'created_by', 'updated_by', 'performed_by'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['performed_at'];


    protected $rules = [
        'cashout_request_id' => 'required|integer|row_exists',
        'transaction_system_id' => 'required|integer|row_exists',
        'amount' => 'required|numeric',
        'fee' => 'required|numeric',
        'bank_id' => 'required|integer|row_exists',
        'account_holder' => 'required|string|max:255',
        'account_number' => 'required|string|max:50',
        'branch' => 'string|max:255',
        'cashout_receipt' => 'string|max:50',
        'status' => 'required|integer',
        'performed_at' => 'numeric',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'performed_by' => 'integer',
    ];

    /**
     * Get the cashout_requests record associated with the cashouts.
     */
    public function cashout_request()
    {
        return $this->hasOne('App\Models\CashoutRequest');
    }



    /**
     * Get the transaction_systems record associated with the cashouts.
     */
    public function transaction_system()
    {
        return $this->hasOne('App\Models\TransactionSystem');
    }



    /**
     * Get the banks record associated with the cashouts.
     */
    public function bank()
    {
        return $this->hasOne('App\Models\Bank');
    }
}
