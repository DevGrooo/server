<?php

namespace App\Models;

class Cashin extends Model
{
    const STATUS_NEW = 1; //Mới tạo
    const STATUS_PAID = 2; //Đã thanh toán
    const STATUS_CANCEL = 3; //Đã hủy
    const STATUS_WAIT_COLLATE = 4; //Đợi đối chiếu
    const STATUS_PARTIALLY_PERFORM = 5; //Đã chuyển ngân một phần
    const STATUS_PERFORM = 6; //Đã chuyển ngân


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cashins';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['fund_distributor_id', 'fund_distributor_bank_account_id', 'fund_product_id', 'fund_product_type_id', 'supervising_bank_id', 
        'deposit_account_system_id', 'investor_id', 'target_account_system_id', 'statement_id', 'amount', 'amount_not_perform', 'amount_freezing', 'amount_available', 
        'fee', 'currency', 'receipt', 
        'bank_trans_note', 'status', 'fund_certificate_id', 'fund_company_id', 
        'bank_paid_at', 'paid_at', 'perform_at', 'created_by', 'updated_by', 'paid_by', 'perform_by'];

    protected $casts = [
        'bank_paid_at' => \App\Casts\DateTime::class,
        'paid_at' => \App\Casts\DateTime::class,
        'perform_at' => \App\Casts\DateTime::class,
    ];

    protected $rules = [
        'fund_company_id' => 'required|integer|row_exists',
        'fund_certificate_id' => 'required|integer|row_exists',
        'fund_distributor_id' => 'required|integer|row_exists',
        'fund_distributor_bank_account_id' => 'required|integer|row_exists',
        'fund_product_id' => 'required|integer|row_exists',
        'fund_product_type_id' => 'required|integer|row_exists',
        'supervising_bank_id' => 'required|integer|row_exists',
        'deposit_account_system_id' => 'required|integer|row_exists:account_systems',
        'investor_id' => 'integer|row_exists',
        'target_account_system_id' => 'integer|row_exists:account_systems',
        'statement_id' => 'integer|exists:statements,id',
        'amount' => 'required|numeric',
        'amount_not_perform' => 'required|numeric',
        'amount_available' => 'required|numeric',
        'amount_freezing' => 'required|numeric',
        'fee' => 'numeric',
        'currency' => 'required|string|max:10',
        'receipt' => 'string|max:50',
        'bank_paid_at' => 'date',
        'bank_trans_note' => 'string',
        'status' => 'required|integer',
        'paid_at' => 'date',
        'perform_at' => 'date',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'paid_by' => 'integer',
        'perform_by' => 'integer',
    ];


    /**
     * Get the fund_distributors record associated with the cashins.
     */
    public function fund_distributor()
    {
        return $this->belongsTo('App\Models\FundDistributor');
    }


    /**
     * Get the fund_distributors record associated with the cashins.
     */
    public function fund_certificate()
    {
        return $this->belongsTo('App\Models\FundCertificate');
    }

    /**
     * Get the fund_distributor_bank_accounts record associated with the cashins.
     */
    public function fund_distributor_bank_account()
    {
        return $this->belongsTo('App\Models\FundDistributorBankAccount');
    }



    /**
     * Get the supervising_banks record associated with the cashins.
     */
    public function supervising_bank()
    {
        return $this->belongsTo('App\Models\SupervisingBank');
    }



    /**
     * Get the account_systems record associated with the cashins.
     */
    public function deposit_account_system()
    {
        return $this->belongsTo('App\Models\AccountSystem');
    }



    /**
     * Get the investors record associated with the cashins.
     */
    public function investor()
    {
        return $this->belongsTo('App\Models\Investor');
    }

    /**
     * Get the investors record associated with the cashins.
     */
    public function fund_product()
    {
        return $this->belongsTo('App\Models\FundProduct');
    }

    /**
     * Get the account_systems record associated with the cashins.
     */
    public function target_account_system()
    {
        return $this->belongsTo('App\Models\AccountSystem');
    }

    public static function getListStatus()
    {
        return array(
            self::STATUS_NEW => trans('table.cashin.status.new'),
            self::STATUS_CANCEL => trans('table.cashin.status.cancel'),
            self::STATUS_PAID => trans('table.cashin.status.paid'),
            self::STATUS_WAIT_COLLATE => trans('table.cashin.status.wait_collate'),
            self::STATUS_PARTIALLY_PERFORM => trans('table.cashin.status.partially_perform'),
            self::STATUS_PERFORM => trans('table.cashin.status.perform'),
        );
    }
}
