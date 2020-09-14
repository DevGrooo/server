<?php

namespace App\Models;

class FundDistributorProductBalance extends Model
{


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'fund_distributor_product_balance';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['fund_distributor_id', 'fund_product_id', 'fund_distributor_product_id', 'trading_session_id', 'balance', 'created_by', 'updated_by'];


    protected $rules = [
        'fund_distributor_id' => 'required|integer|row_exists',
        'fund_product_id' => 'required|integer|row_exists',
        'fund_distributor_product_id' => 'required|integer|row_exists',
        'trading_session_id' => 'required|integer|row_exists',
        'balance' => 'required|numeric',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];


    /**
     * Get the fund_distributors record associated with the fund_distributor_product_balance.
     */
    public function fund_distributor()
    {
        return $this->belongsTo('App\Models\FundDistributor');
    }



    /**
     * Get the fund_products record associated with the fund_distributor_product_balance.
     */
    public function fund_product()
    {
        return $this->belongsTo('App\Models\FundProduct');
    }



    /**
     * Get the trading_sessions record associated with the fund_distributor_product_balance.
     */
    public function trading_session()
    {
        return $this->belongsTo('App\Models\TradingSession');
    }

    public static function getBalanceAfterEndTradingSession(TradingSession $trading_session, FundDistributorProduct $fund_distributor_product)
    {
        $previous_balance = self::getPreviousBalance($trading_session, $fund_distributor_product);
        $fluctuation_balance = self::getFluctuationBalance($trading_session, $fund_distributor_product);
        return $previous_balance + $fluctuation_balance;
    }

    public static function getFluctuationBalance(TradingSession $trading_session, FundDistributorProduct $fund_distributor_product)
    {
        $receive_order = TradingOrder::select(['SUM(receive_match_amount) AS total_amount'])
            ->where('fund_distributor_id', $fund_distributor_product->fund_distributor_id)
            ->where('receive_fund_product_id', $fund_distributor_product->fund_product_id)
            ->where('status', TradingOrder::STATUS_PERFORM)->first();
        $send_order = TradingOrder::select(['SUM(send_match_amount) AS total_amount'])
            ->where('fund_distributor_id', $fund_distributor_product->fund_distributor_id)
            ->where('send_fund_product_id', $fund_distributor_product->fund_product_id)
            ->where('status', TradingOrder::STATUS_PERFORM)->first();
        return $receive_order->total_amount - $send_order->total_amount;
    }

    public static function getPreviousBalance(TradingSession $trading_session, FundDistributorProduct $fund_distributor_product)
    {
        if ($trading_session->previous_trading_session_id > 0) {
            $previous = self::where('fund_distributor_product_id', $fund_distributor_product->id)
                ->where('trading_session_id', $trading_session->previous_trading_session_id)
                ->first();
            if ($previous) {
                return $previous->balance;
            }
        }
        return 0;
    }
}
