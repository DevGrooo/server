<?php

namespace App\Models;

class AccountSystem extends Model
{
    const STATUS_ACTIVE = 1; //Đang sử dụng
    const STATUS_LOCK = 2; //Đang khóa

    const REF_TYPE_FUND_DISTRIBUTOR = 'fund_distributor'; // Tài khoản ĐLPP
    const REF_TYPE_INVESTOR = 'investor'; // Tài khoản NĐT

    const MASTER_ID = 1;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'account_systems';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['fund_product_type_id', 'ref_id', 'ref_type', 'balance', 'balance_available', 
        'balance_freezing', 'currency', 'status', 'created_by', 'updated_by'];

    protected $rules = [
        'fund_product_type_id' => 'required|integer|row_exists',
        'ref_id' => 'required|integer|row_exists',
        'ref_type' => 'required|string|max:100',
        'balance' => 'required|numeric',
        'balance_available' => 'required|numeric',
        'balance_freezing' => 'required|numeric',
        'currency' => 'required|string|max:10',
        'status' => 'required|integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    public function ref()
    {
        return $this->morphTo();
    }

    /**
     * Get the fund_distributor_bank_accounts record associated with the account_systems.
     */
    public function fund_product_type()
    {
        return $this->hasOne('App\Models\FundProductType');
    }

    /**
     * @param integer $ref_id
     * @param integer $fund_product_type_id
     * @return AccountSystem
     */
    public static function getForInvestor($investor_id, $fund_product_type_id, $currency)
    {
        return AccountSystem::where('ref_id', $investor_id)
            ->where('ref_type', AccountSystem::REF_TYPE_INVESTOR)
            ->where('fund_product_type_id', $fund_product_type_id)
            ->where('currency', $currency)->first();
    }

    /**
     * @param integer $ref_id
     * @param integer $fund_product_type_id
     * @return AccountSystem
     */
    public static function getForFundDistributor($fund_distributor_id, $fund_product_type_id, $currency)
    {
        return AccountSystem::where('ref_id', $fund_distributor_id)
            ->where('ref_type', AccountSystem::REF_TYPE_FUND_DISTRIBUTOR)
            ->where('fund_product_type_id', $fund_product_type_id)
            ->where('currency', $currency)->first();
    }
}
