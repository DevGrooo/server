<?php

namespace App\Http\Controllers;

use App\Models\SupervisingBank;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


/**
 * @group  SupervisingBank
 */
class SupervisingBankController extends Controller
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
     * SupervisingBank get list
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
        $companies = SupervisingBank::where('status', SupervisingBank::STATUS_ACTIVE)->get();
        return $this->responseSuccess(SupervisingBank::getArrayForSelectBox($companies, 'id', 'name'));
    }
}
