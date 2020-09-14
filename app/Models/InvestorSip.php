<?php

namespace App\Models;

class InvestorSip extends Model
{
    const STATUS_ACTIVE = 1; //Đang sử dụng
    const STATUS_PAUSE = 2; //Đang tạm dừng
    const STATUS_STOP = 3; //Đã đóng

    const SIP_TYPE_FIX = 1; //Cố định
    const SIP_TYPE_FLEXIBLE = 2; //Linh hoạt


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'investor_sips';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['fund_company_id', 'fund_certificate_id', 'fund_product_type_id', 'fund_product_id', 'fund_distributor_id', 'investor_id', 'payment_type', 'periodic_amount', 'start_at', 'reason_close', 'status', 'created_by', 'updated_by'];

    protected $casts = [
        'start_at' => \App\Casts\DateTime::class,
    ];


    protected $rules = [
        'fund_company_id' => 'required|integer|exists:fund_company,id',
        'fund_certificate_id' => 'required|integer|exists:fund_certificates,id',
        'fund_product_type_id' => 'required|integer|exists:fund_product_types,id',
        'fund_product_id' => 'required|integer|exists:fund_products,id',
        'fund_distributor_id' => 'required|integer|exists:fund_distributors,id',
        'investor_id' => 'required|integer|exists:investors,id',
        'payment_type' => 'required|integer',
        'periodic_amount' => 'required|numeric',
        'start_at' => 'required|date',
        'reason_close' => 'string',
        'status' => 'required|integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];




    /**
     * Get the fund_company record associated with the investor_sips.
     */
    public function fund_company()
    {
        return $this->belongsTo('App\Models\FundCompany');
    }



    /**
     * Get the fund_certificates record associated with the investor_sips.
     */
    public function fund_certificate()
    {
        return $this->belongsTo('App\Models\FundCertificate');
    }



    /**
     * Get the fund_product_types record associated with the investor_sips.
     */
    public function fund_product_type()
    {
        return $this->belongsTo('App\Models\FundProductType');
    }



    /**
     * Get the fund_products record associated with the investor_sips.
     */
    public function fund_product()
    {
        return $this->belongsTo('App\Models\FundProduct');
    }



    /**
     * Get the fund_distributors record associated with the investor_sips.
     */
    public function fund_distributor()
    {
        return $this->belongsTo('App\Models\FundDistributor');
    }



    /**
     * Get the investors record associated with the investor_sips.
     */
    public function investor()
    {
        return $this->belongsTo('App\Models\Investor');
    }

    /**
     * Get status name
     */
    public static function getListStatus()
    {
        return array (
            InvestorSip::STATUS_ACTIVE => trans('status.investor_sip.status_active'),
            InvestorSip::STATUS_PAUSE => trans('status.investor_sip.status_pause'),
            InvestorSip::STATUS_STOP => trans('status.investor_sip.status_stop')
        );
    }

    public static function getSipTypes() {
        return array(
            InvestorSip::SIP_TYPE_FIX => trans('status.fix'),
            InvestorSip::SIP_TYPE_FLEXIBLE => trans('status.flexible'),
        );
    }

}
