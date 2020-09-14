<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FundDistributor;
use App\Models\SettingCommissionLine;
use App\Http\Controllers\Traits\TableTrait;
use App\Services\Transactions\SettingCommissionTransaction;

/**
 * @group  SettingCommissionApi
 */
class SettingCommissionController extends Controller
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
     * SettingCommission list
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
        $dir = 'asc';
        $flagOrderBy = false;
        if (isset($request['params']['orderBy']) && $request['params']['orderBy'] === 'fund_distributor.name') {
            if ($request['params']['ascending'] === 1 ) {
                $dir = 'desc';
            }
            $flagOrderBy = true;
        }

        $query_builder = SettingCommissionLine::with([
            'fund_distributor' => function ($q) use ($dir) {
                $q->select('name', 'id');
                $q->orderBy('name', $dir);
            }
        ]);

        $data = $this->getData(
            $request,
            $query_builder,
            '\App\Http\Resources\SettingCommissionLineResource',
            ['id', 'start_at', 'ref_id', 'min_amount', 'sell_commission', 'maintance_commission_amount'],
            false,
            $flagOrderBy
        );

        return $this->responseSuccess($data);
    }

    /**
     * SettingCommission create
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.fund_distributor_id integer required Fund distributor id. Example: 1
     * @bodyParam params.fund_product_id  integer required Fund product id . Example: 1
     * @bodyParam params.start_at timestamp required Start apply setting commission. Example: 2020-07-20 14:00:00
     * @bodyParam params.lines.*.min_amount required double Minimum amount apply setting. Example: 0
     * @bodyParam params.lines.*.sell_commission float required Sell Commission. Example: 0.01
     * @bodyParam params.lines.*.maintance_commission_amount float required  Maintaince Commission. Example: 0.01
     *
     * @response {
     * "status" : true,
     * "code" : 200,
     * "messages" : "Success",
     * "response" : {setting_commission_id}
     * }
     */
    public function create(Request $request)
    {
        $inputs = $this->validate($request, [
            'params.fund_distributor_id'                   => 'required|integer|exists:App\Models\FundDistributor,id',
            'params.start_at'                              => 'required|string',
            'params.lines.*.min_amount'                    => 'required',
            'params.lines.*.sell_commission'               => 'required',
            'params.lines.*.maintance_commission_amount'   => 'required',
        ], [
            'params.fund_distributor_id.exists'                   => 'Nhà phân phối quỹ không tồn tại',
            'params.lines.*.min_amount.required'                  => 'Mục tiêu là bắt buộc',
            'params.lines.*.sell_commission.required'             => 'Phí bán hàng là bắt buộc',
            'params.lines.*.maintance_commission_amount.required' => 'Phí giữ là bắt buộc'
        ]);

        $fundDistributor = FundDistributor::find($inputs['params']['fund_distributor_id']);
        if ($fundDistributor) {
            $inputs['params']['ref_id ']  = $inputs['params']['fund_distributor_id'];
            $inputs['params']['ref_type'] = 'setting_commissions';

            // $this->authorize('create', ['App\Models\SettingCommission', $inputs['params']]);

            return (new SettingCommissionTransaction())->create($inputs['params'], true);
        }

        $this->error('Nhà phân phối quỹ không tồn tại');
    }

    /**
     * SettingCommission detail
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.setting_commission_lines_id integer required Setting commission lines id. Example: 1
     *
     * @response {
     * "status" : true,
     * "code" : 200,
     * "messages" : "Success",
     * "response" : {}
     * }
     */
    public function detail(Request $request)
    {
        $data = SettingCommissionLine::with([
            'fund_distributor' => function ($q) {
                $q->select('name', 'id');
            }
        ])->find($request['params']['setting_commission_lines_id']);

        return $this->responseSuccess($data);
    }

    /**
     * SettingCommission getListFundDistributor
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
     * "response" : {}
     * }
     */
    public function getListFundDistributor()
    {
        $fundDistributors = FundDistributor::get(['id as value', 'name as title']);
        return $this->responseSuccess($fundDistributors);
    }
}