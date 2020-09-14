<?php

namespace App\Models;

class TransactionSystem extends Model
{
    const STATUS_NEW = 1; //Mới tạo
    const STATUS_VERIFY = 2; //Đã xác nhận đang tạm giữ
    const STATUS_CANCEL = 3; //Đã hủy
    const STATUS_PERFORM = 4; //Đã hoàn thành

    const TYPE_DEPOSIT = 1; // Nạp
    const TYPE_TRANSFER = 2; // Chuyển
    const TYPE_WITHDRAW = 3; // Rút

    const REF_TYPE_CASHIN = 'cashin'; // phiếu thu
    const REF_TYPE_CASHOUT = 'cashout'; // phiếu chi
    const REF_TYPE_TRADING_ORDER = 'trading_order'; // lệnh giao dịch

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transaction_systems';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['type', 'ref_id', 'ref_type', 'send_account_system_id', 'receive_account_system_id', 'amount', 'send_fee', 'receive_fee', 'currency', 'status', 'verified_at', 'cancelled_at', 'performed_at', 'created_by', 'updated_by', 'verified_by', 'cancelled_by', 'performed_by'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $casts = [
        'verified_at' => \App\Casts\DateTime::class,
        'cancelled_at' => \App\Casts\DateTime::class,
        'performed_at' => \App\Casts\DateTime::class,
    ];

    protected $rules = [
        'type' => 'required|integer',
        'ref_id' => 'required|integer|row_exists',
        'ref_type' => 'required|string|max:50',
        'send_account_system_id' => 'required|integer|row_exists',
        'receive_account_system_id' => 'required|integer|row_exists',
        'amount' => 'required|numeric',
        'send_fee' => 'required|numeric',
        'receive_fee' => 'required|numeric',
        'currency' => 'required|string|max:10',
        'status' => 'required|integer',
        'verified_at' => 'date',
        'cancelled_at' => 'date',
        'performed_at' => 'date',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'verified_by' => 'integer',
        'cancelled_by' => 'integer',
        'performed_by' => 'integer',
    ];

    /**
     * Get the owning ref model.
     */
    public function ref()
    {
        return $this->morphTo();
    }

    /**
     * Get the account_systems record associated with the transaction_systems.
     */
    public function send_account_system()
    {
        return $this->hasOne('App\Models\AccountSystem');
    }



    /**
     * Get the account_systems record associated with the transaction_systems.
     */
    public function receive_account_system()
    {
        return $this->hasOne('App\Models\AccountSystem');
    }

    /**
     * @param TransactionSystem $model
     * @return double
     */
    public static function getSendFee($model)
    {
        return 0;
    }

    /**
     * @param TransactionSystem $model
     * @return double
     */
    public static function getReceiveFee($model)
    {
        return 0;
    }
}
