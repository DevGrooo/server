<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\TableTrait;
use App\Http\Resources\InvestorResource;
use App\Imports\InvestorImport;
use App\Models\FileImport;
use App\Models\IdType;
use App\Models\Investor;
use App\Models\InvestorBankAccount;
use App\Models\User;
use App\Services\Transactions\FileImportTransaction;
use App\Services\Transactions\InvestorTransaction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

/**
 * @group  Investor
 */
class InvestorController extends Controller
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
     * Investor Create
     *
     * Tạo tài khoản ngần hàng cho nhà đầu tư
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.fund_distributor_code required string Đại lý quản lý quỹ. Example: 1
     * @bodyParam params.fund_distributor_staff_id integer Nhân viên đại lý. Example: 1
     * @bodyParam params.referral_bank_id integer Ngân hàng giới thiệu. Example: 1
     * @bodyParam params.trading_account_number string required Account Number VSD. Example: 34906902
     * @bodyParam params.trading_account_type integer required Loại tài khoản giao dịch. Example: 1
     * @bodyParam params.code string required Mã nhà đầu tư. Example: ABC
     * @bodyParam params.zone_type integer required Phân loại(1:Trong nước,2:Nước ngoài). Example: 1
     * @bodyParam params.scale_type integer required Loại nhà đầu tư(1:Cá nhân,2:Tổ chức). Example: 1
     * @bodyParam params.invest_type integer required Loại đầu tư(1:SIP,2:Thông thường). Example: 1
     * @bodyParam params.name string required Tên nhà đầu tư. Example: Nguyen Van A
     * @bodyParam params.gender string Giới tính. Example: M
     * @bodyParam params.country_id integer Quốc gia. Example: 1
     * @bodyParam params.birthday date Ngày sinh. Example: 20/11/2009
     * @bodyParam params.id_type_id integer Loại giấy chứng thực. Example: 1
     * @bodyParam params.id_number string Số giấy chứng thực. Example: 023862386792
     * @bodyParam params.id_issuing_date date Ngày cấp. Example: 09/07/2020
     * @bodyParam params.id_issuing_place string Nơi cấp. Example: Ha Noi
     * @bodyParam params.id_expiration_date date Ngày hết hạn. Example: 09/07/2025
     * @bodyParam params.phone string Số điện thoại. Example: 0987654321
     * @bodyParam params.fax string Fax.
     * @bodyParam params.email string Địa chỉ email. Example: phuonglh@hiworld.com.vn
     * @bodyParam params.tax_id string Mã số thuế.
     * @bodyParam params.tax_country_id integer Quốc gia thu thuế. Example: 1
     * @bodyParam params.permanent_address string Địa chỉ đăng ký thường trú/Trụ sở. Example: Thanh Thai, Cau Giay, Ha Noi
     * @bodyParam params.permanent_country_id integer Quốc gia. Example: 1
     * @bodyParam params.current_address string Địa chỉ hiện tại. Example: Thanh Thai, Cau Giay, Ha Noi
     * @bodyParam params.current_country_id Quốc gia hiện tại. Example: 1
     * @bodyParam params.visa_number string Số thị thực nhập cảnh. Example:
     * @bodyParam params.visa_issuing_date date Ngày cấp Visa. Example:
     * @bodyParam params.visa_issuing_place string Nơi cấp Visa. Example:
     * @bodyParam params.temporary_address Địa chỉ tạm trú. Example: Thanh Thai, Cau Giay, Ha Noi
     * @bodyParam params.re_fullname string Họ tên người đại diện. Example: Nguyen Van A
     * @bodyParam params.re_position string Chức vụ. Example: Giam doc
     * @bodyParam params.re_gender integer  Giới tính(1:Nam,2:Nữ). Example:
     * @bodyParam params.re_country_id integer Quốc gia người đại diện. Example: 1
     * @bodyParam params.re_id_number string Số giấy chứng thực người đại diện. Example: 348968234689
     * @bodyParam params.re_id_type integer Loại giấy chứng thực người đại diện. Example: 1
     * @bodyParam params.re_id_issuing_date date Ngày cấp. Example: 09/07/2020
     * @bodyParam params.re_id_issuing_place string Nơi cấp. Example: Ha Noi
     * @bodyParam params.re_id_expiration_date date Ngày hết hạn. Example: 09/07/2025
     * @bodyParam params.re_phone string Số điện thoại. Example: 0987654321
     * @bodyParam params.re_address string Địa chỉ người đại diện. Example: Thanh Thai, Cau Giay, Ha Noi
     * @bodyParam params.re_country_id integer Quốc gia người đại diện. Example: 1
     * @bodyParam params.au_fullname string Họ tên người ủy quyền. Example: Nguyen Van A
     * @bodyParam params.au_country_id integer Quốc gia người ủy quyền. Example: 1
     * @bodyParam params.au_id_number string Số giấy chứng thực người ủy quyền. Example: 23896892534
     * @bodyParam params.au_id_type integer Loại giấy chứng thực người ủy quyền. Example: 1
     * @bodyParam params.au_id_issuing_date date Ngày cấp. Example: 09/07/2020
     * @bodyParam params.au_id_issuing_place string Nơi cấp. Example: Ha Noi
     * @bodyParam params.au_id_expiration_date date Ngày hết hạn. Example: 09/07/2025
     * @bodyParam params.au_phone string Số điện thoại người ủy quyền. Example: 0987654321
     * @bodyParam params.au_address string Địa chỉ. Example: Thanh Thai, Cau Giay, Ha Noi
     * @bodyParam params.au_country_id integer Quốc gia. Example: 1
     * @bodyParam params.au_start_date date Ngày bắt đầu ủy quyền. Example: 01/07/2020
     * @bodyParam params.au_end_date date Ngày kết thúc ủy quyền. Example: 01/07/2021
     * @bodyParam params.fatca_link_auth string fatca_link_auth.
     * @bodyParam params.fatca_recode string fatca_recode.
     * @bodyParam params.fatca_funds string fatca_funds.
     * @bodyParam params.fatca1 integer fatca1.
     * @bodyParam params.fatca2 integer fatca2.
     * @bodyParam params.fatca3 integer fatca3.
     * @bodyParam params.fatca4 integer fatca4.
     * @bodyParam params.fatca5 integer fatca5.
     * @bodyParam params.fatca6 integer fatca6.
     * @bodyParam params.fatca7 integer fatca7.
     * @response {
     *      "status": true,
     *      "code": 200,
     *      "messages": "Success",
     *      "response": {
     *          "investor_id" : 1,
     *          "investor_bank_account_id" : 1
     *      }
     * }
     */
    public function create(Request $request)
    {
        $rules = [
            'params.fund_distributor_code' => 'required|string|max:255',
            'params.fund_distributor_staff_id' => 'integer',
            'params.referral_bank_id' => 'integer',
            'params.trading_account_number' => 'required|string|max:255|unique:investors,trading_account_number',
            'params.trading_reference_number' => 'string|max:255',
            'params.trading_account_type' => 'required|integer',
            'params.name' => 'required|string|max:500',
            'params.zone_type' => 'required|integer',
            'params.scale_type' => 'required|integer',
            'params.invest_type' => 'required|integer',
            'params.country_id' => 'integer',
            'params.birthday' => 'date_format:d/m/Y',
            'params.gender' => 'string',
            'params.id_type_id' => 'integer',
            'params.id_number' => 'string|max:100',
            'params.id_issuing_date' => 'date_format:d/m/Y',
            'params.id_issuing_place' => 'string|max:255',
            'params.id_expiration_date' => 'date_format:d/m/Y',
            'params.permanent_address' => 'string|max:500',
            'params.permanent_country_id' => 'integer',
            'params.current_address' => 'string|max:500',
            'params.current_country_id' => 'integer',
            'params.phone' => 'string|max:50',
            'params.fax' => 'string|max:50',
            'params.email' => 'string|max:255',
            'params.tax_id' => 'string|max:100',
            'params.tax_country_id' => 'integer',
            'params.visa_number' => 'string|max:50',
            'params.visa_issuing_date' => 'date_format:d/m/Y',
            'params.visa_issuing_place' => 'string|max:255',
            'params.temporary_address' => 'string',
            'params.re_fullname' => 'string|max:255',
            'params.re_birthday' => 'date_format:d/m/Y',
            'params.re_gender' => 'integer',
            'params.re_position' => 'string|max:255',
            'params.re_id_type' => 'integer',
            'params.re_id_number' => 'string|max:50',
            'params.re_id_issuing_date' => 'date_format:d/m/Y',
            'params.re_id_issuing_place' => 'string|max:255',
            'params.re_id_expiration_date' => 'date_format:d/m/Y',
            'params.re_phone' => 'string|max:50',
            'params.re_address' => 'string',
            'params.re_country_id' => 'integer',
            'params.au_fullname' => 'string|max:255',
            'params.au_id_type' => 'integer',
            'params.au_id_number' => 'string|max:50',
            'params.au_id_issuing_date' => 'date_format:d/m/Y',
            'params.au_id_issuing_place' => 'string|max:255',
            'params.au_id_expiration_date' => 'date_format:d/m/Y',
            'params.au_email' => 'string|max:255',
            'params.au_phone' => 'string|max:50',
            'params.au_address' => 'string',
            'params.au_country_id' => 'integer',
            'params.au_start_date' => 'date_format:d/m/Y',
            'params.au_end_date' => 'date_format:d/m/Y',
            'params.bank_id' => 'required|integer',
            'params.account_holder' => 'required|string|max:255',
            'params.account_number' => 'required|string|max:50',
            'params.branch' => 'string|max:255',
            'params.description' => 'string|max:500',
        ];
        $inputs = $this->validate($request, $rules, ['unique' => 'Tài khoản giao dịch đã tồn tại']);
        $params = $inputs['params'];
        if ($params['zone_type'] == 2) {
            $external_rules = [
                'params.fatca_link_auth' => 'string|max:255',
                'params.fatca_recode' => 'string|max:255',
                'params.fatca_funds' => 'array',
                'params.fatca1' => 'integer',
                'params.fatca2' => 'integer',
                'params.fatca3' => 'integer',
                'params.fatca4' => 'integer',
                'params.fatca5' => 'integer',
                'params.fatca6' => 'integer',
                'params.fatca7' => 'integer',
            ];
            $external_inputs = $this->validate($request, $external_rules);
            $params = array_merge($params, $external_inputs['params']);
        }
        $params['is_default'] = InvestorBankAccount::DEFAULT;
        $params['created_by'] = auth()->user()->id;
        $this->authorize('create', ['App\Models\Investor', $params]);
        return (new InvestorTransaction())->createAndAddBankAccount($params, true);
    }

    /**
     * Investor Update
     *
     * Cập nhật tài khoản ngần hàng cho nhà đầu tư
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.investor_id required integer Nhà đầu tư. Example: 1
     * @bodyParam params.fund_distributor_staff_id integer Nhân viên đại lý. Example: 1
     * @bodyParam params.referral_bank_id integer Ngân hàng giới thiệu. Example: 1
     * @bodyParam params.trading_account_number string required Account Number VSD. Example: 34906902
     * @bodyParam params.trading_account_type integer required Loại tài khoản giao dịch. Example: 1
     * @bodyParam params.code string required Mã nhà đầu tư. Example: ABC
     * @bodyParam params.zone_type integer required Phân loại(1:Trong nước,2:Nước ngoài). Example: 1
     * @bodyParam params.scale_type integer required Loại nhà đầu tư(1:Cá nhân,2:Tổ chức). Example: 1
     * @bodyParam params.invest_type integer required Loại đầu tư(1:SIP,2:Thông thường). Example: 1
     * @bodyParam params.name string required Tên nhà đầu tư. Example: Nguyen Van A
     * @bodyParam params.gender integer Giới tính. Example: 1
     * @bodyParam params.country_id integer Quốc gia. Example: 1
     * @bodyParam params.birthday date Ngày sinh. Example: 20/11/2009
     * @bodyParam params.id_type integer Loại giấy chứng thực. Example: 1
     * @bodyParam params.id_number string Số giấy chứng thực. Example: 023862386792
     * @bodyParam params.id_issuing_date date Ngày cấp. Example: 09/07/2020
     * @bodyParam params.id_issuing_place string Nơi cấp. Example: Ha Noi
     * @bodyParam params.id_expiration_date date Ngày hết hạn. Example: 09/07/2025
     * @bodyParam params.phone string Số điện thoại. Example: 0987654321
     * @bodyParam params.fax string Fax.
     * @bodyParam params.email string Địa chỉ email. Example: phuonglh@hiworld.com.vn
     * @bodyParam params.tax_id string Mã số thuế.
     * @bodyParam params.tax_country_id integer Quốc gia thu thuế. Example: 1
     * @bodyParam params.permanent_address string Địa chỉ đăng ký thường trú/Trụ sở. Example: Thanh Thai, Cau Giay, Ha Noi
     * @bodyParam params.permanent_country_id integer Quốc gia. Example: 1
     * @bodyParam params.current_address string Địa chỉ hiện tại. Example: Thanh Thai, Cau Giay, Ha Noi
     * @bodyParam params.current_country_id Quốc gia hiện tại. Example: 1
     * @bodyParam params.visa_number string Số thị thực nhập cảnh. Example:
     * @bodyParam params.visa_issuing_date date Ngày cấp Visa. Example:
     * @bodyParam params.visa_issuing_place string Nơi cấp Visa. Example:
     * @bodyParam params.temporary_address Địa chỉ tạm trú. Example: Thanh Thai, Cau Giay, Ha Noi
     * @bodyParam params.re_fullname string Họ tên người đại diện. Example: Nguyen Van A
     * @bodyParam params.re_position string Chức vụ. Example: Giam doc
     * @bodyParam params.re_gender integer  Giới tính(1:Nam,2:Nữ). Example:
     * @bodyParam params.re_country_id integer Quốc gia người đại diện. Example: 1
     * @bodyParam params.re_id_number string Số giấy chứng thực người đại diện. Example: 348968234689
     * @bodyParam params.re_id_type integer Loại giấy chứng thực người đại diện. Example: 1
     * @bodyParam params.re_id_issuing_date date Ngày cấp. Example: 09/07/2020
     * @bodyParam params.re_id_issuing_place string Nơi cấp. Example: Ha Noi
     * @bodyParam params.re_id_expiration_date date Ngày hết hạn. Example: 09/07/2025
     * @bodyParam params.re_phone string Số điện thoại. Example: 0987654321
     * @bodyParam params.re_address string Địa chỉ người đại diện. Example: Thanh Thai, Cau Giay, Ha Noi
     * @bodyParam params.re_country_id integer Quốc gia người đại diện. Example: 1
     * @bodyParam params.au_fullname string Họ tên người ủy quyền. Example: Nguyen Van A
     * @bodyParam params.au_country_id integer Quốc gia người ủy quyền. Example: 1
     * @bodyParam params.au_id_number string Số giấy chứng thực người ủy quyền. Example: 23896892534
     * @bodyParam params.au_id_type integer Loại giấy chứng thực người ủy quyền. Example: 1
     * @bodyParam params.au_id_issuing_date date Ngày cấp. Example: 09/07/2020
     * @bodyParam params.au_id_issuing_place string Nơi cấp. Example: Ha Noi
     * @bodyParam params.au_id_expiration_date date Ngày hết hạn. Example: 09/07/2025
     * @bodyParam params.au_phone string Số điện thoại người ủy quyền. Example: 0987654321
     * @bodyParam params.au_address string Địa chỉ. Example: Thanh Thai, Cau Giay, Ha Noi
     * @bodyParam params.au_country_id integer Quốc gia. Example: 1
     * @bodyParam params.au_start_date date Ngày bắt đầu ủy quyền. Example: 01/07/2020
     * @bodyParam params.au_end_date date Ngày kết thúc ủy quyền. Example: 01/07/2021
     * @bodyParam params.fatca_link_auth string fatca_link_auth.
     * @bodyParam params.fatca_recode string fatca_recode.
     * @bodyParam params.fatca_funds string fatca_funds.
     * @bodyParam params.fatca1 integer fatca1.
     * @bodyParam params.fatca2 integer fatca2.
     * @bodyParam params.fatca3 integer fatca3.
     * @bodyParam params.fatca4 integer fatca4.
     * @bodyParam params.fatca5 integer fatca5.
     * @bodyParam params.fatca6 integer fatca6.
     * @bodyParam params.fatca7 integer fatca7.
     * @response {
     *      "status": true,
     *      "code": 200,
     *      "messages": "Success",
     *      "response": {
     *          "investor_id" : 1,
     *          "investor_bank_account_id" : 1
     *      }
     * }
     */
    public function update(Request $request)
    {
        $inputs = $this->validate($request, [
            'params.investor_id' => 'required|integer',
            'params.fund_distributor_staff_id' => 'integer',
            'params.referral_bank_id' => 'integer',
            'params.trading_account_number' => 'required|string|max:255',
            'params.trading_reference_number' => 'string|max:255',
            'params.trading_account_type' => 'required|integer',
            'params.name' => 'required|string|max:500',
            'params.zone_type' => 'required|integer',
            'params.scale_type' => 'required|integer',
            'params.invest_type' => 'required|integer',
            'params.country_id' => 'integer',
            'params.birthday' => 'date_format:d/m/Y',
            'params.gender' => 'integer',
            'params.id_type' => 'integer',
            'params.id_number' => 'string|max:100',
            'params.id_issuing_date' => 'date_format:d/m/Y',
            'params.id_issuing_place' => 'string|max:255',
            'params.id_expiration_date' => 'date_format:d/m/Y',
            'params.permanent_address' => 'string|max:500',
            'params.permanent_country_id' => 'integer',
            'params.current_address' => 'string|max:500',
            'params.current_country_id' => 'integer',
            'params.phone' => 'string|max:50',
            'params.fax' => 'string|max:50',
            'params.email' => 'string|max:255',
            'params.tax_id' => 'string|max:100',
            'params.tax_country_id' => 'integer',
            'params.visa_number' => 'string|max:50',
            'params.visa_issuing_date' => 'date_format:d/m/Y',
            'params.visa_issuing_place' => 'string|max:255',
            'params.temporary_address' => 'string',
            'params.re_fullname' => 'string|max:255',
            'params.re_birthday' => 'date_format:d/m/Y',
            'params.re_gender' => 'integer',
            'params.re_position' => 'string|max:255',
            'params.re_id_type' => 'integer',
            'params.re_id_number' => 'string|max:50',
            'params.re_id_issuing_date' => 'date_format:d/m/Y',
            'params.re_id_issuing_place' => 'string|max:255',
            'params.re_id_expiration_date' => 'date_format:d/m/Y',
            'params.re_phone' => 'string|max:50',
            'params.re_address' => 'string',
            'params.re_country_id' => 'integer',
            'params.au_fullname' => 'string|max:255',
            'params.au_id_type' => 'integer',
            'params.au_id_number' => 'string|max:50',
            'params.au_id_issuing_date' => 'date_format:d/m/Y',
            'params.au_id_issuing_place' => 'string|max:255',
            'params.au_id_expiration_date' => 'date_format:d/m/Y',
            'params.au_email' => 'string|max:255',
            'params.au_phone' => 'string|max:50',
            'params.au_address' => 'string',
            'params.au_country_id' => 'integer',
            'params.au_start_date' => 'date_format:d/m/Y',
            'params.au_end_date' => 'date_format:d/m/Y',
            'params.fatca_link_auth' => 'string|max:255',
            'params.fatca_recode' => 'string|max:255',
            'params.fatca_funds' => 'array',
            'params.fatca1' => 'integer',
            'params.fatca2' => 'integer',
            'params.fatca3' => 'integer',
            'params.fatca4' => 'integer',
            'params.fatca5' => 'integer',
            'params.fatca6' => 'integer',
            'params.fatca7' => 'integer',
            'params.bank_id' => 'required|integer',
            'params.account_holder' => 'required|string|max:255',
            'params.account_number' => 'required|string|max:50',
            'params.branch' => 'string|max:255',
            'params.description' => 'string|max:500',
        ]);
        $params = $inputs['params'];
        $params['updated_by'] = auth()->user()->id;
        $this->authorize('create', ['App\Models\Investor', $params]);
        return (new InvestorTransaction())->update($params, true);
    }

    /**
     * Investor Get list
     *
     * Lấy danh sách công ty quản lý quỹ
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam fund_distributor_id integer Fund Distributor ID. Example: 1
     *
     * @response {
     * "status": true,
     * "code": 200,
     * "messages": "Success",
     * "response": {
     * }
     */
    public function getList(Request $request)
    {
        $inputs = $this->validate($request,[
            'params.fund_distributor_id'  => 'integer',
        ]);
        $fund_distributor_id = intval(@$inputs['params']['fund_distributor_id']);
        if ($fund_distributor_id > 0) {
            $rows = Investor::where('fund_distributor_id', $fund_distributor_id)
                ->whereNotIn('status', [Investor::STATUS_CANCEL, Investor::STATUS_CLOSED])->get();
        } else {
            $rows = Investor::whereNotIn('status', [Investor::STATUS_CANCEL, Investor::STATUS_CLOSED])->get();
        }
        return $this->responseSuccess(Investor::getArrayForSelectBox($rows, 'id', 'name'));
    }

    /**
     * Investor Get list trading_account_number
     *
     * Lấy danh sách công ty quản lý quỹ
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam fund_distributor_id integer Fund Distributor ID. Example: 1
     *
     * @response {
     * "status": true,
     * "code": 200,
     * "messages": "Success",
     * "response": {
     * }
     */
