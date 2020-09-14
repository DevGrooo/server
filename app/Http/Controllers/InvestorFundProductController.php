<?php


namespace App\Http\Controllers;


use App\Http\Controllers\Traits\TableTrait;
use App\Models\FundDistributor;
use App\Models\FundDistributorProduct;
use App\Models\Investor;
use App\Models\InvestorFundProduct;
use App\Models\FundProduct;
use App\Services\Transactions\InvestorFundProductTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class InvestorFundProductController extends Controller
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
     * Investor list product investor
     *
     * Danh sách sản phẩm nhà đầu tư
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
        $query_builder = InvestorFundProduct::with(
            [
                'investor:id,name',
                'fund_distributor:id,name',
                'fund_company:id,name',
                'fund_certificate:id,name',
                'fund_product:id,name'
            ]);
        $data = $this->getData($request, $query_builder, '\App\Http\Resources\InvestorFundProductResource');
        return $this->responseSuccess($data);
     }

     /**
      * Investor add product investor
      *
      * Thêm sản phẩm cho nhà đầu tư
      *
      * @bodyParam env string required Enviroment client. Example: web
      * @bodyParam app_version string required App version. Example: 1.1
      * @bodyParam lang string required Language. Example: vi
      * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
      * @bodyParam params.investor_id integer required ID. Example: 1
      * @bodyParam params.fund_product_id integer required Fund Product ID. Example: 1
      *
      * @response {
      * "status": true,
      * "code": 200,
      * "messages": "Success",
      * "response": { "investor_fund_product_id": 1 }
      * }
      */
      public function create(Request $request)
      {
          $inputs = $this->validate($request, [
             'params.investor_id' => 'required',
             'params.fund_product_id' => 'required|integer'
          ]);
           $user = Auth::user();
           $inputs['params']['created_by'] = $user->id;
          return (new InvestorFundProductTransaction())->create($inputs['params'], true);
      }

      /**
       * Investor lock product investor
       *
       * Khóa sản phẩm nhà đầu tư
       *
       * @bodyParam env string required Enviroment client. Example: web
       * @bodyParam app_version string required App version. Example: 1.1
       * @bodyParam lang string required Language. Example: vi
       * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
       * @bodyParam params.investor_fund_product_id integer required ID. Example:1
       *
       * "@response {
       * "status": true,
       * "code": 200,
       * "messages": "Success",
       * "response": null
       * }
       */
       public function lock(Request $request)
       {
            $inputs = $this->validate($request, [
               'params.investor_fund_product_id' => 'integer'
            ]);

            return (new InvestorFundProductTransaction())->lock($inputs['params'], true);
       }

    /**
     * Investor active product investor
     *
     * Mở khóa sản phẩm nhà đầu tư
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.investor_fund_product_id integer required ID. Example:1
     *
     * "@response {
     * "status": true,
     * "code": 200,
     * "messages": "Success",
     * "response": null
     * }
     */
    public function active(Request $request)
    {
        $inputs = $this->validate($request, [
            'params.investor_fund_product_id' => 'integer'
        ]);

        return (new InvestorFundProductTransaction())->active($inputs['params'], true);
    }

    /**
     * Investor fund product get status
     *
     * Lấy danh sách trạng thái sản phẩm nhà đầu tư
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
        $list_status = InvestorFundProduct::getListStatus();
        return $this->responseSuccess(InvestorFundProduct::getArrayForSelectBox($list_status));
    }

    /**
     * Investor fund product get fund product
     *
     * Lấy danh danh sách sản phẩm quỹ có trong đại lý phân phối quỹ
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params investor_id string requried
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
            'params.investor_id' => 'required'
        ]);
        return (new InvestorFundProductTransaction())->getFundProduct($inputs['params'], true);
    }

}
