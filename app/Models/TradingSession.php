<?php

namespace App\Models;

use Carbon\Carbon;

class TradingSession extends Model
{
    const STATUS_ACTIVE = 1; //Đang sử dụng
    const STATUS_TIME_UP = 2; //Hết thời gian đặt lệnh
    const STATUS_END = 3; //Đã kết thúc
    const STATUS_CANCEL = 4; //Đã hủy

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'trading_sessions';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['previous_trading_session_id', 'fund_company_id', 'trading_frequency_id', 'code', 'start_at', 'end_at', 'limit_order_at', 
        'nav', 'status', 'created_by', 'updated_by'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $casts = [
        'start_at' => \App\Casts\DateTime::class,
        'end_at' => \App\Casts\DateTime::class,
        'limit_order_at' => \App\Casts\DateTime::class,
        ];

    protected $rules = [
        'previous_trading_session_id' => 'required|integer',
        'fund_company_id' => 'required|integer|row_exists',
        'trading_frequency_id' => 'required|integer|row_exists',
        'code' => 'required|unique:trading_sessions,code',
        'start_at' => 'required|date',
        'end_at' => 'date',
        'limit_order_at' => 'required|date',
        'nav' => 'numeric',
        'status' => 'required|integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = strtoupper($value);
    }

    /**
     * Get the fund_company record associated with the trading_sessions.
     */
    public function fund_company()
    {
        return $this->belongsTo('App\Models\FundCompany');
    }


    /**
     * Get the trading_frequency record associated with the trading_sessions.
     */
    public function trading_frequency()
    {
        return $this->belongsTo('App\Models\TradingFrequency');
    }

    public function isAllowOrder()
    {
        if ($this->status == self::STATUS_ACTIVE && Carbon::createFromFormat('d/m/Y H:i:s', $this->limit_order_at)->greaterThan(Carbon::now())) {
            return true;
        }
        return false;
    }

    /**
     * @return TradingSession
     */
    public static function getForTradingOrder($fund_company_id, $investor_id, $fund_product_id, $created_at) {
        $trading_session = TradingSession::where('fund_company_id', $fund_company_id)
            ->where('limit_order_at', '>', $created_at)
            ->where('status', TradingSession::STATUS_ACTIVE)
            ->orderBy('start_at', 'ASC')->first();
        return $trading_session;
    }

    public static function getListStatus() {
        return array(
            TradingSession::STATUS_ACTIVE => trans('status.trading_session.active'),
            TradingSession::STATUS_TIME_UP => trans('status.trading_session.time_up'),
            TradingSession::STATUS_END => trans('status.trading_session.end'),
            TradingSession::STATUS_CANCEL => trans('status.trading_session.cancel'),
        );
    }
}
