<?php

namespace App\Models;

use Exception;
use Illuminate\Support\Carbon;

class TradingFrequency extends Model
{
    const STATUS_ACTIVE = 1; //Đang sử dụng
    const STATUS_LOCK = 2; //Đang khóa

    const TYPE_WDAY = 1;
    const TYPE_MDAY = 2;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'trading_frequency';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['fund_company_id', 'type', 'name', 'wday', 'mday', 'cut_off_date', 'cut_off_hour', 'cut_off_time', 'status', 'created_by', 'updated_by'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */

    protected $rules = [
        'fund_company_id' => 'required|integer|row_exists',
        'type' => 'required|integer',
        'name' => 'required|string|max:255',
        'wday' => 'integer',
        'mday' => 'integer',
        'cut_off_date' => 'required|integer',
        'cut_off_hour' => 'required|string|max:5',
        'cut_off_time' => 'required|string|max:50',
        'status' => 'required|integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    /**
     * Get the fund_company record associated with the fund_products.
     */
    public function fund_company()
    {
        return $this->belongsTo('App\Models\FundCompany');
    }

    /**
     * Return true if user active
     * @return boolean
     */
    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public static function getListStatus() {
        return array(
            TradingFrequency::STATUS_ACTIVE => trans('status.trading_frequency.active'),
            TradingFrequency::STATUS_LOCK => trans('status.trading_frequency.lock'),
        );
    }

    /**
     * @param Carbon $date
     * @return Carbon
     */
    public function getStartAt(Carbon $date)
    {        
        if ($this->type == self::TYPE_MDAY) {
            if ($date->day < $this->mday) {
                $month = $date->month;
                $year = $date->year;
            } else {
                $month = $date->month + 1;
                $year = $date->year;
                if ($month > 12) {
                    $month = 1;
                    $year++;
                }                
            }
            return Carbon::create($year, $month, $this->mday, 0, 0, 0);
        } elseif ($this->type == self::TYPE_WDAY) {
            if ($date->dayOfWeek < $this->wday) {
                $mday = $date->day + ($this->wday - $date->dayOfWeek);
            } else {
                $mday = $date->day + ($this->wday - $date->dayOfWeek) + 7;
            }
            return Carbon::create($date->year, $date->month, $mday, 0, 0, 0);
        } else {
            throw new Exception('Loại tần xuất giao dịch không được hỗ trợ');
        }
    }

    /**
     * @param Carbon $start_at
     * @return Carbon
     */
    public function getEndAt(Carbon $start_at) {        
        return $start_at->copy()->addDay();
    }

    /**
     * @param Carbon $start_at
     * @return Carbon
     */
    public function getLimitOrderAt(Carbon $start_at) {        
        list($hour, $minute) = explode(':', $this->cut_off_hour, 2);
        return $start_at->copy()->subDays($this->cut_off_date)->addHours(intval($hour))->addMinutes(intval($minute));
    }
}
