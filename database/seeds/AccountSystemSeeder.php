<?php

use App\Models\AccountSystem;
use Illuminate\Support\Facades\DB;

require_once('MySeeder.php');

class AccountSystemSeeder extends MySeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $balance = 1000000000000;
        DB::insert('INSERT INTO account_systems (id, fund_product_type_id, ref_type, ref_id, balance, balance_available, balance_freezing, currency, status, created_by) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
            1, 0, '', 0, $balance, $balance, 0, 'VND', 1, 0
        ]);        
    }
}
