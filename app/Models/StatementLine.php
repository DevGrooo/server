<?php

namespace App\Models;

use App\Services\Banks\StandardCharteredBank;

class StatementLine extends Model
{
    const STATUS_NEW = 1; //Chưa xử lý
    const STATUS_PROCESSING = 2; //Đang xử lý
    const STATUS_ERROR = 3; //Lỗi hoặc thiếu thông tin
    const STATUS_SUCCESS = 4; //Đã hoàn thành


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'statement_lines';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['statement_id', 'fund_company_id', 'fund_distributor_id', 'supervising_bank_id', 'investor_id', 'fund_product_id', 
        'data', 'data_result', 'bank_trans_note', 'bank_paid_at', 'cashin_id', 'cashin_receipt', 'errors', 'warnings', 'status', 'created_by', 'updated_by'];

    protected $casts = [
        'bank_paid_at' => \App\Casts\DateTime::class,
        'data' => 'array',
        'data_result' => 'array',
        'errors' => 'array',
        'warnings' => 'array',
    ];


    protected $rules = [
        'statement_id' => 'required|integer|exists:statements,id',
        'fund_company_id' => 'required|integer|exists:fund_company,id',
        'fund_distributor_id' => 'required|integer|exists:fund_distributors,id',
        'supervising_bank_id' => 'required|integer|exists:supervising_banks,id',
        'investor_id' => 'integer|exists:investors,id',
        'fund_product_id' => 'integer|exists:fund_products,id',
        'data' => 'array',
        'data_result' => 'array',
        'bank_trans_note' => 'string',
        'bank_paid_at' => 'date',
        'cashin_id' => 'integer|exists:cashins,id',
        'cashin_receipt' => 'string|max:50',
        'errors' => 'array',
        'warnings' => 'array',
        'status' => 'required|integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];




    /**
     * Get the statements record associated with the statement_lines.
     */
    public function statement()
    {
        return $this->belongsTo('App\Models\Statement');
    }



    /**
     * Get the fund_company record associated with the statement_lines.
     */
    public function fund_company()
    {
        return $this->belongsTo('App\Models\FundCompany');
    }



    /**
     * Get the fund_distributors record associated with the statement_lines.
     */
    public function fund_distributor()
    {
        return $this->belongsTo('App\Models\FundDistributor');
    }



    /**
     * Get the supervising_banks record associated with the statement_lines.
     */
    public function supervising_bank()
    {
        return $this->belongsTo('App\Models\SupervisingBank');
    }



    /**
     * Get the investors record associated with the statement_lines.
     */
    public function investor()
    {
        return $this->belongsTo('App\Models\Investor');
    }



    /**
     * Get the fund_products record associated with the statement_lines.
     */
    public function fund_product()
    {
        return $this->belongsTo('App\Models\FundProduct');
    }



    /**
     * Get the cashins record associated with the statement_lines.
     */
    public function cashin()
    {
        return $this->belongsTo('App\Models\Cashin');
    }

    public static function getListStatus()
    {
        return array(
            self::STATUS_NEW => trans('table.statement_line.status.new'),
            self::STATUS_PROCESSING => trans('table.statement_line.status.processing'),
            self::STATUS_ERROR => trans('table.statement_line.status.error'),
            self::STATUS_SUCCESS => trans('table.statement_line.status.success'),
        );
    }
}
