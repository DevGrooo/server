<?php

namespace App\Imports;

use App\Models\Bank;
use App\Models\IdType;
use App\Models\Investor;
use App\Rules\BankAccountNumberRule;
use App\Rules\IdNumberRule;
use App\Rules\TradingAccountNumberRule;
use App\Rules\VNDateRule;
use Illuminate\Support\Str;

class InvestorImport extends BasicImport
{
    protected function _processValueInRow($row)
    {
        $this->data_result['fund_distributor_code'] = $row['dbcode'];
        $this->data_result['fund_distributor_staff_id'] = 0;
        $this->data_result['referral_bank_id'] = 0;
        $this->data_result['trading_account_number'] = $row['custodycd'];
        $this->data_result['trading_account_type'] = $row['acctype'] == 'TT' ? Investor::TRADING_ACCOUNT_TYPE_DIRECT : Investor::TRADING_ACCOUNT_TYPE_INDIRECT;
        $this->data_result['zone_type'] = $row['grinvestor'] == 'TN' ? Investor::ZONE_TYPE_INTERNAL : Investor::ZONE_TYPE_EXTERNAL;
        $this->data_result['scale_type'] = $row['custtype']  == 'CN' ? Investor::SCALE_TYPE_PERSONAL : Investor::SCALE_TYPE_ORGANIZATION;
        $this->data_result['invest_type'] = $row['investtype'] == 'TT' ? Investor::INVEST_TYPE_NORMAL : Investor::INVEST_TYPE_PROFESSION;
        $this->data_result['name'] = $row['fullname'];
        $this->data_result['gender'] = $row['sex'];
        $this->data_result['country_id'] = $row['country'];
        $this->data_result['birthday'] = $row['birthdate'];
        $this->data_result['id_type_id'] = IdType::getIdByCode($row['idtype']);
        $this->data_result['id_number'] = $row['idcode'];
        $this->data_result['id_issuing_date'] = $row['iddate'];
        $this->data_result['id_issuing_place'] = $row['idplace'];
        $this->data_result['id_expiration_date'] = null;
        $this->data_result['phone'] = $row['phone'];
        $this->data_result['fax'] = $row['fax'];
        $this->data_result['email'] = $row['email'];
        $this->data_result['tax_id'] = $row['taxno'];
        $this->data_result['tax_country_id'] = null;
        $this->data_result['permanent_address'] = $row['regaddress'];
        $this->data_result['permanent_country_id'] = $row['country'];
        $this->data_result['current_address'] = $row['address'];
        $this->data_result['current_country_id'] = $row['country'];
        $this->data_result['visa_number'] = null;
        $this->data_result['visa_issuing_date'] = null;
        $this->data_result['visa_issuing_place'] = null;
        $this->data_result['temporary_address'] = null;

        $this->data_result['bank_id'] = Bank::getIdByCode($row['bankcode']);
        $this->data_result['account_holder'] = strtoupper(Str::ascii($row['fullname']));
        $this->data_result['account_number'] = str_replace([' ','–'], ['','-'], $row['bankacc']);
        $this->data_result['branch'] = $row['citybank'];
        $this->data_result['description'] = $row['description'];

        $this->data_result['re_fullname'] = $row['refname1'];
        $this->data_result['re_position'] = $row['refpost1'];
        $this->data_result['re_country_id'] = $row['country'];
        $this->data_result['re_id_number'] = $row['refidcode1'];
        $this->data_result['re_id_type'] = null;
        $this->data_result['re_id_issuing_date'] = $row['refiddate1'];
        $this->data_result['re_id_issuing_place'] = $row['refidplace1'];
        $this->data_result['re_phone'] = $row['refmobile1'];
        $this->data_result['re_address'] = $row['refaddress1'];
        $this->data_result['au_fullname'] = $row['authname'];
        $this->data_result['au_id_number'] = $row['authidcode'];
        $this->data_result['au_id_type'] = null;
        $this->data_result['au_id_issuing_date'] = $row['authiddate'];
        $this->data_result['au_id_issuing_place'] = $row['authidplace'];
        $this->data_result['au_id_expiration_date'] = null;
        $this->data_result['au_phone'] = $row['authphone'];
        $this->data_result['au_address'] = $row['authaddress'];
        $this->data_result['au_country_id'] = $row['country'];
        $this->data_result['au_start_date'] = $row['authefdate'];
        $this->data_result['au_end_date'] = $row['authexdate'];
        $this->data_result['fatca_link_auth'] = $row['linkauth'];
        $this->data_result['fatca_recode'] = $row['recode'];
        $this->data_result['fatca_funds'] = explode(',', $row['symbols']);
        $this->data_result['fatca1'] = $row['fatca1'] == 'Y' ? 1 : 2;
        $this->data_result['fatca2'] = $row['fatca2'] == 'Y' ? 1 : 2;
        $this->data_result['fatca3'] = $row['fatca3'] == 'Y' ? 1 : 2;
        $this->data_result['fatca4'] = $row['fatca4'] == 'Y' ? 1 : 2;
        $this->data_result['fatca5'] = $row['fatca5'] == 'Y' ? 1 : 2;
        $this->data_result['fatca6'] = $row['fatca6'] == 'Y' ? 1 : 2;
        $this->data_result['fatca7'] = $row['fatca7'] == 'Y' ? 1 : 2;
        // check trading account number & name
        $investor = Investor::where('trading_account_number', $this->data_result['trading_account_number'])->first();
        if ($investor) {
            $this->data_result['investor_id'] = $investor->id;
            if ($investor->name != $this->data_result['name']) {
                $this->_addWarning('Tên nhà đầu tư không khớp với dữ liệu đã có');
            }
        } else {
            $this->data_result['investor_id'] = 0;
        }
    }    
    
