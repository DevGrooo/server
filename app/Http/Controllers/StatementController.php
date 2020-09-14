<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Traits\TableTrait;
use App\Models\Statement;
use App\Models\StatementLine;

/**
 * @group  cashin
 */
class StatementController extends Controller
{
    use TableTrait;
    
    protected $statement_line;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Statement detail
     *
     * Chi tiết sao kê
     *
     * @bodyPram env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.statement_id integer required Statement ID. Example: 1      
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
            'params.statement_id' => 'required|integer',
        ]);
        $this->authorize('view', ['App\Models\Statement', $inputs['params']]);
        $model = Statement::find($inputs['params']['statement_id']);
        if ($model) {
            return $this->responseSuccess([
                'id' => $model->id,
                'file_name' => $model->file_name,
                'total_line' => $model->total_line,
                'processed_line' => $model->processed_line,
                'status' => $model->status,
                'created_at' => $model->created_at->toDateTimeString(),
            ]);
        } else {
            $this->error('Không tồn tại sao kê');
        }
    }

    /**
     * Statement Line
     *
     * Danh sách dòng trong sao kê
     *
     * @bodyPram env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.statement_id integer required Statement ID. Example: 1      
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
            'params.statement_id' => 'required|integer',
        ]);
        $this->authorize('view', ['App\Models\Statement', $inputs['params']]);
        $model = Statement::where('id', $inputs['params']['statement_id'])->where('status', Statement::STATUS_PROCESSED)->first();
        if (!$model) {
            $this->error('Không tồn tại sao kê');
        }
        $query_builder = StatementLine::where('statement_id', $model->id);
        return $this->responseSuccess($this->getData($request, $query_builder, '\App\Http\Resources\StatementLineResource'));
    }

    /**
     * Statement getLineStatus
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
        $list_status = StatementLine::getListStatus();
        return $this->responseSuccess(StatementLine::getArrayForSelectBox($list_status));
    }
}
