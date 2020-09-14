<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MailTemplateLocale;
use App\Http\Controllers\Traits\TableTrait;
use App\Services\Transactions\MailTemplateLocaleTransaction;

/**
 * @group Mail Template Locale
 */

class MailTemplateLocaleController extends Controller {

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
     * List Mail Template Locale
     *
     * Danh sách ngôn ngữ mẩu thư
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
        $query_builder = MailTemplateLocale::with([
            'mail_template' => function($q) {
                $q->select('mail_templates.id', 'mail_templates.name', 'mail_templates.code');
            }
        ]);

        $data = $this->getData(
            $request,
            $query_builder,
            '\App\Http\Resources\MailTemplateLocaleResource',
            ['mail_template_locales.id', 'mail_template_locales.locale', 'mail_template_locales.mail_template_id', 'mail_template_locales.subject']
        );

        return $this->responseSuccess($data);
    }

    /**
     * Mail Template Locale add
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.mail_template_id integer ID Mail Template. Example: 1
     * @bodyParam params.subject string required Subject. Example: Subject
     * @bodyParam params.content string required Content. Example: Content
     *
     * @response {
     * "status" : true,
     * "code" : 200,
     * "messages" : "Success",
     * "response" : {}
     * }
     */
    public function create(Request $request)
    {
        $inputs = $this->validate($request, [
            'params.mail_template_id' => 'required|integer|exists:App\Models\MailTemplate,id',
            'params.subject'          => 'required|string',
            'params.content'          => 'required|string'
        ], [
            'params.mail_template_id.exists' => trans('table.mail_template.id.required')
        ]);

        // $this->authorize('create', ['App\Models\MailTemplateLocale', $inputs['params']]);

        return (new MailTemplateLocaleTransaction())->create($inputs['params'], true);
    }

    /**
     * Detail Mail Template Locale
     *
     * Ngôn ngữ Mẫu Thư Chi tiết
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.mail_template_locale_id integer required Mail Template Locale ID. Example: 1
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
            'params.mail_template_locale_id' => 'required|integer|exists:App\Models\MailTemplateLocale,id',
        ], [
            'params.mail_template_locale_id.exists' => trans('table.mail_template_locale.id.exists'),
        ]);

        $data = MailTemplateLocale::select('id', 'mail_template_id', 'subject', 'content')
            ->find($inputs['params']['mail_template_locale_id']);

        return $this->responseSuccess($data);
    }

    /**
     * Mail Template Locale update
     *
     * Cập nhật Ngôn ngữ Mẫu Thư
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.mail_template_locale_id integer ID Mail Template Locale. Example: 1
     * @bodyParam params.subject string required Subject. Example: Subject
     * @bodyParam params.content string required Content. Example: Content
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
            'params.mail_template_locale_id' => 'required|integer|exists:App\Models\MailTemplateLocale,id',
            'params.subject'                 => 'required|string',
            'params.content'                 => 'required|string'
        ], [
            'params.mail_template_locale_id.exists' => trans('table.mail_template_locale.id.exists')
        ]);

        // $this->authorize('update', ['App\Models\MailTemplateLocale', $inputs['params']]);

        return (new MailTemplateLocaleTransaction())->update($inputs['params'], true);
    }

}
