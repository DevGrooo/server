<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\TableTrait;
use App\Imports\ReportSR0044Import;
use App\Models\FileImport;
use App\Services\Transactions\FileImportTransaction;
use App\Services\Transactions\TransactionFundTransaction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\TransactionFund;

/**
 * @group  Transaction fund
 */
class TransactionFundController extends Controller
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
     * TransactionFund importFileExcel
     *
     * Xem chi tiết thông tin quỹ sản phẩm
     *
     * @bodyPram env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.file file required File import. Example: 1
     *
     * @response {
     * "status" : false,
     * "code" : 403,
     * "messages" : "Access denied",
     * "response" : null
     * }
     */
    public function importFileExcel(Request $request)
    {
        $inputs = $this->validate($request, [
            'params.file' => 'required|mimes:xls,xlsx,csv'
        ]);
        $file = $inputs['params']['file'];
        $file_name = $file->getClientOriginalName();
        $file_path = 'uploads/report/sr0044/'.date('YmdHis') . '_' . uniqid() . '.' . $file->extension();
        Storage::disk('local')->put($file_path, file_get_contents($file->getRealPath()));
        // get lines in file import
        $import = new ReportSR0044Import();
        Excel::import($import, $file_path);
        // save lines to DB
        return (new FileImportTransaction())->create([
            'type' => FileImport::TYPE_TRANSACTION_FUND,
            'file_name' => $file_name,
            'file_path' => $file_path,
            'lines' => $import->getLines(),
            'created_by' => auth()->user()->id,
        ], true);
    }


    /**
     * TransactionFund importFileExcel
     *
     * Xem chi tiết thông tin quỹ sản phẩm
     *
     * @bodyPram env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.file_import_id integer required File import. Example: 1
     *
     * @response {
     * "status" : false,
     * "code" : 403,
     * "messages" : "Access denied",
     * "response" : null
     * }
     */
    public function import(Request $request)
    {
        $inputs = $this->validate($request, [
            'params.file_import_id' => 'required|integer'
        ]);
        return (new TransactionFundTransaction)->import([
            'file_import_id' => $inputs['params']['file_import_id'],
            'created_by' => auth()->user()->id,
        ], true);
    }

    public function test()
    {
        $rules = array(
            'fundname' => 'string',
            'mbname' => 'required|string',
            'fullname' => 'required|string',
            'idcode' => ['required'],
            'dbcode' => 'required|regex:/(^[A-Z0-9]{3}$)/u|exists:fund_distributors,code',
            'iddate' => ['required'],
            'custodycd' => ['required'],
            'srexetype' => 'required|string',
            'dealtype' => ['required', 'regex:/(NS|NR)/u'],
            'exectype' => 'required|string',
            'txdate' => ['required'],
            'feeamt' => 'required|numeric',
            'taxamt' => 'required|numeric',
            'pv_symbol' => 'required|exists:trading_sessions,code',
            'matchamt' => 'required|numeric',
            'matchamtnr' => 'required|numeric',
            'matchamtns' => 'required|numeric',
            'orderamt' => 'required|numeric',
            'matchqttynr' => 'required|numeric',
            'matchqttyns' => 'required|numeric',
            'orderqtty' => 'required|numeric',
            'matchamtt' => 'required|numeric',
            'orderid' => 'required|string',
            'status' => 'required|string',
            'actype' => 'required|string',
            'feeamc' => 'required|numeric',
            'feedxx' => 'required|numeric',
            'feefund' => 'required|numeric',
            'feetotal' => 'required|numeric',
            'nav' => 'required|numeric',
            'totalnav' => 'required|numeric',
            'tradingdate' => ['required'],
            'name' => 'required|string',
            'symbol' => 'required|exists:fund_certificates,code',
            'feename' => 'required|string',
            'feeid' => 'required|exists:fund_products,code',
        );
        foreach ($rules as $key => $value) {
            echo '"'.$key.'": "'.strtoupper($key).'",'.chr(10);
        }
        $this->middleware('auth');
    }

    /**
     * Transaction Fund list
     *
     * Danh sách các quỹ giao dịch
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5"
     * @response {
     * "status" : true,
     * "code" : 200,
     * "messages" : "Success",
     * "response" : null
     * }
     */
    public function list (Request $request){
        $query_builder = TransactionFund::query();
        $data = $this->getData($request, $query_builder,'\App\Http\Resources\TransactionFundResource');
        return $this->responseSuccess($data);
    }
}
