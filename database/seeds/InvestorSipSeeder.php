<?php

use Illuminate\Database\Seeder;

class InvestorSipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 20; $i++) {
            DB::table('investor_sips')->insert([
                'fund_company_id' => 1,
                'fund_certificate_id' => 1,
                'fund_product_type_id' => 2,
                'fund_product_id' => 1,
                'fund_distributor_id' => 1,
                'investor_id' => 1,
                'payment_type' => 1,
                'periodic_amount' => 10000,
                'start_at' => '2020-09-01 11:12:58',
                'status' => 1,
            ]);
        }
    }
}
