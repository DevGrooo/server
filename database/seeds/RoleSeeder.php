<?php

require_once('MySeeder.php');

class RoleSeeder extends MySeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->_makeRoleUserBasic();
    }

    /**
     * Add role user basic
     */
    private function _makeRoleUserBasic()
    {
        $query = "
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(0, 'Thông tin tài khoản', '/profile', '', 0, 6, 1, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(0, 'Sửa thông tin tài khoản', '/profile/update', '', 0, 7, 1, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(0, 'Đổi mật khẩu', '/profile/change-password', '', 0, 8, 1, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(1, 'Quản trị người dùng', '/user', '', 0, 9, 1, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(1, 'Tạo tài khoản người dùng', '/user/create', '', 0, 10, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(1, 'Xem chi tiết người dùng', '/user/detail', '', 0, 11, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(1, 'Cập nhật người dùng', '/user/update', '', 0, 12, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(1, 'Khóa người dùng', '/user/lock', '', 0, 13, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(1, 'Mở khóa người dùng', '/user/active', '', 0, 14, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(1, 'Cập nhật quyền truy cập', '/user/update-role', '', 0, 15, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(1, 'Reset mật khẩu', '/user/reset-password', '', 0, 16, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(2, 'Quản lý quỹ', '/fund-certificate', '', 0, 17, 1, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(2, 'Thêm quỹ', '/fund-certificate/create', '', 0, 18, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(2, 'Cập nhật quỹ', '/fund-certificate/update', '', 0, 19, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(2, 'Kích hoạt quỹ', '/fund-certificate/active', '', 0, 20, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(2, 'Khóa quỹ', '/fund-certificate/lock', '', 0, 21, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(2, 'Sản phẩm quỹ', '/fund-product', '', 0, 22, 1, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(2, 'Thêm sản phẩm quỹ', '/fund-product/create', '', 0, 23, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(2, 'Câp nhật sản phẩm quỹ', '/fund-product/update', '', 0, 24, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(2, 'Mở khóa sản phẩm quỹ', '/fund-product/active', '', 0, 25, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(2, 'Khóa sản phẩm quỹ', '/fund-product/lock', '', 0, 26, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(2, 'Loại sản phẩm quỹ', '/fund-product-type', '', 0, 27, 1, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(2, 'Thêm loại sản phẩm quỹ', '/fund-product-type/create', '', 0, 28, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(2, 'Cập nhật loại sản phẩm quỹ', '/fund-product-type/update', '', 0, 29, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(2, 'Khóa loại sản phẩm quỹ', '/fund-product-type/lock', '', 0, 30, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(2, 'Mở khóa loại sản phẩm quỹ', '/fund-product-type/active', '', 0, 31, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(2, 'Quốc gia', '/country', '', 0, 32, 1, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(2, 'Thêm quốc gia', '/country/create', '', 0, 33, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(2, 'Cập nhật quốc gia', '/country/update', '', 0, 34, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(2, 'Khóa quốc gia', '/country/lock', '', 0, 35, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(2, 'Mở khóa quốc gia', '/country/active', '', 0, 36, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(2, 'Ngân hàng', '/bank', '', 0, 37, 1, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(2, 'Thêm ngân hàng', '/bank/create', '', 0, 38, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(2, 'Cập nhật ngân hàng', '/bank/update', '', 0, 39, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(2, 'Mở khóa ngân hàng', '/bank/active', '', 0, 40, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(2, 'Khóa ngân hàng', '/bank/lock', '', 0, 41, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(2, 'Ngân hàng giới thiệu', '/referral-bank', '', 0, 42, 1, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(2, 'Thêm ngân hàng giới thiệu', '/referral-bank/create', '', 0, 43, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(2, 'Cập nhật ngân hàng giới thiệu', '/referral-bank/update', '', 0, 44, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(2, 'Mở khóa ngân hàng giới thiệu', '/referral-bank/active', '', 0, 45, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(2, 'Khóa ngân hàng giới thiệu', '/referral-bank/lock', '', 0, 46, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(2, 'Tần xuất giao dịch', '/trading-frequency', '', 0, 47, 1, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(2, 'Thêm tần xuất giao dịch', '/trading-frequency/create', '', 0, 48, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(2, 'Mở khóa tần xuất giao dịch', '/trading-frequency/active', '', 0, 49, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(2, 'Khóa tần xuất giao dịch', '/trading-frequency/lock', '', 0, 50, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(2, 'Phiên giao dịch', '/trading-session', '', 0, 51, 1, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(2, 'Hủy phiên giao dịch', '/trading-session/cancel', '', 0, 52, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(5, 'Lệnh giao dịch', '/trading-order', '', 0, 53, 1, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(5, 'Thêm lệnh giao dịch', '/trading-order/create', '', 0, 54, 1, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(5, 'Hủy lệnh giao dịch', '/trading-order/cancel', '', 0, 55, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(5, 'Import lệnh giao dịch', '/trading-order/import', '', 0, 56, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(5, 'Export lệnh giao dịch', '/trading-order/export', '', 0, 57, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(5, 'Export VSD lệnh giao dịch', '/trading-order/export-vsd', '', 0, 58, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(5, 'Đối chiếu với sao kê', '/trading-order/collate', '', 0, 59, 1, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(2, 'Phí mua chứng chỉ', '/trading-order-fee-buy', '', 0, 60, 1, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(2, 'Thêm phí mua chứng chỉ', '/trading-order-fee-buy/create', '', 0, 61, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(2, 'Khóa phí mua chứng chỉ', '/trading-order-fee-buy/lock', '', 0, 62, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(2, 'Phí bán chứng chỉ', '/trading-order-fee-sell', '', 0, 63, 1, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(2, 'Thêm phí bán chứng chỉ', '/trading-order-fee-sell/create', '', 0, 64, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(2, 'Khóa phí bán chứng chỉ', '/trading-order-fee-sell/lock', '', 0, 65, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(7, 'Phiếu thu', '/cashin', '', 0, 66, 1, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(7, 'Cập nhật thanh toán phiếu thu', '/cashin/paid', '', 0, 67, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(7, 'Câp nhật nhà đầu tư', '/cashin/update-investor', '', 0, 68, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(7, 'Import sao kê', '/cashin/import-statement', '', 0, 69, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(13, 'Yêu cầu rút tiền', '/cashout-request', '', 0, 70, 1, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(13, 'Duyệt yêu cầu rút tiền', '/cashout-request/accept', '', 0, 71, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(13, 'Từ chối yêu cầu rút tiền', '/cashout-request/reject', '', 0, 72, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(11, 'Tài khoản hoa hồng', '/account-commission', '', 0, 73, 1, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(11, 'Kích hoạt tài khoản hoa hồng', '/account-commission/active', '', 0, 74, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(11, 'Khóa tài khoản hoa hồng', '/account-commission/lock', '', 0, 75, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(11, 'Cấu hình hoa hồng', '/setting-commission', '', 0, 76, 1, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(11, 'Thêm cấu hình hoa hồng', '/setting-commission/create', '', 0, 77, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(11, 'Khóa cấu hình hoa hồng', '/setting-commission/lock', '', 0, 78, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(11, 'Ghi nhận hoa hồng', '/transaction-commission', '', 0, 79, 1, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(4, 'Tài khoản ngân hàng NĐT', '/investor-bank-account', '', 0, 80, 1, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(4, 'Thêm tài khoản ngân hàng NĐT', '/investor-bank-account/create', '', 0, 81, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(4, 'Kích hoạt tài khoản ngân hàng NĐT', '/investor-bank-account/active', '', 0, 82, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(4, 'Khóa tài khoản ngân hàng NĐT', '/investor-bank-account/lock', '', 0, 83, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(4, 'Đặt làm mặc định tài khoản ngân hàng NĐT', '/investor-bank-account/set-default', '', 0, 84, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(4, 'Nhà đầu tư', '/investor', '', 0, 85, 1, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(4, 'Thêm nhà đầu tư', '/investor/create', '', 0, 86, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(4, 'Cập nhật nhà đầu tư', '/investor/update', '', 0, 87, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(4, 'Từ chối kích hoạt tài khoản nhà đầu tư', '/investor/reject', '', 0, 88, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(4, 'Kích hoạt tài khoản nhà đầu tư', '/investor/active', '', 0, 89, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(4, 'Gửi mail thông báo tài khoản kích hoạt', '/investor/send-email', '', 0, 90, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(4, 'Import file excel', '/investor/import-file-excel', '', 0, 91, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(4, 'Export file excel', '/investor/export-file-excel', '', 0, 92, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(4, 'Hủy tài khoản nhà đầu tư', '/investor/cancel', '', 0, 93, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(4, 'Đóng tài khoản nhà đầu tư', '/investor/closed', '', 0, 94, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(4, 'Mở lại tài khoản nhà đầu tư', '/investor/reopen', '', 0, 95, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(4, 'Export VSD', '/investor/export-vsd', '', 0, 96, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(4, 'Sản phẩm nhà đầu tư', '/investor-fund-product', '', 0, 97, 1, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(4, 'Thêm sản phẩm nhà đầu tư', '/investor-fund-product/create', '', 0, 98, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(4, 'Khóa sản phẩm nhà đầu tư', '/investor-fund-product/lock', '', 0, 99, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(4, 'Mở khóa sản phẩm nhà đầu tư', '/investor-fund-product/active', '', 0, 100, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(3, 'Đại lý phân phối', '/fund-distributor', '', 0, 101, 1, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(3, 'Thêm đại lý phân phối', '/fund-distributor/create', '', 0, 102, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(3, 'Cập nhật đại lý phân phối', '/fund-distributor/update', '', 0, 103, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(3, 'Khóa đại lý phân phối', '/fund-distributor/lock', '', 0, 104, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(3, 'Kích hoạt đại lý phân phối', '/fund-distributor/active', '', 0, 105, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(3, 'Sản phẩm đại lý phân phối', '/fund-distributor-product', '', 0, 106, 1, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(3, 'Thêm sản phẩm đại lý phân phối', '/fund-distributor-product/create', '', 0, 107, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(3, 'Cập nhật sản phẩm đại lý phân phối', '/fund-distributor-product/update', '', 0, 108, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(3, 'Kích hoạt sản phẩm đại lý phân phối', '/fund-distributor-product/active', '', 0, 109, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(3, 'Khóa sản phẩm đại lý phân phối', '/fund-distributor-product/lock', '', 0, 110, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(3, 'Chi nhánh đại lý phân phối', '/fund-distributor-location', '', 0, 111, 1, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(3, 'Thêm chi nhánh đại lý phân phối', '/fund-distributor-location/create', '', 0, 112, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(3, 'Cập nhật chi nhánh đại lý phân phối', '/fund-distributor-location/update', '', 0, 113, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(3, 'Khóa chi nhánh đại lý phân phối', '/fund-distributor-location/lock', '', 0, 114, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(3, 'Kích hoạt chi nhánh đại lý phân phối', '/fund-distributor-location/active', '', 0, 115, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(10, 'Nhân viên kinh doanh', '/fund-distributor-staff', '', 0, 116, 1, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(10, 'Thêm nhân viên kinh doanh', '/fund-distributor-staff/create', '', 0, 117, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(10, 'Cập nhật nhân viên kinh doanh', '/fund-distributor-staff/update', '', 0, 118, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(10, 'Khóa nhân viên kinh doanh', '/fund-distributor-staff/lock', '', 0, 119, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(10, 'Kích hoạt nhân viên kinh doanh', '/fund-distributor-staff/active', '', 0, 120, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(2, 'Mail template', '/mail-template', '', 0, 121, 1, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(2, 'Thêm nội dung mail template', '/mail-template/create', '', 0, 122, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(2, 'Cập nhật nội dung mail template', '/mail-template/update', '', 0, 123, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(6, 'Quản lý SIP', '/investor-sip', '', 0, 124, 1, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(6, 'Thêm SIP', '/investor-sip/create', '', 0, 125, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(6, 'Cập nhật SIP', '/investor-sip/update', '', 0, 126, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(6, 'Kích hoạt SIP', '/investor-sip/active', '', 0, 127, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(6, 'Tạm ngừng SIP', '/investor-sip/pause', '', 0, 128, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(6, 'Đóng SIP', '/investor-sip/stop', '', 0, 129, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(12, 'SR0044', '/report-sr0044', '', 0, 130, 1, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(12, 'Import SR0044', '/report-sr0044/import', '', 0, 131, 2, 1, 1, 1);
        INSERT INTO roles(role_group_id, name, code, description, parent_id, position, publish, status, created_by, updated_by) VALUES(5, 'Danh sách giao dịch quỹ', '/transaction-fund', '', 0, 131, 1, 1, 1, 1);
        ";
        $this->_executeMultiRaw($query);
        // update parent_id
        $query = "UPDATE roles, (SELECT id, code FROM `roles` WHERE code NOT LIKE '/%/%') temp SET roles.parent_id = temp.id WHERE roles.code LIKE CONCAT(temp.code, '/%') AND roles.code LIKE '/%/%';";
        $this->_executeMultiRaw($query);
    }

    
}
