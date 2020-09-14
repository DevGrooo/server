<?php

namespace App\Models;

class Investor extends Model
{
    const STATUS_NEW = 1; //Mới tạo
    const STATUS_IMPORT_VSD = 2; //Đã import VSD
    const STATUS_CANCEL = 3; //Đã hủy
    const STATUS_CLOSED = 4; //Đã đóng

    const VSD_STATUS_NEW = 1; //Đợi kích hoạt
    const VSD_STATUS_ACTIVE = 2; //Đã kích hoạt
    const VSD_STATUS_REJECT = 3; //Bị từ chối
    const VSD_STATUS_SEND_MAIL = 4; //Đã gửi mail

    const SCALE_TYPE_PERSONAL = 1;
    const SCALE_TYPE_ORGANIZATION = 2;

    const GENDER_MALE = 'M';
    const GENDER_FEMALE = 'F';
    const GENDER_OTHER = 'O';

    const INVEST_TYPE_NORMAL = 1;
    const INVEST_TYPE_PROFESSION = 2;

    const ZONE_TYPE_INTERNAL = 1;
    const ZONE_TYPE_EXTERNAL = 2;

    const TRADING_ACCOUNT_TYPE_DIRECT = 1;
    const TRADING_ACCOUNT_TYPE_INDIRECT = 2;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'investors';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'file_import_id','fund_company_id', 'fund_distributor_id', 'fund_distributor_staff_id', 'referral_bank_id', 'trading_account_number',
        'trading_reference_number', 'trading_account_type', 'name', 'zone_type', 'scale_type', 'invest_type', 'country_id', 'birthday',
        'gender', 'id_type_id', 'id_number', 'id_issuing_date', 'id_issuing_place', 'id_expiration_date', 'permanent_address', 'permanent_country',
        'current_address', 'current_country_id', 'phone', 'fax', 'email', 'tax_id', 'tax_country_id', 'visa_number', 'visa_issuing_date',
        'visa_issuing_place', 'temporary_address', 're_fullname', 're_birthday', 're_gender', 're_position', 're_id_type_id', 're_id_number',
        're_id_issuing_date', 're_id_issuing_place', 're_id_expiration_date', 're_phone', 're_address', 're_country_id', 'au_fullname', 'au_id_type_id',
        'au_id_number', 'au_id_issuing_date', 'au_id_issuing_place', 'au_id_expiration_date', 'au_mail', 'au_phone', 'au_address', 'au_country_id',
        'au_start_date', 'au_end_date', 'fatca_link_auth', 'fatca_recode', 'fatca_funds', 'fatca1', 'fatca2', 'fatca3', 'fatca4', 'fatca5', 'fatca6',
        'fatca7', 'status', 'vsd_status', 'created_by', 'updated_by'
    ];

    protected $rules = [
        'file_import_id' => 'integer|exists:file_imports,id',
        'fund_company_id' => 'required|integer|row_exists',
        'fund_distributor_id' => 'required|integer|row_exists',
        'fund_distributor_staff_id' => 'integer',
        'referral_bank_id' => 'integer|row_exists',
        'trading_account_number' => 'required|regex:/(^[A-Z0-9]{3}[PCF][A-Z0-9]{6}$)/u|unique:investors',
        'trading_reference_number' => 'string|max:255',
        'trading_account_type' => 'required|integer',
        'name' => 'required|string|max:500',        
        'zone_type' => 'required|integer',
        'scale_type' => 'required|integer',
        'invest_type' => 'required|integer',
        'country_id' => 'integer|row_exists:country',
        'birthday' => 'date',
        'gender' => 'string',
        'id_type_id' => 'integer|exists:id_types,id',
        'id_number' => 'string|max:100',
        'id_issuing_date' => 'date',
        'id_issuing_place' => 'string|max:255',
        'id_expiration_date' => 'date',
        'permanent_address' => 'string|max:500',
        'permanent_country_id' => 'integer|row_exists:country',
        'current_address' => 'string|max:500',
        'current_country_id' => 'integer|row_exists:country',
        'phone' => 'string|max:50',
        'fax' => 'string|max:50',
        'email' => 'string|max:255',
        'tax_id' => 'string|max:100',
        'tax_country_id' => 'integer|row_exists:country',
        'visa_number' => 'string|max:50',
        'visa_issuing_date' => 'date',
        'visa_issuing_place' => 'string|max:255',
        'temporary_address' => 'string',
        're_fullname' => 'string|max:255',
        're_birthday' => 'date',
        're_gender' => 'integer',
        're_position' => 'string|max:255',
        're_id_type_id' => 'integer|exists:id_types,id',
        're_id_number' => 'string|max:50',
        're_id_issuing_date' => 'date',
        're_id_issuing_place' => 'string|max:255',
        're_id_expiration_date' => 'date',
        're_phone' => 'string|max:50',
        're_address' => 'string',
        're_country_id' => 'integer|row_exists:country',
        'au_fullname' => 'string|max:255',
        'au_id_type_id' => 'integer|exists:id_types,id',
        'au_id_number' => 'string|max:50',
        'au_id_issuing_date' => 'date',
        'au_id_issuing_place' => 'string|max:255',
        'au_id_expiration_date' => 'date',
        'au_email' => 'string|max:255',
        'au_phone' => 'string|max:50',
        'au_address' => 'string',
        'au_country_id' => 'integer|row_exists:country',
        'au_start_date' => 'date',
        'au_end_date' => 'date',
        'fatca_link_auth' => 'string|max:255',
        'fatca_recode' => 'string|max:255',
        'fatca_funds' => 'string',
        'fatca1' => 'integer',
        'fatca2' => 'integer',
        'fatca3' => 'integer',
        'fatca4' => 'integer',
        'fatca5' => 'integer',
        'fatca6' => 'integer',
        'fatca7' => 'integer',
        'status' => 'required|integer',
        'vsd_status' => 'required|integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];
    
    protected $casts = [
        'birthday' => \App\Casts\Date::class,
        're_birthday' => \App\Casts\Date::class,
        'id_issuing_date' => \App\Casts\Date::class,
        'id_expiration_date' => \App\Casts\Date::class,
        'visa_issuing_date' => \App\Casts\Date::class,
        're_id_issuing_date' => \App\Casts\Date::class,
        're_id_expiration_date' => \App\Casts\Date::class,
        'au_id_issuing_date' => \App\Casts\Date::class,
        'au_id_expiration_date' => \App\Casts\Date::class,
        'au_start_date' => \App\Casts\Date::class,
        'au_end_date' => \App\Casts\Date::class,
    ];

    public function setFatcaFundsAttribute($value) {
        if (!empty($value)) {
            $this->attributes['fatca_funds'] = implode(',', $value);
        } else {
            $this->attributes['fatca_funds'] = '';
        }
    }

    public function getFatcaFundsAttribute($value) {
        if (trim($value) != '') {
            return explode(',', $value);
        }
        return [];
    }

    public function setFundDistributorStaffIdAttribute($value) {
        $this->attributes['fund_distributor_staff_id'] = intval($value);
    }

    public function setTradingAccountNumberAttribute($value) {
        $this->attributes['trading_account_number'] = strtoupper($value);
    }

    public static function getListStatus() {
        return array(
            self::STATUS_NEW => trans('table.investor.status.new'),
            self::STATUS_CANCEL => trans('table.investor.status.cancel'),
            self::STATUS_CLOSED => trans('table.investor.status.closed'),
            self::STATUS_IMPORT_VSD => trans('table.investor.status.import_vsd'),
        );
    }

    public static function getListVsdStatus() {
        return array(
            self::VSD_STATUS_NEW => trans('table.investor.status.vsd_new'),
            self::VSD_STATUS_ACTIVE => trans('table.investor.status.vsd_active'),
            self::VSD_STATUS_REJECT => trans('table.investor.status.vsd_reject'),
            self::VSD_STATUS_SEND_MAIL => trans('table.investor.status.vsd_send_mail'),
        );
    }

    public static function getListScaleTypes() {
        return array(
            self::SCALE_TYPE_PERSONAL => trans('table.investor.scale_type.personal'),
            self::SCALE_TYPE_ORGANIZATION => trans('table.investor.scale_type.organization'),
        );
    }

    public static function getListGenders() {
        return array(
            self::GENDER_MALE => trans('table.investor.gender.male'),
            self::GENDER_FEMALE => trans('table.investor.gender.female'),
            // self::GENDER_OTHER => trans('table.investor.gender.other'),
        );
    }

    public static function getListInvestTypes() {
        return array(
            self::INVEST_TYPE_NORMAL => trans('table.investor.invest_type.normal'),
            self::INVEST_TYPE_PROFESSION => trans('table.investor.invest_type.profession'),
        );
    }

    public static function getListZoneTypes() {
        return array(
            self::ZONE_TYPE_INTERNAL => trans('table.investor.zone_type.internal'),
            self::ZONE_TYPE_EXTERNAL => trans('table.investor.zone_type.external'),
        );
    }

    public static function getListTradingAccountTypes() {
        return array(
            self::TRADING_ACCOUNT_TYPE_DIRECT => trans('table.investor.trading_account_type.direct'),
            self::TRADING_ACCOUNT_TYPE_INDIRECT => trans('table.investor.trading_account_type.indirect'),
        );
    }

    /**
     * The banks that belong to the investor.
     */
    public function investor_bank_accounts()
    {
        return $this->hasMany('App\Models\InvestorBankAccount');
    }

    public function users()
    {
        return $this->morphMany('App\Models\User', 'ref');
    }

    /**
     * Get the bank that owns the investor bank account.
     */
    public function fund_distributor()
    {
        return $this->belongsTo('App\Models\FundDistributor');
    }

    /**
     * Get the bank that owns the investor bank account.
     */
    public function id_type()
    {
        return $this->belongsTo('App\Models\IdType');
    }

    /**
     * Get the bank that owns the investor bank account.
     */
    public function re_id_type()
    {
        return $this->belongsTo('App\Models\IdType');
    }

    /**
     * Get the bank that owns the investor bank account.
     */
    public function au_id_type()
    {
        return $this->belongsTo('App\Models\IdType');
    }

    /**
     * Get the bank that owns the investor bank account.
     */
    public function re_country()
    {
        return $this->belongsTo('App\Models\Country');
    }


    /**
     * Get the bank that owns the investor bank account.
     */
    public function au_country()
    {
        return $this->belongsTo('App\Models\Country');
    }

	public static function isTradingAccountNumber($trading_account_number, $fund_distributor_code)
	{
		if (strpos($trading_account_number, $fund_distributor_code) === 0) {
            return true;
        }
        return false;
    }

    /**
     * NĐT đang khóa
     */
    public function isLock() {
        if ($this->status == self::STATUS_CANCEL || $this->status == self::STATUS_CLOSED) {
            return true;
        }
        return false;
    }
    
    /**
     * NĐT được phép giao dịch
     */
    public function isActive() {
        if ($this->status == self::STATUS_IMPORT_VSD) {
            if ($this->vsd_status == self::VSD_STATUS_ACTIVE || $this->vsd_status == self::VSD_STATUS_SEND_MAIL) {
                return true;
            }
        }
        return false;
    }
}
