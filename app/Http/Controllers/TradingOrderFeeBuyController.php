<?php


namespace App\Http\Controllers;


use App\Http\Controllers\Traits\TableTrait;
use App\Models\TradingOrderFeeBuy;
use App\Services\Transactions\TradingOrderFeeBuyTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TradingOrderFeeBuyController extends Controller
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
     * Order list trading fee buy
     *
     * Danh sách phí mua
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
         $query_bulder = TradingOrderFeeBuy::with('fund_product:id,name');
         $data = $this->getData($request, $query_bulder, '\App\Http\Resources\TradingOrderFeeBuyResource');
         return $this->responseSuccess($data);
     }

     /**
      * Order add trading fee buy
      *
      * Thêm phí mua
      *
      * @bodyParam env string required Enviroment client. Example: web
      * @bodyParam app_version string required App version. Example: 1.1
      * @bodyParam lang string required Language. Example: vi
      * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
      * @bodyParam params.fund_product_id integer require ID fund product id. Example: 1
      * @bodyParam params.fund_distributor_id integer ID fund distributor. Example: 0
      * @bodyParam params.investor_id integer ID investor. Example: 0
      * @bodyParam params.start_at  required Start apply fee. Example: 2020/07/10 13:00:00
      * @bodyParam params.lines.*.min_amount  required Minimum amount apply fee. Example: 1
      * @bodyParam params.lines.*.fee_amount  required Fee amount. Example: 10000
      * @bodyParam params.lines.*.fee_percent required Fee percent. Example: 0.01
      *
      * @response {
      * "status": true,
      * "code": 200,
      * "messages": "Success",
      * "response": null
      * }
      */
     public function create(Request $request)
     {
//         dd($request->all());
         $inputs = $this->validate($request, [
            'params.fund_product_id' => 'required|integer',
            'params.fund_distributor_id' => 'integer',
            'params.investor_id' => 'integer',
            'params.start_at' => 'required',
            'params.lines.*.min_amount' => 'required',
            'params.lines.*.fee_percent' => 'required'
         ]);

         $user = Auth::user();
         $inputs['params']['created_by'] = $user->id;
//         dd($inputs);
         return (new TradingOrderFeeBuyTransaction())->createMulti($inputs['params'], true);
     }

     /**
      * Order lock trading fee buy
      *
      * Khóa phí mua
      *
      * @bodyParam env string required Enviroment client. Example: web
      * @bodyParam app_version string required App version. Example: 1.1
      * @bodyParam lang string required Language. Example: vi
      * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
      * @bodyParam params.trading_order_fee_buy_id integer required Fee buy Id. Example: 1
      *
      * @response {
      * "status": true,
      * "code": 200,
      * "messages": "Success",
      * "response": null
      * }
      */
      public function lock(Request $request)
      {
          $inputs = $this->validate($request, [
             'params.trading_order_fee_buy_id' => 'required|integer'
          ]);
          return (new TradingOrderFeeBuyTransaction())->lock($inputs['params'], true);
      }

    /**
     * Trading order fee buy get status
     *
     * Lấy danh sách trạng thái phí mua
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
        $list_status = TradingOrderFeeBuy::getListStatus();
        return $this->responseSuccess(TradingOrderFeeBuy::getArrayForSelectBox($list_status));
    }
}
