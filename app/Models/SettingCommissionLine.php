<?php

namespace App\Models;

class SettingCommissionLine extends Model
{
    const STATUS_NEW = 1; //Mới tạo
    const STATUS_REQUEST = 2; //Đợi duyệt
    const STATUS_REJECT = 3; //Từ chối
    const STATUS_ACCEPT = 4; //Đã duyệt
    const STATUS_LOCK = 5; //Đã khóa
    
    const TYPE_MAINTANCE = 1; // phí duy trì
    const TYPE_BUY = 2; // Phí mua

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'setting_commission_lines';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['setting_commission_id', 'ref_id', 'ref_type', 'fund_company_id', 'fund_certificate_id', 'fund_product_id', 'start_at', 'end_at', 'sell_commission', 'maintance_commission', 'min_amount', 'status'];

    protected $casts = [
        'start_at' => \App\Casts\DateTime::class,
        'end_at' => \App\Casts\DateTime::class,
    ];


    protected $rules = [
        'setting_commission_id' => 'required|integer|row_exists',
        'ref_id' => 'required|integer|row_exists',
        'ref_type' => 'required|string|max:100',
        'fund_company_id' => 'required|integer|row_exists',
        'fund_certificate_id' => 'required|integer|row_exists',
        'fund_product_id' => 'required|integer|row_exists',
        'start_at' => 'required|date',
        'end_at' => 'date',
        'sell_commission' => 'required|float',
        'maintance_commission' => 'required|float',
        'min_amount' => 'required|numeric',
        'status' => 'required|integer',
    ];

    /**
     * Get the setting_commissions record associated with the setting_commission_lines.
     */
    public function setting_commission()
    {
        return $this->belongsTo('App\Models\SettingCommission');
    }

    /**
     * Get the refs record associated with the setting_commission_lines.
     */
    public function ref()
    {
        return $this->belongsTo('App\Models\Ref');
    }



    /**
     * Get the fund_company record associated with the setting_commission_lines.
     */
    public function fund_company()
    {
        return $this->belongsTo('App\Models\FundCompany');
    }



    /**
     * Get the fund_certificates record associated with the setting_commission_lines.
     */
    public function fund_certificate()
    {
        return $this->belongsTo('App\Models\FundCertificate');
    }



    /**
     * Get the fund_products record associated with the setting_commission_lines.
     */
    public function fund_product()
    {
        return $this->belongsTo('App\Models\FundProduct');
    }

    /**
     * return BelongsTo
     */
    public function fund_distributor()
    {
        return $this->belongsTo('App\Models\FundDistributor', 'ref_id', 'id');
    }

    /**
     * @return SettingCommissionLine
     */
    public static function getSettingCommissionLine($type, $ref_id, $ref_type, $fund_company_id, $fun_certificate_id, $fund_product_id, $amount, $perform_at)
    {
        $line = SettingCommissionLine::where('ref_id', $ref_id)->where('ref_type', $ref_type)
            ->where('fund_company_id', $fund_company_id)
            ->where('fun_certificate_id', $fun_certificate_id)
            ->where('fund_product_id', $fund_product_id)
            ->where('type', $type)
            ->where('min_amount', '<=', $amount)
            ->where('start_at', '<=', $perform_at)->where('end_at', '>', $perform_at)
            ->where('status', SettingCommissionLine::STATUS_ACCEPT)
            ->orderBy('start_at', 'ASC')
            ->orderBy('id', 'DESC')
            ->first();
        return $line;
    }

    public static function getCommissionAmount($setting_commission_line, $amount)
    {
        return $setting_commission_line->commission_amount + round($setting_commission_line->commission_percent * $amount / 100, 0);
    }
}
