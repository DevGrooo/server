<?php

namespace App\Http\Controllers;

use App\Models\FundDistributorProduct;
use App\Models\FundProduct;
use App\Models\InvestorFundProduct;
use Illuminate\Http\Request;
use App\Http\Resources\FundCertificateResource;
use App\Http\Controllers\Traits\DatatableTrait;
use App\Services\Transactions\FundCertificateTransaction;
use App\Models\FundCertificate;

/**
 * @group  FundCertificateApi
 */
class FundCertificateController extends Controller
{

    use DatatableTrait;

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
     * FundCertificate list
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     *
     * @response {
     * "status" : true,
     * "code" : 200,
     * "messages" : "Success",
     * "response" : null
     * }
     */
    public function list(Request $request)
    {
        return FundCertificateResource::collection(
            $this->getData(
                $request,
                FundCertificate::query(),
                ['name', 'code', 'status', 'created_at', 'updated_at']
            )
        );
    }

    /**
     * FundCertificate get list
     *
     * Lấy danh sách giấy chứng nhận quỹ đầu tư
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     *
     * @response {
     * "status" : true,
     * "code" : 200,
     * "messages" : "Success",
     * "response" : null
     * }
     */
    public function getlist(Request $request)
    {
        $certificate = FundCertificate::where('status', FundCertificate::STATUS_ACTIVE)->get();
        return $this->responseSuccess(FundCertificate::getArrayForSelectBox($certificate, 'id', 'name'));
    }

    /**
     * FundCertificate add
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.fund_company_id integer ID fund company. Example: 1
     * @bodyParam params.name string required Name product type. Example: SIP
     * @bodyParam params.code string required Code product type. Example: SIP
     * @bodyParam params.description string Description. Example: haoquang20111996@gmail.com
     * @bodyParam params.status integer Status. Example: 1
     *
     * @response {
     * "status" : true,
     * "code" : 200,
     * "messages" : "Success",
     * "response" : {fund_certificate_id}
     * }
     */
    public function add(Request $request)
    {
        $inputs = $this->validate($request, [
            'params.fund_company_id' => 'required|integer|exists:App\Models\FundCompany,id',
            'params.name'            => 'required|string',
            'params.code'            => 'required|string|unique:fund_certificates,code',
            'params.status'          => 'required|integer'
        ], [
            'params.fund_company_id.exists' => trans('table.fund_company.id.exists'),
            'params.code.unique'            => trans('table.fund_certificate.code.unique')
        ]);

        // $this->authorize('create', ['App\Models\FundCertificate', $inputs['params']]);

        return (new FundCertificateTransaction())->create($inputs['params'], true);
    }

    /**
     * FundCertificate update
     *
     * Cập nhật thông tin chứng chỉ quỹ
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.fund_certificate_id integer required ID fund certificate. Example: 1
     * @bodyParam params.fund_company_id integer ID fund company. Example: 1
     * @bodyParam params.name string required Name product type. Example: SIP
     * @bodyParam params.code string required Code product type. Example: SIP
     * @bodyParam params.description string Description. Example: haoquang20111996@gmail.com
     * @bodyParam params.status integer Status. Example: 1
     *
     * @response {
     * "status" : true,
     * "code" : 200,
     * "messages" : "Success",
     * "response" : null
     * }
     */
    public function update(Request $request)
    {
        $inputs = $this->validate($request, [
            'params.fund_certificate_id' => 'required|integer',
            'params.fund_company_id'     => 'required|integer|exists:App\Models\FundCompany,id',
            'params.name'                => 'required|string',
            'params.code'                => 'required|string|unique:fund_certificates,code,' . $request['params']['fund_certificate_id'],
            'params.status'              => 'required|integer'
        ], [
            'params.fund_company_id.exists' => 'Công ty quản lý quỹ không tồn tại',
            'params.code.unique'            => 'Mã chứng chỉ quỹ đã tồn tại',
        ]);

        // $this->authorize('update', ['App\Models\FundCertificate', $inputs['params']]);

        return (new FundCertificateTransaction())->update($inputs['params'], true);
    }