    protected function _getRulesValidateRow($row)
    {
        $rules = array(
            'dbcode' => 'required|regex:/(^[A-Z0-9]{3}$)/u|exists:fund_distributors,code',
            'custodycd' => ['required', new TradingAccountNumberRule($row['dbcode'], $row['grinvestor'])],
            'acctype' => ['required', 'regex:/(TT|GT)/u'],
            'fullname' => 'required|string',
            'idtype' => 'required|exists:id_types,code',
            'idcode' => ['required', new IdNumberRule],
            'iddate' => ['required', new VNDateRule],
            'idplace' => 'required|string',
            'birthdate' => new VNDateRule,
            'sex' => ['regex:/(F|M|O)/u'],
            'country' => 'required|exists:country,id',
            'taxno' => 'string',
            'regaddress' => 'string',
            'address' => 'string',
            'phone' => 'string',
            'fax' => 'string',
            'email' => 'email',
            'investtype' => ['required', 'regex:/(TT|CN)/'],
            'custtype' => ['required', 'regex:/(TC|CN)/'],
            'grinvestor' => ['required', 'regex:/(TN|NN)/'],
            'bankacc' => ['required', new BankAccountNumberRule],
            'bankcode' => 'required|exists:banks,code',
            'citybank' => 'required|string',
            'description' => 'string',
            'refname1' => 'string',
            'refpost1' => 'string',
            'refidcode1' => new IdNumberRule,
            'refiddate1' => new VNDateRule,
            'refidplace1' => 'string',
            'refcountry1' => 'string',
            'refmobile1' => 'string',
            'refaddress1' => 'string',
            'authname' => 'string',
            'authidcode' => new IdNumberRule,
            'authiddate' => new VNDateRule,
            'authidplace' => 'string',
            'authphone' => 'string',
            'authaddress' => 'string',
            'authefdate' => new VNDateRule,
            'authexdate' => new VNDateRule,
            'linkauth' => 'string',
            'recode' => 'string',
            'symbols' => 'required|string',
            'fatca1' => ['required', 'regex:/(Y|N)/'],
            'fatca2' => ['required', 'regex:/(Y|N)/'],
            'fatca3' => ['required', 'regex:/(Y|N)/'],
            'fatca4' => ['required', 'regex:/(Y|N)/'],
            'fatca5' => ['required', 'regex:/(Y|N)/'],
            'fatca6' => ['required', 'regex:/(Y|N)/'],
            'fatca7' => ['required', 'regex:/(Y|N)/'],
        );
        return $rules;
    }
}