<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\TableTrait;
use App\Models\Bank;
use App\Services\Transactions\BankTransaction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;


/**
 * @group  Bank
 */
class BankController extends Controller
{
    use TableTrait;

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
     * Bank Get list
     *
     * Lấy danh sách công ty quản lý quỹ
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
    public function getList()
    {
        $rows = Bank::where('status', Bank::STATUS_ACTIVE)->get();
        return $this->responseSuccess(Bank::getArrayForSelectBox($rows, 'id', 'name'));
    }

    /**
     * Setting list bank
     *
     * Danh sách ngân hàng
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
     * "response": null
     * }
     */
     public function list(Request $request)
     {
         $query_builder = Bank::query();
         $data = $this->getData($request, $query_builder, '\App\Http\Resources\BankResource');
         return $this->responseSuccess($data);
     }

     /**
      * Setting add bank
      *
      * Thêm ngân hàng
      *
      * @bodyParam env string required Enviroment client. Example: web
      * @bodyParam app_version string required App version. Example: 1.1
      * @bodyParam lang string required Language. Example: vi
      * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
      * @bodyParam params.name string required Name. Example: Vietcombank
      * @bodyParam params.trade_name string required Trade Name. Example: VIETCOMBANK
      *
      * @response {
      * "status": true,
      * "code": 200,
      * "messages": ""Success"",
      * "response": {bank_id}
      * }
      */
      public function create(Request $request)
      {
          $inputs = $this->validate($request, [
              'params.name' => 'required|string',
              'params.trade_name' => 'required|string',
              'params.logo' =>'string',
              'params.status'   => 'required|integer',
          ]);
          $user = Auth::user();
          $inputs['params']['logo'] = $this->uploadImage($request);
//          dd($inputs['params']['logo'])
          $inputs['params']['created_by'] = $user->id;
          return (new BankTransaction())->create($inputs['params'], true);
      }

    /**
     * Setting update bank
     *
     * Cập nhật ngân hàng
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.bank_id integer required ID bank. Example: 1
     * @bodyParam params.name string required Name. Example: Vietcombank
     * @bodyParam params.trade_name string required Trade Name. Example: VIETCOMBANK
     *
     * @response {
     * "status": true,
     * "code": 200,
     * "messages": ""Success"",
     * "response": null
     * }
     */
    public function update(Request $request)
    {
        $inputs = $this->validate($request, [
           'params.bank_id' => 'required|integer',
           'params.name' => 'required|string',
           'params.trade_name' => 'required|string',
        ]);
        $user = Auth::user();
        $inputs['params']['updated_by'] = $user->id;
        return (new BankTransaction())->update($inputs['params'], true);
    }

    /**
     * Setting active bank
     *
     * Mở khóa ngân hàng
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.bank_id integer required ID bank. Example: 1
     *
     * @response {
     * "status": true,
     * "code": 200,
     * "messages": ""Success"",
     * "response": null
     * }
     */
    public function active(Request $request)
    {
        $inputs = $this->validate($request, [
           'params.bank_id' => 'required|integer'
        ]);
        return (new BankTransaction())->active($inputs['params'], true);
    }

    /**
     * Setting lock bank
     *
     *  khóa ngân hàng
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.bank_id integer required ID bank. Example: 1
     * @response {
     * "status": true,
     * "code": 200,
     * "messages": ""Success"",
     * "response": null
     * }
     */
    public function lock(Request $request)
    {
        $inputs = $this->validate($request, [
            'params.bank_id' => 'required|integer'
        ]);
        return (new BankTransaction())->lock($inputs['params'], true);
    }

    public function uploadImage(Request $request)
    {
            if ($request->hasFile('logo')) {
                $logo = $request->file('logo');
                $time = Carbon::now()->format('YmdHis');
                $extension=$logo->getClientOriginalExtension();
                $image_name=$time.".".$extension;
                $filePath="/storage/images/bank/".$image_name;
                $request->file('logo')->move($filePath, $image_name);

                return $filePath;
            }
    }

    public function detail(Request $request)
    {
        $inputs = $this->validate($request, [
            'params.bank_id' => 'required|integer'
        ]);
        $bank = Bank::where('id', $inputs['params']['bank_id'])->first();
        if ($bank) {
            return $this->responseSuccess($bank);
        } else {
            $this->error('Ngân hàng không tồn tại');
        }
    }

    /**
     * Bank get status
     *
     * Lấy danh sách trạng thái ngân hàng
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
        $list_status = Bank::getListStatus();
        return $this->responseSuccess(Bank::getArrayForSelectBox($list_status));
    }
}
