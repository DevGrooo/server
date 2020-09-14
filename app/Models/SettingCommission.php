<?php

namespace App\Models;

use Exception;
use Illuminate\Support\Carbon;

class SettingCommission extends Model
{
    const STATUS_NEW = 1; //Mới tạo
    const STATUS_REQUEST = 2; //Đợi duyệt
    const STATUS_REJECT = 3; //Từ chối
    const STATUS_ACCEPT = 4; //Đã duyệt
    const STATUS_LOCK = 5; //Đã khóa

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'setting_commissions';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['ref_id', 'ref_type', 'fund_company_id', 'fund_certificate_id', 'fund_product_id', 'start_at', 'end_at', 'status', 'requested_at', 'rejected_at', 'accepted_at', 'locked_at', 'created_by', 'updated_by', 'requested_by', 'rejected_by', 'accepted_by', 'locked_by'];

    protected $casts = [
        'start_at' => \App\Casts\DateTime::class,
        'end_at' => \App\Casts\DateTime::class,
        'requested_at' => \App\Casts\DateTime::class,
        'rejected_at' => \App\Casts\DateTime::class,
        'accepted_at' => \App\Casts\DateTime::class,
        'locked_at' => \App\Casts\DateTime::class,
    ];

    // protected $rules = [
    //     'ref_id' => 'required|integer',
    //     'ref_type' => 'required|string|max:100',
    //     'fund_company_id' => 'required|integer|row_exists',
    //     'fund_certificate_id' => 'required|integer',
    //     'fund_product_id' => 'required|integer',
    //     'start_at' => 'required|date',
    //     'end_at' => 'date',
    //     'status' => 'required|integer',
    //     'requested_at' => 'date',
    //     'rejected_at' => 'date',
    //     'accepted_at' => 'date',
    //     'locked_at' => 'date',
    //     'created_by' => 'integer',
    //     'updated_by' => 'integer',
    //     'requested_by' => 'integer',
    //     'rejected_by' => 'integer',
    //     'accepted_by' => 'integer',
    //     'locked_by' => 'integer',
    // ];

    /**
     * Get the refs record associated with the setting_commissions.
     */
    public function ref()
    {
        return $this->belongsTo('App\Models\Ref');
    }



    /**
     * Get the fund_company record associated with the setting_commissions.
     */
    public function fund_company()
    {
        return $this->belongsTo('App\Models\FundCompany');
    }



    /**
     * Get the fund_certificates record associated with the setting_commissions.
     */
    public function fund_certificate()
    {
        return $this->belongsTo('App\Models\FundCertificate');
    }



    /**
     * Get the fund_products record associated with the setting_commissions.
     */
    public function fund_product()
    {
        return $this->belongsTo('App\Models\FundProduct');
    }

    /**
     * return HasMany
     */
    public function setting_commission_lines()
    {
        return $this->hasMany('App\Models\SettingCommissionLine', 'setting_commission_id', 'id');
    }

    /**
     * return BelongsTo
     */
    public function fund_distributor()
    {
        return $this->belongsTo('App\Models\FundDistributor', 'ref_id', 'id');
    }

    /**
     * @param TradingOrder $trading_order
     * @throws Exception
     * @return array [setting_commission_id, commission_amount, currency]
     * @return false
     */
    public static function getForTypeBuy(TradingOrder $trading_order)
    {
        if (!$trading_order->exec_type == TradingOrder::EXEC_TYPE_BUY) {
            throw new Exception('Lệnh mua không hợp lệ');
        }
        $setting_commission_line = SettingCommissionLine::getSettingCommissionLine(
            SettingCommissionLine::TYPE_BUY, 
            $trading_order->fund_distributor_id,
            'fund_distributor',
            $trading_order->fund_company_id,
            $trading_order->receive_fund_certificate_id,
            $trading_order->receive_fund_product_id,
            $trading_order->receive_match_amount,
            $trading_order->vsd_time_received
        );
        if ($setting_commission_line) {
            return [
                'setting_commission_id' => $setting_commission_line->setting_commission_id,
                'commission_amount' => SettingCommissionLine::getCommissionAmount($setting_commission_line, $trading_order->receive_match_amount),
                'currency' => $setting_commission_line->currency,
            ];
        }
        return false;
    }

   /**
     * @param FundDistributorProduct $fund_distributor_product
     * @param double $balance
     * @param int $day_holding
     * @throws Exception
     * @return array [setting_commission_id, commission_amount, currency]
     * @return false
     */
    public static function getForTypeMaintance($fund_distributor_product, $balance, $day_holding)
    {
        $setting_commission_line = SettingCommissionLine::getSettingCommissionLine(
            SettingCommissionLine::TYPE_MAINTANCE, 
            $fund_distributor_product->fund_distributor_id,
            'fund_distributor',
            $fund_distributor_product->fund_company_id,
            $fund_distributor_product->receive_fund_certificate_id,
            $fund_distributor_product->receive_fund_product_id,
            $balance,
            Carbon::now()
        );
        if ($setting_commission_line) {
            return [
                'setting_commission_id' => $setting_commission_line->setting_commission_id,
                'commission_amount' => SettingCommissionLine::getCommissionAmount($setting_commission_line, $balance),
                'currency' => $setting_commission_line->currency,
            ];
        }
        return false;
    }

}
