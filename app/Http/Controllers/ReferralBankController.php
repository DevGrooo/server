<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\TableTrait;
use App\Models\ReferralBank;
use App\Services\Transactions\ReferralBankTransaction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * @group  Referral Bank
 */

class ReferralBankController extends Controller {
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
     * List Referral Bank
     *
     * Danh sách ngân hàng giới thiệu
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
    public function list (Request $request){
        $query_builder = ReferralBank::query();
        $data = $this->getData($request, $query_builder, '\App\Http\Resources\ReferralBankResource');
        return $this->responseSuccess($data);
    }

    /**
     * Create Referral Bank
     *
     * Thêm ngân hàng giới thiệu
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.fund_company_id integer required fund_company_id. Example: 1
     * @bodyParam params.name string required Name. Example: Vietcombank
     * @bodyParam params.trade_name string required Trade Name. Example: VIETCOMBANK
     * @bodyParam params.status integer required status. Example: 1
     *
     * @response {
     * "status": true,
     * "code": 200,
     * "messages": ""Success"",
     * "response": {bank_id}
     * }
     */
    public function create (Request $request)
    {
        $inputs = $this->validate($request, [
            'params.fund_company_id' => 'required|integer',
            'params.name' => 'required|string',
//            'params.logo' =>'string',
            'params.trade_name' => 'required|string',
            'params.status'   => 'required|integer',
        ]);
        $user = Auth::user();
        $inputs['params']['logo'] = $this->uploadImage($request);
        $inputs['params']['created_by'] = $user->id;
        return (new ReferralBankTransaction())->create($inputs['params'], true);
    }

    /**
     * Update Referral Bank
     *
     * Cập nhật ngân hàng giới thiệu
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.referral_bank_id integer required ID bank. Example: 1
     * @bodyParam params.fund_company_id integer required fund_company_id. Example: 1
     * @bodyParam params.name string required Name. Example: Vietcombank
     * @bodyParam params.trade_name string required Trade Name. Example: VIETCOMBANK
     * @bodyParam params.status integer required status. Example: 1
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
            'params.fund_company_id' => 'required|integer',
            'params.referral_bank_id' => 'required|integer',
            'params.name' => 'required|string',
            'params.trade_name' => 'required|string',
            'params.status'   => 'required|integer',
        ]);
        $user = Auth::user();
        $inputs['params']['updated_by'] = $user->id;
        return (new ReferralBankTransaction())->update($inputs['params'], true);
    }
    /**
     * Active Referral Bank
     *
     * Mở khóa ngân hàng giới thiệu
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.referral_bank_id integer required ID bank. Example: 1
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
            'params.referral_bank_id' => 'required|integer'
        ]);
        return (new ReferralBankTransaction())->active([
            'referral_bank_id' => $inputs['params']['referral_bank_id'],
            'updated_by' => auth()->user()->id
        ],true);
    }

    /**
     * Lock Referral Bank
     *
     * Khóa ngân hàng giới thiệu
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.referral_bank_id integer required ID bank. Example: 1
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
            'params.referral_bank_id' => 'required|integer'
        ]);
        return (new ReferralBankTransaction())->lock([
            'referral_bank_id' => $inputs['params']['referral_bank_id'],
            'updated_by' => auth()->user()->id
        ],true);
    }

    public function uploadImage(Request $request)
    {
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $time = Carbon::now()->format('YmdHis');
            $extension=$logo->getClientOriginalExtension();
            $image_name=$time.".".$extension;
            $filePath="/storage/images/referral_bank/".$image_name;
            $request->file('logo')->move($filePath, $image_name);
            return $filePath;
        }
    }
    /**
     * Detail Referral Bank
     *
     * Xem chi tiết ngân hàng giới thiệu
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.referral_bank_id integer required ID bank. Example: 1
     * @response {
     * "status": true,
     * "code": 200,
     * "messages": ""Success"",
     * "response": null
     * }
     */
    public function detail(Request $request)
    {
        $inputs = $this->validate($request, [
            'params.referral_bank_id' => 'required|integer'
        ]);
        $bank = ReferralBank::where('id', $inputs['params']['referral_bank_id'])->first();
        if ($bank) {
            return $this->responseSuccess($bank);
        } else {
            $this->error('Ngân hàng giới thiệu không tồn tại');
        }
    }

    /**
     * Bank get status
     *
     * Lấy danh sách trạng thái ngân hàng giới thiệu
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
        $list_status = ReferralBank::getListStatus();
        return $this->responseSuccess(ReferralBank::getArrayForSelectBox($list_status));
    }
}
