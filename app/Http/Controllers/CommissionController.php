<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Test;


/**
 * @group  Commission
 */
class CommissionController extends Controller
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
     * Commission list
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
        $tests = Test::paginate(5);

        return $this->responseSuccess($tests);
    }
}
