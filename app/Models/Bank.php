<?php

namespace App\Models;

class Bank extends Model
{
    const STATUS_ACTIVE = 1; //Đang sử dụng
    const STATUS_LOCK = 2; //Đang khóa

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'banks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'trade_name', 'code', 'bcardno', 'bcarddate', 'address', 'logo', 'status', 'created_by', 'updated_by'
    ];

    /**
     * Get the investor bank accounts for the bank.
     */
    public function investor_bank_accounts()
    {
        return $this->hasMany('App\Models\InvestorBankAccount', 'bank_id');
    }

    /**
     * The investors that belong to the bank.
     */
    public function investor()
    {
        return $this->belongsToMany('App\Models\Investor', 'investor_bank_accounts', 'bank_id', 'investor_id');
    }

    public static function getListStatus() {
        return array(
            Bank::STATUS_ACTIVE => trans('status.bank.active'),
            Bank::STATUS_LOCK => trans('status.bank.lock'),
        );
    }

    public static function getIdByCode($code)
    {
        $model = Bank::where('code', $code)->first();
        if ($model) {
            return $model->id;
        }
        return 0;
    }
}
