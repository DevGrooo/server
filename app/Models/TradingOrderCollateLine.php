<?php

namespace App\Models;

class TradingOrderCollateLine extends Model
{
    const STATUS_NEW = 1; //Mới tạo
    const STATUS_VERIFY = 2; //Đã xác nhận, còn tiền thừa
    const STATUS_CANCEL = 3; //Đã hủy
    const STATUS_SUCCESS = 4; //Đã hoàn tất


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'trading_order_collate_lines';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['trading_order_collate_id', 'cashin_id', 'cashin_amount', 'overpayment', 'currency', 
        'deposit_transaction_system_id', 'status', 'created_by', 'updated_by'];




    protected $rules = [
        'trading_order_collate_id' => 'required|integer|exists:trading_order_collates,id',
        'cashin_id' => 'required|integer|exists:cashins,id',
        'cashin_amount' => 'required|numeric',
        'overpayment' => 'required|numeric',
        'bank_trans_note' => 'string',
        'currency' => 'required|string|max:10',
        'deposit_transaction_system_id' => 'integer|exists:transaction_systems,id',
        'status' => 'required|integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];




    /**
     * Get the trading_order_collates record associated with the trading_order_collate_lines.
     */
    public function trading_order_collate()
    {
        return $this->belongsTo('App\Models\TradingOrderCollate');
    }



    /**
     * Get the cashins record associated with the trading_order_collate_lines.
     */
    public function cashin()
    {
        return $this->belongsTo('App\Models\Cashin');
    }



    /**
     * Get the transaction_systems record associated with the trading_order_collate_lines.
     */
    public function deposit_transaction_system()
    {
        return $this->belongsTo('App\Models\TransactionSystem');
    }
}
