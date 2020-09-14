<?php

namespace App\Http\Controllers;

use App\Models\FundCompany;
use App\Models\FundDistributor;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


/**
 * @group  FundCompany
 */
class FundCompanyController extends Controller
{
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
     * Fund company get list
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
        $companies = FundCompany::where('status', FundCompany::STATUS_ACTIVE)->get();
        return $this->responseSuccess(FundCompany::getArrayForSelectBox($companies, 'id', 'name'));
    }
}
