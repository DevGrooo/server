<?php

namespace App\Models;

class TradingOrderCollate extends Model
{
    const STATUS_NOT_ENOUGHT = 1; //Chưa đủ tiền
    const STATUS_VERIFY = 2; //Đã xác nhận
    const STATUS_WAIT_CONFIRM = 3; //Đợi NĐT phản hồi
    const STATUS_SUCCESS = 4; // Đã hoàn tất
    const STATUS_CANCEL = 5; //Đã hủy

    const COMPARISON_MATCH = 1;
    const COMPARISON_NOT_MATCH = 2;

    const KEEP_FOR_NEXT = 1;
    const NOT_KEEP_FOR_NEXT = 2;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'trading_order_collates';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['trading_order_id', 'trading_order_amount', 'total_cashin_amount', 'investor_account_system_id', 'investor_id', 'fund_product_id', 
        'trading_session_id', 'balance', 'overpayment', 'currency', 'comparison_result', 
        'keep_for_next', 'withdraw_transaction_system_id', 'status', 'created_by', 'updated_by', 'verified_at', 'verified_by'];

    protected $casts = [
        'verified_at' => \App\Casts\DateTime::class,
    ];


    protected $rules = [
        'trading_order_id' => 'required|unique:trading_order_collates,trading_order_id|exists:trading_orders,id',
        'trading_order_amount' => 'required|numeric',
        'total_cashin_amount' => 'numeric',
        'investor_id' => 'required|integer',
        'investor_account_system_id' => 'required|integer',
        'fund_product_id' => 'required|integer',
        'trading_session_id' => 'required|integer',
        'balance' => 'numeric',
        'overpayment' => 'numeric',
        'currency' => 'required|string|max:10',
        'comparison_result' => 'required|integer',        
        'keep_for_next' => 'integer',        
        'withdraw_transaction_system_id' => 'integer|exists:transaction_systems,id',
        'status' => 'required|integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'verified_at' => 'date',
        'verified_by' => 'integer',
    ];

    /**
     * Get the trading_orders record associated with the trading_order_collates.
     */
    public function trading_order()
    {
        return $this->belongsTo('App\Models\TradingOrder');
    }



    /**
     * Get the cashins record associated with the trading_order_collates.
     */
    public function cashin()
    {
        return $this->belongsTo('App\Models\Cashin');
    }



    /**
     * Get the transaction_systems record associated with the trading_order_collates.
     */
    public function deposit_transaction_system()
    {
        return $this->belongsTo('App\Models\TransactionSystem');
    }



    /**
     * Get the transaction_systems record associated with the trading_order_collates.
     */
    public function withdraw_transaction_system()
    {
        return $this->belongsTo('App\Models\TransactionSystem');
    }
}
