<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Traits\TableTrait;
use App\Jobs\ProcessFileImportLineJob;
use App\Models\FileImport;
use App\Models\FileImportLine;
use App\Rules\BankAccountNumberRule;
use App\Rules\IdNumberRule;
use App\Rules\TradingAccountNumberRule;
use App\Rules\VNDateRule;

/**
 * @group  File import
 */
class FileImportController extends Controller
{
    use TableTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['test'] ]);
    }

    /**
     * File import detail
     *
     * Chi tiết file
     *
     * @bodyPram env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.file_import_id integer required FileImport ID. Example: 1      
     *
     * @response {
     * "status" : false,
     * "code" : 403,
     * "messages" : "Access denied",
     * "response" : null
     * }
     */
    public function detail(Request $request)
    {
        $inputs = $this->validate($request, [
            'params.file_import_id' => 'required|integer',
        ]);
        $this->authorize('view', ['App\Models\FileImport', $inputs['params']]);
        $model = FileImport::find($inputs['params']['file_import_id']);
        if ($model) {
            return $this->responseSuccess([
                'id' => $model->id,
                'file_name' => $model->file_name,
                'total_line' => $model->total_line,
                'processed_line' => $model->processed_line,
                'total_error' => $model->total_error,
                'total_warning' => $model->total_warning,
                'status' => $model->status,
                'created_at' => $model->created_at->toDateTimeString(),
            ]);
        } else {
            $this->error('Không tồn tại sao kê');
        }
    }

    /**
     * FileImport Line
     *
     * Danh sách dòng trong sao kê
     *
     * @bodyPram env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.file_import_id integer required FileImport ID. Example: 1      
     *
     * @response {
     * "status" : false,
     * "code" : 403,
     * "messages" : "Access denied",
     * "response" : null
     * }
     */
    public function line(Request $request)
    {
        $inputs = $this->validate($request, [
            'params.file_import_id' => 'required|integer',
        ]);
        $this->authorize('view', ['App\Models\FileImport', $inputs['params']]);
        $model = FileImport::where('id', $inputs['params']['file_import_id'])->where('status', FileImport::STATUS_PROCESSED)->first();
        if (!$model) {
            $this->error('Không tồn tại sao kê');
        }
        $query_builder = FileImportLine::where('file_import_id', $model->id);
        return $this->responseSuccess($this->getData($request, $query_builder, '\App\Http\Resources\FileImportLineResource'));
    }

    /**
     * FileImport getLineStatus
     *
     * Lấy danh sách trạng thái xử lý sao kê
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     *
     * @response {
     * "status": true,
     * "code": 200,
     * "messages": "Success",
     * "response": {
     * }
     */
    public function getLineStatus()
    {
        $list_status = FileImportLine::getListStatus();
        return $this->responseSuccess(FileImportLine::getArrayForSelectBox($list_status));
    }

    public function test()
    {
        // $line = FileImportLine::find(1);
        // $job = new ProcessFileImportLineJob($line);
        // $job->onConnection('sync')->onQueue('imports');
        // dispatch($job);
    }
}
