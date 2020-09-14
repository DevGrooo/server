<?php

use App\Models\FundCertificate;
use App\Models\FundDistributor;
use App\Models\FundDistributorProduct;
use App\Models\FundProduct;
use App\Services\Transactions\FundDistributorProductTransaction;
use App\Services\Transactions\InvestorFundProductTransaction;
use App\Services\Transactions\InvestorTransaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $fund_company_id = $this->_getFundCompanyIdByCode('Mira-Access');
        $fund_certificate_id = DB::table('fund_certificates')->insertGetId([
            'fund_company_id' => $fund_company_id,
            'name' => 'Qũy MAGEF',
            'code' => 'MAGEF',
            'status' => FundCertificate::STATUS_ACTIVE,
            'created_by' => 0,
            'updated_by' => 0,
        ]);
        $fund_product_type_id = $this->_getFundProductTypeIdByCode('NORMAL');
        $fund_product_id = DB::table('fund_products')->insertGetId([
            'fund_company_id' => $fund_company_id,
            'fund_certificate_id' => $fund_certificate_id,
            'fund_product_type_id' => $fund_product_type_id,
            'name' => 'Sản phẩm MAGEF',
            'code' => 'MAGEF',
            'status' => FundProduct::STATUS_ACTIVE,
            'created_by' => 0,
            'updated_by' => 0,
        ]);
        $fund_distributor_id = DB::table('fund_distributors')->insertGetId([
            'fund_company_id' => $fund_company_id,
            'name' => 'Đại lý phân phối 1',
            'code' => '701',
            'status' => FundDistributor::STATUS_ACTIVE,
            'created_by' => 0,
            'updated_by' => 0,
        ]);
        (new FundDistributorProductTransaction)->create([
            'fund_product_id' => $fund_product_id, 
            'fund_distributor_id' => $fund_distributor_id, 
            'supervising_bank_id' => 1, 
            'account_holder' => 'DAI LY PHAN PHOI 1', 
            'account_number' => '90359462904', 
            'branch' => 'HA NOI', 
            'status' => FundDistributorProduct::STATUS_ACTIVE, 
            'created_by' => 0,
        ], true);
        // $bank_id = 1;

        // $query = "INSERT INTO `investors` (`id`, `fund_company_id`, `fund_distributor_id`, `fund_distributor_staff_id`, `referral_bank_id`, `trading_account_number`, `trading_reference_number`, `trading_account_type`, `name`, `zone_type`, `scale_type`, `invest_type`, `country_id`, `birthday`, `gender`, `id_type_id`, `id_number`, `id_issuing_date`, `id_issuing_place`, `id_expiration_date`, `permanent_address`, `permanent_country_id`, `current_address`, `current_country_id`, `phone`, `fax`, `email`, `tax_id`, `tax_country_id`, `visa_number`, `visa_issuing_date`, `visa_issuing_place`, `temporary_address`, `re_fullname`, `re_birthday`, `re_gender`, `re_position`, `re_id_type`, `re_id_number`, `re_id_issuing_date`, `re_id_issuing_place`, `re_id_expiration_date`, `re_phone`, `re_address`, `re_country_id`, `au_fullname`, `au_id_type`, `au_id_number`, `au_id_issuing_date`, `au_id_issuing_place`, `au_id_expiration_date`, `au_email`, `au_phone`, `au_address`, `au_country_id`, `au_start_date`, `au_end_date`, `fatca_link_auth`, `fatca_recode`, `fatca_funds`, `fatca1`, `fatca2`, `fatca3`, `fatca4`, `fatca5`, `fatca6`, `fatca7`, `status`, `vsd_status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
        // (1, 1, 1, 0, NULL, '701C123456', NULL, 1, 'Lê Huy Phương', 1, 1, 1, 1, '2020-08-21 00:00:00', 1, 1, '47437347437', '2020-08-21 00:00:00', 'ha noi', NULL, 'Ba Đình, Hà Nội', NULL, 'Ba Đình, Hà Nội', NULL, '0977827477', '', 'lehuyphuong1982@gmail.com', '7378778373', NULL, NULL, NULL, NULL, NULL, 'LÊ HUY PHUONG', NULL, NULL, 'Nhân viên', NULL, '34437634734', '2020-08-21 00:00:00', 'ha noi', NULL, '0977827477', 'Ba Đình, Hà Nội', 1, 'Lê Huy Phương', NULL, '23236236236', '2020-08-21 00:00:00', 'Ha Noi', NULL, NULL, '0977827477', 'Ba Đình, Hà Nội', 1, '2020-08-21 00:00:00', '2021-09-30 00:00:00', '', '', 'fatca1,fatca2', 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, NULL, '2020-08-21 02:58:35', '2020-08-21 02:58:35');";
        // DB::insert(DB::raw($query));

        // $query = "INSERT INTO `investor_bank_accounts` (`id`, `bank_id`, `fund_company_id`, `fund_distributor_id`, `investor_id`, `account_holder`, `account_number`, `branch`, `description`, `is_default`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
        // (1, 1, 1, 1, 1, 'le huy phuong', '56093490634', 'Dong do', '', 1, 1, 0, 0, '2020-08-21 02:58:35', '2020-08-21 02:58:35');";
        // DB::insert(DB::raw($query));

        // (new InvestorFundProductTransaction)->create([
        //     'investor_id' => 1, 
        //     'fund_product_id' => $fund_product_id, 
        //     'created_by' => 0,
        // ], true);
    }

    /**
     * @param string fund_company_code
     * @return integer
     */
    private function _getFundCompanyIdByCode($fund_company_code)
    {
        $model = DB::table('fund_company')->where('code', strtoupper($fund_company_code))->first();
        if ($model) {
            return $model->id;
        }
        return 0;
    }

    /**
     * @param string fund_product_type_code
     * @return integer
     */
    private function _getFundProductTypeIdByCode($fund_product_type_code)
    {
        $model = DB::table('fund_product_types')->where('code', strtoupper($fund_product_type_code))->first();
        if ($model) {
            return $model->id;
        }
        return 0;
    }
}
