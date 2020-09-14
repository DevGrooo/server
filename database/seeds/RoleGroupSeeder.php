<?php

use Illuminate\Support\Facades\DB;

class RoleGroupSeeder extends MySeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $query = "
        INSERT INTO role_groups(id, name, data, position, status) VALUES(1, 'Hệ thống', 'setting', 12, 1);
        INSERT INTO role_groups(id, name, data, position, status) VALUES(2, 'Cấu hình', 'setting', 13, 1);
        INSERT INTO role_groups(id, name, data, position, status) VALUES(3, 'Đại lý phân phối', 'agents', 6, 1);
        INSERT INTO role_groups(id, name, data, position, status) VALUES(4, 'Tài khoản nhà đầu tư', 'investor', 1, 1);
        INSERT INTO role_groups(id, name, data, position, status) VALUES(5, 'Giao dịch', 'transaction', 2, 1);
        INSERT INTO role_groups(id, name, data, position, status) VALUES(6, 'Quản lý SIP', 'sip', 3, 1);
        INSERT INTO role_groups(id, name, data, position, status) VALUES(7, 'Quản lý phiếu thu', 'cash-flow', 4, 1);
        INSERT INTO role_groups(id, name, data, position, status) VALUES(8, 'Tài sản nhà đầu tư', 'asset', 5, 1);
        INSERT INTO role_groups(id, name, data, position, status) VALUES(9, 'Ngân hàng giới thiệu', 'bank', 7, 1);
        INSERT INTO role_groups(id, name, data, position, status) VALUES(10, 'Nhân viên kinh doanh', 'saleman', 8, 1);
        INSERT INTO role_groups(id, name, data, position, status) VALUES(11, 'Quản lý hoa hồng', 'commission', 9, 1);
        INSERT INTO role_groups(id, name, data, position, status) VALUES(12, 'Báo cáo', 'report', 11, 1);
        INSERT INTO role_groups(id, name, data, position, status) VALUES(13, 'Yêu cầu chi', 'asset', 10, 1);
        ";
        $this->_executeMultiRaw($query);
    }
}
