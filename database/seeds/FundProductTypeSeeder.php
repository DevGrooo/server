<?php

use App\Models\FundProductType;

require_once('MySeeder.php');

class FundProductTypeSeeder extends MySeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rows = array(
            ['name' => 'Loại SIP', 'code' => 'SIP', 'description' => ''],
            ['name' => 'Loại thông thường', 'code' => 'NORMAL', 'description' => ''],
        );
        foreach($rows as $row) {
            FundProductType::create([
                'name' => $row['name'],
                'code' => $row['code'],
                'description' => $row['description'],
                'status' => FundProductType::STATUS_ACTIVE,
                'created_by' => 0,
                'updated_by' => 0,
            ]);
        }
        
    }
}
