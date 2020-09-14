<?php

namespace App\Models;


class CashinReceipt extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cashin_receipts';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['cashin_id', 'supervising_bank_id', 'receipt', 'created_by', 'updated_by'];

    protected $rules = [
        'cashin_id' => 'required|unique:cashin_receipts|integer|row_exists',
        'supervising_bank_id' => 'required|integer|row_exists',
        'receipt' => 'required|unique:cashin_receipts|string|max:50',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    /**
     * Get the cashins record associated with the cashin_receipts.
     */
    public function cashin()
    {
        return $this->hasOne('App\Models\Cashin');
    }


    /**
     * Get the supervising_banks record associated with the cashin_receipts.
     */
    public function supervising_bank()
    {
        return $this->hasOne('App\Models\SupervisingBank');
    }
}