//    public function getListTradingAccountNumber(Request $request)
//    {
//        $inputs = $this->validate($request,[
//            'params.fund_distributor_id'  => 'integer',
//        ]);
//        $fund_distributor_id = intval(@$inputs['params']['fund_distributor_id']);
//        if ($fund_distributor_id > 0) {
//            $rows = Investor::where('fund_distributor_id', $fund_distributor_id)->get();
//        }
//
//        return $this->responseSuccess(Investor::getArrayForSelectBox($rows, 'id', 'trading_account_number'));
//    }

    /**
     * Investor Get list
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
    public function list(Request $request)
    {
        $query_builder = Investor::query();
        return $this->responseSuccess($this->getData($request, $query_builder, '\App\Http\Resources\InvestorResource'));
    }

    /**
     * Investor get status
     *
     * Lấy danh sách trạng thái người dùng
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
        $list_status = Investor::getListStatus();
        return $this->responseSuccess(Investor::getArrayForSelectBox($list_status));
    }

    /**
     * Investor get vsd status
     *
     * Lấy danh sách trạng thái người dùng
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
    public function getVsdStatus()
    {
        $list = Investor::getListVsdStatus();
        return $this->responseSuccess(Investor::getArrayForSelectBox($list));
    }

    /**
     * Investor get genders
     *
     * Lấy danh sách trạng thái người dùng
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
    public function getGenders()
    {
        $list = Investor::getListGenders();
        return $this->responseSuccess(Investor::getArrayForSelectBox($list));
    }

    /**
     * Investor closed
     *
     * Mở khóa người dùng
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.investor_id integer required User ID. Example: 1
     *
     * @response {
     * "status" : false,
     * "code" : 403,
     * "messages" : "Access denied",
     * "response" : null
     * }
     */
    public function closed(Request $request)
    {
        $inputs = $this->validate($request,[
            'params.investor_id'       => 'required|integer',
        ]);
        $this->authorize('closed', ['App\Models\Investor', $inputs['params']]);
        return (new InvestorTransaction())->closed([
            'investor_id' => $inputs['params']['investor_id'],
            'updated_by' => auth()->user()->id
        ],true);
    }

    /**
     * Investor cancel
     *
     * Mở khóa người dùng
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.investor_id integer required User ID. Example: 1
     *
     * @response {
     * "status" : false,
     * "code" : 403,
     * "messages" : "Access denied",
     * "response" : null
     * }
     */
    public function cancel(Request $request)
    {
        $inputs = $this->validate($request,[
            'params.investor_id'       => 'required|integer',
        ]);
        $this->authorize('update', ['App\Models\Investor', $inputs['params']]);
        return (new InvestorTransaction())->cancel([
            'investor_id' => $inputs['params']['investor_id'],
            'updated_by' => auth()->user()->id
        ],true);
    }

    /**
     * Investor reopen
     *
     * Mở khóa người dùng
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.investor_id integer required User ID. Example: 1
     *
     * @response {
     * "status" : false,
     * "code" : 403,
     * "messages" : "Access denied",
     * "response" : null
     * }
     */
    public function reopen(Request $request)
    {
        $inputs = $this->validate($request,[
            'params.investor_id'       => 'required|integer',
        ]);
        $this->authorize('update', ['App\Models\Investor', $inputs['params']]);
        return (new InvestorTransaction())->reopen([
            'investor_id' => $inputs['params']['investor_id'],
            'updated_by' => Auth::user()->id
        ],true);
    }

    /**
     * Investor active
     *
     * Kích hoạt NĐT
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.investor_id integer required User ID. Example: 1
     *
     * @response {
     * "status" : false,
     * "code" : 403,
     * "messages" : "Access denied",
     * "response" : null
     * }
     */
    public function active(Request $request)
    {
        $inputs = $this->validate($request,[
            'params.investor_id'       => 'required|integer',
        ]);
        $this->authorize('update', ['App\Models\Investor', $inputs['params']]);
        return (new InvestorTransaction())->active([
            'investor_id' => $inputs['params']['investor_id'],
            'updated_by' => Auth::user()->id
        ],true);
    }

    /**
     * Investor reject
     *
     * Từ chối kích hoạt NĐT
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.investor_id integer required User ID. Example: 1
     *
     * @response {
     * "status" : false,
     * "code" : 403,
     * "messages" : "Access denied",
     * "response" : null
     * }
     */
    public function reject(Request $request)
    {
        $inputs = $this->validate($request,[
            'params.investor_id'       => 'required|integer',
        ]);
        $this->authorize('update', ['App\Models\Investor', $inputs['params']]);
        return (new InvestorTransaction())->reject([
            'investor_id' => $inputs['params']['investor_id'],
            'updated_by' => Auth::user()->id
        ],true);
    }

    /**
     * Investor sendMail
     *
     * Cập nhật đã gửi email thông báo tài khoản được kích hoạt đến NĐT
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.investor_id integer required User ID. Example: 1
     *
     * @response {
     * "status" : false,
     * "code" : 403,
     * "messages" : "Access denied",
     * "response" : null
     * }
     */
    public function sendMail(Request $request)
    {
        $inputs = $this->validate($request,[
            'params.investor_id'       => 'required|integer',
        ]);
        $this->authorize('update', ['App\Models\Investor', $inputs['params']]);
        return (new InvestorTransaction())->updateStatusSendMail([
            'investor_id' => $inputs['params']['investor_id'],
            'updated_by' => Auth::user()->id
        ],true);
    }

    /**
     * Investor get scale_types
     *
     * Lấy danh sách trạng thái người dùng
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
    public function getScaleTypes()
    {
        $list = Investor::getListScaleTypes();
        return $this->responseSuccess(Investor::getArrayForSelectBox($list));
    }

    /**
     * Investor get id_types
     *
     * Lấy danh sách trạng thái người dùng
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
    public function getIdTypes()
    {
        $id_types = IdType::where('status', IdType::STATUS_ACTIVE)->get(['name AS title', 'id AS value']);
        return $this->responseSuccess($id_types);
    }

    /**
     * Investor get invest_types
     *
     * Lấy danh sách loại đầu tư
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
    public function getInvestTypes()
    {
        $list = Investor::getListInvestTypes();
        return $this->responseSuccess(Investor::getArrayForSelectBox($list));
    }

    /**
     * Investor get zone_types
     *
     * Lấy danh sách loại đầu tư
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
    public function getZoneTypes()
    {
        $list = Investor::getListZoneTypes();
        return $this->responseSuccess(Investor::getArrayForSelectBox($list));
    }

    /**
     * Investor get trading account types
     *
     * Lấy danh sách loại đầu tư
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
    public function getTradingAccountTypes()
    {
        $list = Investor::getListTradingAccountTypes();
        return $this->responseSuccess(Investor::getArrayForSelectBox($list));
    }

    /**
     * Investor detail
     *
     * Xem chi tiết thông tin quỹ sản phẩm
     *
     * @bodyPram env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.investor_id integer required User ID. Example: 1
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
            'params.investor_id' => 'required|integer'
        ]);
        $this->authorize('view', ['App\Models\Investor', $inputs['params']]);
        $investor= Investor::where('id', $inputs['params']['investor_id'])->first();
        if ($investor) {
            return $this->responseSuccess(new InvestorResource($investor));
        } else {
            $this->error('Nhà đầu tư không tồn tại');
        }
    }

    /**
     * Investor importFileExcel
     *
     * Xem chi tiết thông tin quỹ sản phẩm
     *
     * @bodyPram env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.file file required File import. Example: 1
     *
     * @response {
     * "status" : false,
     * "code" : 403,
     * "messages" : "Access denied",
     * "response" : null
     * }
     */
    public function importFileExcel(Request $request)
    {
        $inputs = $this->validate($request, [
            'params.file' => 'required|mimes:xls,xlsx,csv'
        ]);
        $file = $inputs['params']['file'];
        $file_name = $file->getClientOriginalName();
        $file_path = 'uploads/investors/'.date('YmdHis') . '_' . uniqid() . '.' . $file->extension();
        Storage::disk('local')->put($file_path, file_get_contents($file->getRealPath()));
        // get lines in file import
        $import = new InvestorImport();
        Excel::import($import, $file_path);
        // save lines to DB
        return (new FileImportTransaction())->create([
            'type' => FileImport::TYPE_INVESTOR,
            'file_name' => $file_name,
            'file_path' => $file_path,
            'lines' => $import->getLines(),
            'created_by' => auth()->user()->id,
        ], true);
    }


    /**
     * Investor exportFileExcel
     *
     * Xem chi tiết thông tin quỹ sản phẩm
     *
     * @bodyPram env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     *
     * @response {
     * "status" : false,
     * "code" : 403,
     * "messages" : "Access denied",
     * "response" : null
     * }
     */
    public function exportFileExcel(Request $request)
    {
        $query_builder = Investor::query();
        $file_data = $this->exportData($request, $query_builder, '\App\Exports\InvestorExport');
        $file_name = 'investor.xlsx';
        return $this->responseSuccess(['file_name' => $file_name, 'file_data' => $file_data]);
    }

    /**
     * Investor exportVSD
     *
     * Export file để import vào hệ thống VSD
     *
     * @bodyPram env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     *
     * @response {
     * "status" : false,
     * "code" : 403,
     * "messages" : "Access denied",
     * "response" : null
     * }
     */
    public function exportVSD(Request $request)
    {
        $query_builder = Investor::query();
        $file_data = $this->exportData($request, $query_builder, '\App\Exports\InvestorVSDExport');
        $file_name = 'investor_import_vsd.xlsx';
        return $this->responseSuccess(['file_name' => $file_name, 'file_data' => $file_data]);
    }

    /**
     * Investor importFileExcel
     *
     * Xem chi tiết thông tin quỹ sản phẩm
     *
     * @bodyPram env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.file_import_id integer required File import. Example: 1
     *
     * @response {
     * "status" : false,
     * "code" : 403,
     * "messages" : "Access denied",
     * "response" : null
     * }
     */
    public function import(Request $request)
    {
        $inputs = $this->validate($request, [
            'params.file_import_id' => 'required|integer'
        ]);
        return (new InvestorTransaction)->import([
            'file_import_id' => $inputs['params']['file_import_id'],
            'created_by' => auth()->user()->id,
        ], true);
    }

    /**
     * Get detail and sip products
     *
     * Lấy thông tin NĐT và danh sách các sản phẩm SIP mà NĐT được đặt lệnh
     *
     * "@bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam fund_distributor_id integer Fund Distributor ID. Example: 1"
     * "@response {
     * "status"": true,
     * "code"": 200,
     * "messages"": ""Success"",
     * "response"": {}
    }"
     */
    public function getListTradingAccountNumber(Request $request) {
        $inputs = $this->validate($request, [
            'params.fun_distributor_id' => 'required',
        ]);
        $params = $inputs['params'];
        $list_accounts = Investor::where('fund_distributor_id', $params['fun_distributor_id'])->get();
        return $this->responseSuccess(Investor::getArrayForSelectBox($list_accounts, 'id', 'trading_account_number'));
    }

    /**
     * "@bodyParam env string required Enviroment client. Example: web
     *  @bodyParam app_version string required App version. Example: 1.1
     *  @bodyParam lang string required Language. Example: vi
     *  @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     *  @bodyParam investor_id integer Investor ID. Example: 1"
     * "@response {
     * "status"": true,
     * "code"": 200,
     * "messages"": ""Success"",
     * "response"": {}
    }"
     */
    public function getDetailAndSipProducts(Request $request) {
        $inputs = $this->validate($request, [
            'params.investor_id' => 'required'
        ]);
        $params = $inputs['params'];
        $investor = Investor::findOrFail($params['investor_id']);
        $data = new Investor();
        $data->name = $investor->name;
        $data->id_number = $investor->id_number;
        $data->id_issuing_date = $investor->id_issuing_date;
        $data->au_fullname = $investor->au_fullname;

        return $this->responseSuccess($data);
    }
}
