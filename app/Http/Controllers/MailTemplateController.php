<?php

namespace App\Http\Controllers;

use App\Models\MailTemplate;
use Illuminate\Http\Request;
use App\Http\Controllers\Traits\TableTrait;

/**
 * @group Mail Template
 */

class MailTemplateController extends Controller {

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
     * List Mail Template
     *
     * Danh sách mẩu thư
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
    public function list(Request $request) {
        $query_builder = MailTemplate::with([
            'mail_template_locales' => function($q) {
                $q->select('mail_template_locales.id', 'mail_template_locales.locale', 'mail_template_locales.mail_template_id', 'mail_template_locales.subject');
            }
        ]);

        $data = $this->getData(
            $request,
            $query_builder,
            '\App\Http\Resources\MailTemplateResource',
            ['mail_templates.id', 'mail_templates.name', 'mail_templates.code']
        );

        return $this->responseSuccess($data);
    }

    /**
     * Detail Mail Template
     *
     * Chi tiết mẩu email
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.mail_template_id integer required Mail Template ID. Example: 1
     *
     * @response {
     * "status": true,
     * "code": 200,
     * "messages": "Success",
     * "response": null
     * }
     */
    public function detail(Request $request) {
        $inputs = $this->validate($request, [
            'params.mail_template_id' => 'required|integer|exists:App\Models\MailTemplate,id',
        ], [
            'params.mail_template_id.required' => trans('table.mail_template.id.required'),
            'params.mail_template_id.integer' => trans('table.mail_template.id.integer'),
            'params.mail_template_id.exists' => trans('table.mail_template.id.exists'),
        ]);

        $data = MailTemplate::find($inputs['params']['mail_template_id'])
            ->select('keys')
            ->get();

        return $this->responseSuccess($data);
    }

    /**
     * GetList Mail Template
     *
     * Danh sách mẩu thư
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
    public function getList(Request $request) {
        $data = MailTemplate::select('id as mail_template_id', 'name', 'keys')
            ->where('status', MailTemplate::STATUS_ACTIVE)
            ->get();

        return $this->responseSuccess($data);
    }


}
