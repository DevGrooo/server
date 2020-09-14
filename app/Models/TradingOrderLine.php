<?php

namespace App\Models;

class TradingOrderLine extends Model
{


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'trading_order_lines';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['trading_order_id', 'trading_order_buy_id', 'sell_amount', 'fee', 'currency'];

    protected $rules = [
        'trading_order_id' => 'required|integer|row_exists',
        'trading_order_buy_id' => 'required|integer|row_exists',
        'sell_amount' => 'required|numeric',
        'fee' => 'required|numeric',
        'currency' => 'required|string|max:50',
    ];

    /**
     * Get the trading_orders record associated with the trading_order_lines.
     */
    public function trading_order()
    {
        return $this->hasOne('App\Models\TradingOrder');
    }



    /**
     * Get the trading_order_buy record associated with the trading_order_lines.
     */
    public function trading_order_buy()
    {
        return $this->hasOne('App\Models\TradingOrderBuy');
    }
}
