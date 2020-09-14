<?php

namespace App\Models;

class FileImportLine extends Model
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
    protected $table = 'file_import_lines';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['file_import_id', 'data', 'data_result', 'warnings', 'errors', 'status'];




    protected $rules = [
        'file_import_id' => 'required|integer|exists:file_imports,id',
        'data' => 'string',
        'data_result' => 'string',
        'warnings' => 'string',
        'errors' => 'string',
        'status' => 'required|integer',
    ];

    protected $casts = [
        'data' => 'array',
        'data_result' => 'array',
        'errors' => 'array',
        'warnings' => 'array',
    ];


    /**
     * Get the file_imports record associated with the file_import_lines.
     */
    public function file_import()
    {
        return $this->belongsTo('App\Models\FileImport');
    }

    public static function getListStatus()
    {
        return array(
            self::STATUS_NEW => trans('table.file_import_line.status.new'),
            self::STATUS_PROCESSING => trans('table.file_import_line.status.processing'),
            self::STATUS_ERROR => trans('table.file_import_line.status.error'),
            self::STATUS_SUCCESS => trans('table.file_import_line.status.success'),
        );
    }
}
