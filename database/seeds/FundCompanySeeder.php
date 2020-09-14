<?php

use App\Models\FundCompany;
use App\Models\FundDistributor;
use App\Services\Transactions\FundDistributorTransaction;

require_once('MySeeder.php');

class FundCompanySeeder extends MySeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rows = array(
            ['name' => 'Mira Access', 'code' => 'Mira-Access'],
        );
        foreach($rows as $row) {
            $company = FundCompany::create([
                'name' => $row['name'],
                'code' => $row['code'],
                'status' => FundCompany::STATUS_ACTIVE,
                'created_by' => 0,
                'updated_by' => 0,
            ]);
        }
        
    }
}
