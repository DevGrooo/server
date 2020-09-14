<?php

namespace App\Models;

use App\Imports\BasicImport;
use App\Imports\InvestorImport;
use App\Imports\ReportSR0044Import;

class FileImport extends Model
{
    const STATUS_NEW = 1; //Chưa xử lý
    const STATUS_PROCESSING = 2; //Đang xử lý
    const STATUS_PROCESSED = 3; //Đã xử lý

    const TYPE_INVESTOR = 'investors';
    const TYPE_TRANSACTION_FUND = 'transaction_funds';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'file_imports';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['type', 'file_name', 'file_path', 'total_line', 'processed_line', 'total_error', 'total_warning', 'status', 'created_by', 'updated_by'];

    protected $casts = [        
        'data' => 'array',
        'data_result' => 'array',
        'errors' => 'array',
        'warnings' => 'array',
    ];


    protected $rules = [
        'type' => 'required|string|max:255',
        'file_name' => 'required|string|max:255',
        'file_path' => 'required|string',
        'total_line' => 'integer',
        'processed_line' => 'integer',
        'total_error' => 'integer',
        'total_warning' => 'integer',
        'status' => 'required|integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    public function getClassImport() : BasicImport
    {
        if ($this->type == FileImport::TYPE_INVESTOR) {
            return new InvestorImport();
        } elseif ($this->type == FileImport::TYPE_TRANSACTION_FUND) {
            return new ReportSR0044Import();
        }
        return new BasicImport();
    }
}
