<?php

require_once('MySeeder.php');

class BankSeeder extends MySeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $query = "
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng TMCP An Bình', 'Ngân hàng TMCP An Bình', 'ABB', '0031/NH-GP', null, '170 Hai Bà Trưng, phường DaKao, quận 1, Tp. Hồ Chí Minh', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng TMCP Á Châu', 'Ngân hàng TMCP Á Châu', 'ACB', '0032/NHGP', null, '442 Nguyễn Thị Minh Khai, Quận 3, Tp. Hồ Chí Minh', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng Nông nghiệp và Phát triển nông thôn Việt Nam', 'Ngân hàng Nông nghiệp và Phát triển nông thôn Việt Nam', 'AGR', '280/QĐ-NH5', null, '36 Nguyễn Cơ Thạch, khu đô thị Mỹ Đình I, Từ Liêm, Hà Nội', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng TNHH Một thành viên ANZ Việt Nam', 'Ngân hàng TNHH Một thành viên ANZ Việt Nam', 'AVL', '0103134809', null, 'Tầng 4,6,7,12 tòa nhà Sun City, số 13 Hai Bà Trưng, Tràng Tiền, Hoàn Kiếm, Hà Nội', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng TMCP Bắc Á', 'Ngân hàng TMCP Bắc Á', 'BAB', '0052/NHGP', null, '117 Quang Trung, Tp. Vinh, Nghệ An', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng TMCP Bảo Việt', 'Ngân hàng TMCP Bảo Việt', 'BAOVB', '328/GP-NHNN', null, 'Số 8 Lý Thái Tổ, quận Hoàn Kiếm, Hà Nội', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng TMCP Đầu tư và Phát triển Việt Nam', 'Ngân hàng TMCP Đầu tư và Phát triển Việt Nam', 'BID', '84/GP-NHNN', null, 'Tháp BIDV 35 Hàng Vôi, Quận Hoàn Kiếm, Hà Nội', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng TMCP Bản Việt', 'Ngân hàng TMCP Bản Việt', 'BVB', '0025/NHGP', null, 'tòa nhà số 112-114-116-118 đường Hai Bà Trưng, phường ĐaKao, quận 1, TP. Hồ Chí Minh', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Công ty tài chính cổ phần Xi Măng', 'Công ty tài chính cổ phần Xi Măng', 'CFC', '142/GP-NHNN', null, '28 Bà Triệu, phường Hàng Bài, Quận Hoàn Kiếm, Hà Nội', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng Citibank N.A. Việt Nam', 'Ngân hàng Citibank N.A. Việt Nam', 'Citibank', '13/NH-GP', null, 'Tòa nhà Sunwah, 115 Nguyễn Huệ, Quận 1, TPHCM, Việt Nam', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng Commonwealth Bank of Australia', 'Ngân hàng Commonwealth Bank of Australia', 'CMWB', '03/GP-NHNN', null, '65 Nguyễn Du, Q1, TPHCM', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng Hợp tác xã Việt Nam', 'Ngân hàng Hợp tác xã Việt Nam', 'COOPB', '166/GP-NHNN', null, '15T Nguyễn Thị Định, Cầu Giấy, Hà Nội', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng TMCP Công thương Việt Nam', 'Ngân hàng TMCP Công thương Việt Nam', 'CTG', '142/GP.NHNN', null, '108 Trần Hưng Đạo, Hà Nội', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng TMCP Đại Á (T. Đồng Nai)', 'Ngân hàng TMCP Đại Á (T. Đồng Nai)', 'DAB', '0036/NHGP', null, '56-58 Cách mạng Tháng 8, Tp. Biên Hòa, Đồng Nai', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Deutsche Bank AG', 'Deutsche Bank AG', 'DBH', '20/NHGP', null, 'Lầu 14 Saigon Centre, 65 Lê Lợi, Quận 1, TP.HCM', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng TMCP Đông Á', 'Ngân hàng TMCP Đông Á', 'EAB', '0009/NH-GP', null, '130 Phan Đăng Lưu, P.3, Q. Phú Nhuận, Tp. HCM', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng TMCP Xuất Nhập Khẩu Việt Nam', 'Ngân hàng TMCP Xuất Nhập Khẩu Việt Nam', 'EIB', '0011/NHGP', null, 'Tầng 8 Vincom Center - 72 Lê Thánh Tôn và 47 Lý Tự Trọng, phường Bến Nghé - Quận 1 - Tp. Hồ Chí Minh', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng Far East National Bank - Chi nhánh Hồ Chí Minh', 'Ngân hàng Far East National Bank - Chi nhánh Hồ Chí Minh', 'FEN', '03/NHNN-GP', null, 'Tòa nhà Trung tâm VP Saigon Riverside, 2A-4A,Tôn Đức Thắng, Quận 1. TP. Hồ Chí Minh', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng TMCP Dầu Khí Toàn Cầu', 'Ngân hàng TMCP Dầu Khí Toàn Cầu', 'GPB', '0043 NH/GP', null, '109 Trần Hưng Đạo, Hoàn Kiếm, Hà Nội', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng TMCP Phát triển Tp. Hồ Chí Minh', 'Ngân hàng TMCP Phát triển Tp. Hồ Chí Minh', 'HDB', '0019/NHGP', null, '25 Bis Nguyễn Thị Minh Khai, phường ĐaKao, quận 1, TP. Hồ Chí Minh', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng TNHH MTV Hong Leong Việt Nam', 'Ngân hàng TNHH MTV Hong Leong Việt Nam', 'HLOB', '342/GP-NHNN', null, 'Tầng 1, tòa nhà Centec, 72-74 Nguyễn Thị Minh Khai, P6, Q3, TPHCM', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng TNHH Một thành viên HSBC (Việt Nam)', 'Ngân hàng TNHH Một thành viên HSBC (Việt Nam)', 'HSB', '235/GP-NHNN', null, '235 Đồng Khởi, phường Bến Nghé, quận 1, TP. Hồ Chí Minh', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng TNHH Indovina', 'Ngân hàng TNHH Indovina', 'IVB', '135/GP-NHGP', null, '39 Hàm Nghi, Quận 1, Tp. Hồ Chí Minh', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng TMCP Kiên Long', 'Ngân hàng TMCP Kiên Long', 'KLB', '0054/NH-GP', null, '44 Phạm Hồng Thái, Tp. Rạch Giá, tỉnh Kiên Giang', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng TMCP Bưu điện Liên Việt', 'Ngân hàng TMCP Bưu điện Liên Việt', 'LPB', '91/GP-NHNN', null, '32 Nguyễn Công Trứ, thị xã Vị Thanh, tỉnh Hậu Giang', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng liên doanh Lào Việt', 'Ngân hàng liên doanh Lào Việt', 'LVB', 'NHLDNN', null, 'Số 44, Đại lộ Lane Xang, Viêng Chăn, Cộng Hòa Dân Chủ Nhân Dân Lào', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng TMCP Quân Đội', 'Ngân hàng TMCP Quân Đội', 'MBB', '0054/NHGP', null, '03 Liễu Giai, Quận Ba Đình, Hà Nội', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng TMCP Phát triển Mê Kông', 'Ngân hàng TMCP Phát triển Mê Kông', 'MDB', '0022/NHGP', null, '248 Trần Hưng Đạo, Thị xã Long Xuyên, An Giang', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng TMCP Phát triển Nhà Đồng Bằng Sông Cửu Long', 'Ngân hàng TMCP Phát triển Nhà Đồng Bằng Sông Cửu Long', 'MHB', '769/TTg', null, 'Số 9 Võ Văn Tần, Quận 3, TPHCM', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng TMCP Hàng Hải Việt Nam', 'Ngân hàng TMCP Hàng Hải Việt Nam', 'MSB', '0001/NHGP', null, 'Tòa nhà Sky Tower A-88 Láng Hạ, Quận Đống Đa, Hà Nội', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng TMCP Nam Á', 'Ngân hàng TMCP Nam Á', 'NAB', '0026/NHGP', null, '201-203 Cách mạng tháng 8, phường 4, Quận 3, TPHCM', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng TMCP Quốc Dân', 'Ngân hàng TMCP Quốc Dân', 'NCB', '000057/NH-GP', null, '28C-28D Bà Triệu, phường Hàng Bài, quận Hoàn Kiếm, Hà Nội', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng Nhà Nước Việt Nam', 'Ngân hàng Nhà Nước Việt Nam', 'NHNN', '96/2008/NĐ-CP', null, '49 Lý Thái Tổ, Hoàn Kiếm, Hà Nội', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng TMCP Nam Việt', 'Ngân hàng TMCP Nam Việt', 'NVB', '0057/NHGP', null, '343 Phạm Ngũ Lão, Quận 1, Tp. Hồ Chí Minh', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng TMCP Phương Đông', 'Ngân hàng TMCP Phương Đông', 'OCB', '0061-NHGP', null, '45 Lê Duẩn, quận 1, Tp. Hồ Chí Minh', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng TMCP Đại Dương', 'Ngân hàng TMCP Đại Dương', 'OJB', '0048/NH-GP', null, 'Số 199 đường Nguyễn Lương Bằng, phường Thanh Bình, Thành phố Hải Dương', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng TMCP Xăng dầu Petrolimex', 'Ngân hàng TMCP Xăng dầu Petrolimex', 'PGB', '0045/NHGP', null, 'Tòa nhà Mipec 229 Tây Sơn, Đống Đa, Hà Nội', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng TMCP Phương Nam', 'Ngân hàng TMCP Phương Nam', 'PNB', '0030/NHGP', null, '279 Lý Thường Kiệt, Quận 11, Thành phố Hồ Chí Minh', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng TMCP Đại chúng Việt Nam', 'Ngân hàng TMCP Đại chúng Việt Nam', 'PVcombank', '279/GP-NHNN', null, '22 Ngô Quyền, Q. Hoàn Kiếm, Hà Nội', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Tổng Công ty tài chính cổ phần Dầu khí Việt Nam', 'Tổng Công ty tài chính cổ phần Dầu khí Việt Nam', 'PVF', '72/GP-NHNN', null, '20 Ngô Quyền - Hoàn Kiếm - Hà Nội', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng Raiffeisen Bank International AG', 'Ngân hàng Raiffeisen Bank International AG', 'RBI', '03/2000/NHGPVP2', null, '6 Phùng Khắc Khoan, Quận 1, Tp. Hồ Chí Minh', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng TNHH Một thành viên Standard Chartered (Việt Nam)', 'Ngân hàng TNHH Một thành viên Standard Chartered (Việt Nam)', 'SCB', '236GP-NHNN', null, 'Tào nhà Kengnam, lô E6, khu đô thị mới Cầu Giấy, xã Mễ Trì, huyện Từ Liêm, Hà Nội', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Công ty tài chính cổ phần Sông Đà', 'Công ty tài chính cổ phần Sông Đà', 'SDF', '137/GP-NHNN', null, 'Tầng 2, tòa nhà HH4 Sông Đà, TWIN TOWER, đường Phạm Hùng, Mễ Trì, Hà Nội', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng TMCP Đông Nam Á', 'Ngân hàng TMCP Đông Nam Á', 'SEAV', '0051/NH-GP', null, '25 Trần Hưng Đạo, Hoàn Kiếm, Hà Nội', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng TMCP Sài Gòn', 'Ngân hàng TMCP Sài Gòn', 'SGB', '238/GP-NHNN', null, '927 Trần Hưng Đạo, Phường 1, Quận 5, TPHCM', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng TMCP Sài Gòn Công Thương', 'Ngân hàng TMCP Sài Gòn Công Thương', 'SGCTB', '0034/NHGP', null, 'Số 2C Phó Đức Chính, Quận 1, TP. HCM', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng TMCP Sài Gòn - Hà Nội', 'Ngân hàng TMCP Sài Gòn - Hà Nội', 'SHB', '0041/NH-GP', null, '77 Trần Hưng Đạo, Hoàn Kiếm, Hà Nội', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng TNHH MTV Shinhan Việt Nam', 'Ngân hàng TNHH MTV Shinhan Việt Nam', 'Shinhan Viet Nam', '0309103635', null, '138-142 Hai Bà Trưng, Phường Da Kao, Quận 1 , TP HCM', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Sumitomo Mitsui Banking Corporation', 'Sumitomo Mitsui Banking Corporation', 'SMBC', '1855/GP-NHNN', null, 'Tầng 6, Tòa nhà Pacific, 83 Lý Thường Kiệt, Hà Nội', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng TMCP Sài Gòn Thương Tín', 'Ngân hàng TMCP Sài Gòn Thương Tín', 'STB', '0006/NHGP', null, '266-268 Nam Kỳ Khởi Nghĩa - Quận 3 - Tp. Hồ Chí Minh', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng TMCP Kỹ Thương', 'Ngân hàng TMCP Kỹ Thương', 'TCB', '0040/NHGP', null, '191 Bà Triệu, Hà Nội', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng TMCP Tiên Phong', 'Ngân hàng TMCP Tiên Phong', 'TPB', '123/GP-NHNN', null, 'Tòa nhà FPT phố Duy Tân, phường Dịch Vọng Hậu, Cầu Giấy, Hà Nội', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng United Overseas Bank Việt Nam', 'Ngân hàng United Overseas Bank Việt Nam', 'UOB', '18/NH-GP', null, 'Tầng B1, Tầng Trệt và tầng 15 tòa nhà Central Plaza, số 17 Đại lộ Lê Duẩn, Quận 1, TP. Hồ Chí Minh', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng TMCP Việt Á', 'Ngân hàng TMCP Việt Á', 'VAB', '12/NHGP', null, '115- 121 Nguyễn Công Trứ, phường Nguyễn Thái Bình, Quận 1, Tp. Hồ Chí Minh', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng TMCP Ngoại Thương Việt Nam', 'Ngân hàng TMCP Ngoại Thương Việt Nam', 'VCB', '286/QĐ-NH5', null, '198 Trần Quang Khải, Hoàn Kiếm, Hà Nội', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng TMCP Quốc Tế Việt Nam', 'Ngân hàng TMCP Quốc Tế Việt Nam', 'VIB', '0060/NHGP', null, '198B Tây Sơn, Hà Nội', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng VID Public Bank', 'Ngân hàng VID Public Bank', 'VIDPB', '01/NHGP', null, '53 Quang Trung - Hà Nội', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng TMCP Việt Nam Thương Tín', 'Ngân hàng TMCP Việt Nam Thương Tín', 'VNB', '2399/QĐ-NHNN', null, 'Tầng 2 Tòa nhà Trần Hưng Đạo, Tp. Sóc Trăng, tỉnh Sóc Trăng', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng TM TNHH MTV Xây dựng Việt Nam', 'Ngân hàng TM TNHH MTV Xây dựng Việt Nam', 'VNCB', '250/QĐ-NHNN', null, '145-147-149 Hùng Vương, phường 2, TP Tân An, Long An', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng TMCP Việt Nam Thịnh Vượng', 'Ngân hàng TMCP Việt Nam Thịnh Vượng', 'VPB', '0042/NHGP', null, 'Số 8 Lê Thái Tổ, Hoàn Kiếm, Hà Nội', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng Liên doanh Việt Nga', 'Ngân hàng Liên doanh Việt Nga', 'VRB', '11/GP-NHNN', null, 'Số 1 Yết Kiêu, Hoàn Kiếm, Hà Nội', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Trung tâm lưu ký chứng khoán Việt Nam', 'Trung tâm lưu ký chứng khoán Việt Nam', 'VSD', 'VSD', null, '15 Đoàn Trần Nghiệp', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Ngân hàng TMCP Phương Tây', 'Ngân hàng TMCP Phương Tây', 'WEB', '0061/NHGP', null, '127 Lý Tự Trọng, phường An Phú, Quận Ninh Kiều, Tp. Cần Thơ', 1);
        INSERT INTO banks(name, trade_name, code, bcardno, bcarddate, address, status) VALUES('Woori Bank - Chi nhánh Hồ Chí Minh', 'Woori Bank - Chi nhánh Hồ Chí Minh', 'WRBHCM', '1854/GP-NHNN', null, '115 Nguyễn Huệ, Quận 1, TP.HCM', 1);
        ";
        $this->_executeMultiRaw($query);
    }

    
}