    /**
     * FundCertificate active
     *
     * Mở khóa chứng chỉ quỹ
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.fund_certificate_id integer required ID fund certificate. Example: 1
     *
     * @response {
     * "status" : true,
     * "code" : 200,
     * "messages" : "Success",
     * "response" : null
     * }
     */
    public function active(Request $request)
    {
        $inputs = $this->validate($request, [
            'params.fund_certificate_id' => 'required|integer|exists:App\Models\FundCertificate,id',
        ], [
            'params.fund_certificate_id.exists' => 'Chứng chỉ quỹ không tồn tại'
        ]);

        // $this->authorize('update', ['App\Models\FundCertificate', $inputs['params']]);

        return (new FundCertificateTransaction())->active($inputs['params'], true);
    }

    /**
     * FundCertificate lock
     *
     * Khóa chứng chỉ quỹ
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.fund_certificate_id integer required ID fund certificate. Example: 1
     *
     * @response {
     * "status" : true,
     * "code" : 200,
     * "messages" : "Success",
     * "response" : null
     * }
     */
    public function lock(Request $request)
    {
        $inputs = $this->validate($request, [
            'params.fund_certificate_id' => 'required|integer|exists:App\Models\FundCertificate,id',
        ], [
            'params.fund_certificate_id.exists' => 'Chứng chỉ quỹ không tồn tại'
        ]);

        // $this->authorize('update', ['App\Models\FundCertificate', $inputs['params']]);

        return (new FundCertificateTransaction())->lock($inputs['params'], true);
    }

    /**
     * FundCertificate detail
     *
     * Xem chi tiết chứng chỉ quỹ
     *
     * @bodyPram env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.fund_certificate_id integer required Fund Certificate ID. Example: 1
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
           'params.fund_certificate_id' => 'required|integer'
        ]);

        // $this->authorize('view', ['App\Models\FundCertificate', $inputs['params']]);

        $fundCertificate = FundCertificate::find($inputs['params']['fund_certificate_id']);
        if ($fundCertificate) {
            return $this->responseSuccess($fundCertificate);
        } else {
            $this->error('Chứng chỉ quỹ không tồn tại');
        }
    }


    /**
    * Fund certificate get fund product
    *
    * Lấy danh danh sách sản phẩm quỹ có thuộc loại quỹ
    *
    * @bodyParam env string required Enviroment client. Example: web
    * @bodyParam app_version string required App version. Example: 1.1
    * @bodyParam lang string required Language. Example: vi
    * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
    * @bodyParam params fund_certificate_id string requried
    *
    * @response {
    * "status": true,
    * "code": 200,
    * "messages": "Success",
    * "response": {
    * }
    */
    public function getFundProduct(Request $request)
    {
        $inputs = $this->validate($request, [
            'params.fund_certificate_id' => 'required'
        ]);
        $investor_fund_products = InvestorFundProduct::where('fund_certificate_id',$inputs['params']['fund_certificate_id'])->get();
        if ($investor_fund_products != "") {
            foreach ($investor_fund_products as $investor_fund_product) {
                $fund_products = FundProduct::where('id', $investor_fund_product->fund_product_id)->get();
                foreach ($fund_products as $value) {
                    $fund_product = FundProduct::find($value);
                    return $this->responseSuccess(FundProduct::getArrayForSelectBox($fund_product, 'id', 'name'));
                }
            }
        } else {
            $this->error('không tồn tại sản phẩm của nhà đầu tư có loại quỹ này !');
        }

    }

    public function getListByFundDistributorProduct(Request $request)
    {
        $inputs = $this->validate($request, [
           'params.fund_distributor_id' => 'integer'
        ]);
        $fund_distributor_products = FundDistributorProduct::where('fund_distributor_id',$inputs['params']['fund_distributor_id'])->get();
//        dd($fund_distributor_products);
        if ($fund_distributor_products != "") {
            foreach ($fund_distributor_products as $fund_distributor_product) {
                $fund_cestificates = FundCertificate::where('id', $fund_distributor_product->fund_certificate_id)->get();
//                dd($fund_cestificates);
                foreach ($fund_cestificates as $value) {
                    $fund_certificate = FundCertificate::find($value);
                    return $this->responseSuccess(FundCertificate::getArrayForSelectBox($fund_certificate, 'id', 'name'));
                }
            }
        } else {
            $this->error('không tồn tại sản phẩm của nhà đầu tư có loại quỹ này !');
        }

    }
}
