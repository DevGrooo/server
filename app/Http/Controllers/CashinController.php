<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Traits\TableTrait;
use App\Http\Resources\CashinResource;
use App\Models\Cashin;
use App\Models\StatementLine;
use App\Services\Banks\StandardCharteredBank;
use App\Services\Transactions\CashinTransaction;
use App\Services\Transactions\StatementLineTransaction;
use App\Services\Transactions\StatementTransaction;
use Illuminate\Support\Facades\Storage;

/**
 * @group  cashin
 */
class CashinController extends Controller
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
        $this->middleware('auth', ['except' => ['test'] ]);
    }

    /**
     * Cashin list
     *
     * Danh sách phiếu thu
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5"
     * 
     * @response {
     * "status" : true,
     * "code" : 200,
     * "messages" : "Success",
     * "response" : null
     * }
     */
    public function list (Request $request){
        $this->authorize('view', ['App\Models\Cashin']);
        $query_builder = Cashin::query();
        return $this->responseSuccess($this->getData($request, $query_builder, '\App\Http\Resources\CashinResource'));
    }

    /**
     * Cashin importStatement
     *
     * Import sao kê
     *
     * @bodyPram env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.fund_distributor_id integer required Fund Distributor ID. Example: 1
     * @bodyParam params.file integer required File import. Example: 
     *
     * @response {
     * "status" : false,
     * "code" : 403,
     * "messages" : "Access denied",
     * "response" : null
     * }
     */
    public function importStatement(Request $request)
    {
        $inputs = $this->validate($request, [
            'params.fund_distributor_id' => 'required|integer',
            'params.file' => 'required|mimes:xls,xlsx,csv'
        ]);
        $file = $inputs['params']['file'];
        $fund_distributor_id = $inputs['params']['fund_distributor_id'];
        $file_name = $file->getClientOriginalName();
        $file_path = 'uploads/statements/'.$fund_distributor_id.'/'.date('YmdHis') . '_' . uniqid() . '.' . $file->extension();
        Storage::disk('local')->put($file_path, file_get_contents($file->getRealPath()));
        return (new StatementTransaction())->create([
            'fund_distributor_id' => $fund_distributor_id, 
            'supervising_bank_id' => 1, 
            'file_name' => $file_name, 
            'file_path' => $file_path, 
            'created_by' => auth()->user()->id,
        ], true);
    }

    /**
     * Cashin get status
     *
     * Lấy danh sách trạng thái phiếu thu
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
    public function getStatus()
    {
        $list_status = Cashin::getListStatus();
        return $this->responseSuccess(Cashin::getArrayForSelectBox($list_status));
    }

    /**
     * Cashin detail
     *
     * Xem chi tiết thông tin phiếu thu
     *
     * @bodyPram env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.cashin_id integer required User ID. Example: 1
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
            'params.cashin_id' => 'required|integer'
        ]);
        $this->authorize('view', ['App\Models\Cashin', $inputs['params']]);
        $cashin= Cashin::where('id', $inputs['params']['cashin_id'])->first();
        if ($cashin) {
            return $this->responseSuccess(new CashinResource($cashin));
        } else {
            $this->error('Phiếu thu không tồn tại');
        }
    }

    /**
     * Cashin updateInvestor
     *
     * Xem chi tiết thông tin phiếu thu
     *
     * @bodyPram env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.cashin_id integer required Cashin ID. Example: 1
     * @bodyParam params.investor_id integer required Invester ID. Example: 1
     * 
     * @response {
     * "status" : false,
     * "code" : 403,
     * "messages" : "Access denied",
     * "response" : null
     * }
     */
    public function updateInvestor(Request $request)
    {
        $inputs = $this->validate($request, [
            'params.cashin_id' => 'required|integer',
            'params.investor_id' => 'required|integer',
        ]);
        $params = $inputs['params'];
        $this->authorize('update', ['App\Models\Cashin', $inputs['params']]);
        $params['updated_by'] = auth()->user()->id;
        return (new CashinTransaction())->updateInvestor($params, true);
    }
}
