<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
    }

    /**
     * Execute multi raw
     * @param string query_raw_inserts
     */
    protected function _executeMultiRaw($query_raw)
    {
        $queries = explode(chr(10),$query_raw);
        if (!empty($queries)) {
            foreach ($queries as $query) {
                $query = trim($query);
                if ($query != '') {
                    DB::insert(DB::raw($query));
                }                
            }
        }
    }
}
