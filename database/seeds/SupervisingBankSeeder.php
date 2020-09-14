<?php

use App\Models\SupervisingBank;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupervisingBankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('supervising_banks')->insertGetId([            
            'name' => 'Standard Chartered',
            'trade_name' => 'StandardChartered',
            'logo' => null,
            'status' => SupervisingBank::STATUS_ACTIVE,
            'created_by' => 0,
            'updated_by' => 0,
        ]);
    }
}
