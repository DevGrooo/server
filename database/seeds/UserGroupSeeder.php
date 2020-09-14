<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin_id = $this->_addRow(0, 'Quản trị hệ thống', 'admin');
        $company_id = $this->_addRow($admin_id, 'Công ty quản lý quỹ', 'fund_company');
        $distributor_id = $this->_addRow($company_id, 'Đại lý phân phối', 'fund_distributor');
        $distributor_staff_id = $this->_addRow($distributor_id, 'Nhân viên đại lý phân phối', 'fund_distributor_staff');
        $investor_id = $this->_addRow($distributor_id, 'Nhà đầu tư', 'investor');
    }

    /**
     * @param integer parent_id
     * @param string name
     * @param string code
     * @return integer
     */
    private function _addRow($parent_id, $name, $code) {
        return DB::table('user_groups')->insertGetId([            
            'parent_id' => $parent_id,
            'name' => $name,
            'code' => strtoupper($code),
            'status' => 1,
            'created_by' => 0,
            'updated_by' => 0,
        ]);
    }
}
