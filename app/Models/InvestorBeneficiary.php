<?php

namespace App\Models;

class InvestorBeneficiary extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'investor_bank_accounts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'investor_id', 'be_name', 'be_birthday', 'be_gender', 'be_id_type', 'be_id_number', 'be_id_issuing_date',
        'be_id_issuing_place', 'be_id_expiration_date', 'be_permanent_country_id', 'be_current_address', 'be_current_country_id',
        'be_current_address', 'be_current_country_id', 'be_phone', 'be_email', 'be_tax_id', 'be_tax_country_id', 'be_visa_number',
        'be_visa_issuing_date', 'be_visa_issuing_place', 'be_temporary_address', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'be_birthday' => \App\Casts\Date::class,
        'be_id_issuing_date' => \App\Casts\Date::class,
        'be_id_expiration_date' => \App\Casts\Date::class,
        'be_visa_issuing_date' => \App\Casts\Date::class,
        'updated_time' => \App\Casts\Date::class,
    ];


    protected $rules = [
        'investor_id' => 'required|integer|row_exists',
        'be_name' => 'string|max:500',
        'be_birthday' => 'date',
        'be_gender' => 'integer',
        'be_id_type' => 'integer',
        'be_id_number' => 'string|max:50',
        'be_id_issuing_date' => 'date',
        'be_id_issuing_place' => 'string|max:255',
        'be_id_expiration_date' => 'date',
        'be_permanent_address' => 'string',
        'be_permanent_country_id' => 'integer|row_exists',
        'be_current_address' => 'string',
        'be_current_country_id' => 'integer|row_exists',
        'be_phone' => 'string|max:50',
        'be_email' => 'string|max:255',
        'be_tax_id' => 'string|max:100',
        'be_tax_country_id' => 'integer|row_exists',
        'be_visa_number' => 'string|max:50',
        'be_visa_issuing_date' => 'date',
        'be_visa_issuing_place' => 'string|max:50',
        'be_temporary_address' => 'string',
        'created_by' => 'integer',
        'updated_time' => 'date',
        'updated_by' => 'integer',
    ];


    /**
     * Get the investor that owns the investor beneficiary.
     */
    public function investor()
    {
        return $this->belongsTo('App\Models\Investor', 'investor_id');
    }

    /**
     * Get the country record associated with the investor_beneficiary.
     */
    public function be_permanent_country()
    {
        return $this->belongsTo('App\Models\Country');
    }



    /**
     * Get the country record associated with the investor_beneficiary.
     */
    public function be_current_country()
    {
        return $this->belongsTo('App\Models\Country');
    }


    /**
     * Get the country record associated with the investor_beneficiary.
     */
    public function be_tax_country()
    {
        return $this->belongsTo('App\Models\Country');
    }
}
