<?php

namespace App\Models;

class Statement extends Model
{
    const STATUS_NEW = 1; //Chưa xử lý
    const STATUS_PROCESSING = 2; //Đang xử lý
    const STATUS_PROCESSED = 3; //Đã xử lý


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'statements';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['fund_company_id', 'fund_distributor_id', 'supervising_bank_id', 'file_name', 'file_path', 'total_line', 'processed_line', 'status', 'created_by', 'updated_by'];




    protected $rules = [
        'fund_company_id' => 'required|integer|row_exists',
        'fund_distributor_id' => 'required|integer|row_exists',
        'supervising_bank_id' => 'required|integer|row_exists',
        'file_name' => 'required|string|max:255',
        'file_path' => 'required|string',
        'total_line' => 'integer',
        'processed_line' => 'integer',
        'status' => 'required|integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];




    /**
     * Get the fund_company record associated with the statements.
     */
    public function fund_company()
    {
        return $this->belongsTo('App\Models\FundCompany');
    }



    /**
     * Get the fund_distributors record associated with the statements.
     */
    public function fund_distributor()
    {
        return $this->belongsTo('App\Models\FundDistributor');
    }



    /**
     * Get the supervising_banks record associated with the statements.
     */
    public function supervising_bank()
    {
        return $this->belongsTo('App\Models\SupervisingBank');
    }
}
