<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\TableTrait;
use App\Models\InvestorSip;
use App\Services\Transactions\InvestorSipTransaction;
use Illuminate\Http\Request;

/*
 * @group InvestorSipApi
 */
class InvestorSipController extends Controller
{
    use TableTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Sip Get List
     *
     * Danh sach cac lenh SIP cua NDT
     *
     * "@bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * "@response {
     * "status"": true,
     * "code"": 200,
     * "messages"": ""Success"",
     * "response"": null
     * }
     */

    public function list(Request $request) {
        $query_build = InvestorSip::query();
        $data = $this->getData($request, $query_build, 'App\Http\Resources\InvestorSipResource');
        return $this->responseSuccess($data);
    }

    /**
     * Create SIP
     *
     * Them lenh SIP cho NDT
     *
     * "@bodyParam env string required Enviroment client. Example: web
     *  @bodyParam app_version string required App version. Example: 1.1
     *  @bodyParam lang string required Language. Example: vi
     *  @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     *  @bodyParam params.trading_account_number string required Trading Account Number. Example: 701C123456
     *  @bodyParam params.fund_product_id integer required ID Product. Example: 1
     *  @bodyParam params.payment_type integer required Payment Type. Example: 1
     *  @bodyParam params.periodic_amount double required Periodic Amount. Example: 1000000"
     * "@response {
     * "status": true,
     * "code": 200,
     * "messages": ""Success"",
     * "response": { ""investor_sip_id"": 1 }
     */

    public function create(Request $request) {
        $inputs = $this->validate($request, [
            'params.fund_company_id' => 'required',
            'params.fund_certificate_id' => 'required',
            'params.fund_product_type_id' => 'required',
            'params.fund_product_id' => 'required',
            'params.fund_distributor_id' => 'required',
            'params.investor_id' => 'required',
            'params.payment_type' => 'required',
            'params.periodic_amount' => 'required',
            'params.status' => 'required',
            'params.created_by' => 'required'
        ]);
        $params = $inputs['params'];
        //$this->authorize('create', ['App\Models\InvestorSip', $params]);
        return (new InvestorSipTransaction())->create($params, true);
    }

    public function getSipType() {
        $typeSips = InvestorSip::getSipTypes();
        return $this->responseSuccess(InvestorSip::getArrayForSelectBox($typeSips));
    }

    public function getInvestorSipDetail(Request $request) {
        $inputs = $this->validate($request, [
            'params.investor_sip_id' => 'required|integer'
        ]);
        $investorSip = InvestorSip::findOrFail($inputs['params']['investor_sip_id']);
        if ($investorSip) {
            return $this->responseSuccess($investorSip);
        } else {
            return $this->error('Investor sip không tồn tại');
        }
    }

    /**
     * "@bodyParam env string required Enviroment client. Example: web
     *  @bodyParam app_version string required App version. Example: 1.1
     *  @bodyParam lang string required Language. Example: vi
     *  @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     *  @bodyParam params.investor_sip_id integer required investor_sip_id. Example: 1
     *  @bodyParam params.payment_type integer required Payment Type. Example: 1
     *  @bodyParam params.periodic_amount double required Periodic Amount. Example: 1000000"
     *
     * "@response {
     *  "status"": true,
     *  "code"": 200,
     *  "messages"": ""Success"",
     *  "response"": null
     * }"
     */

    public function update(Request $request) {
        $inputs = $this->validate($request, [
            'params.id' => 'integer',
            'params.fund_company_id' => 'required',
            'params.fund_certificate_id' => 'required',
            'params.fund_product_type_id' => 'required',
            'params.fund_product_id' => 'required',
            'params.fund_distributor_id' => 'required',
            'params.investor_id' => 'required',
            'params.payment_type' => 'required',
            'params.periodic_amount' => 'required',
            'params.status' => 'required',
            'params.created_by' => 'required'
        ]);
        $params = $inputs['params'];
        //$this->authorize('create', ['App\Models\InvestorSip', $params]);
        return (new InvestorSipTransaction())->update($params, true);
    }
}
