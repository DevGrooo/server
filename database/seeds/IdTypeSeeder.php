<?php

require_once('MySeeder.php');

class IdTypeSeeder extends MySeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $query = "
        INSERT INTO `id_types` (`name`, `code`, `status`) VALUES('CMND/Căn cước CD', '001', 1);
        INSERT INTO `id_types` (`name`, `code`, `status`) VALUES('Hộ chiếu', '002', 1);
        INSERT INTO `id_types` (`name`, `code`, `status`) VALUES('Giấy phép lái xe', '003', 1);
        INSERT INTO `id_types` (`name`, `code`, `status`) VALUES('Chứng thư khác', '004', 1);
        INSERT INTO `id_types` (`name`, `code`, `status`) VALUES('Giấy phép kinh doanh', '005', 1);
        INSERT INTO `id_types` (`name`, `code`, `status`) VALUES('Mã số thuế', '006', 1);
        INSERT INTO `id_types` (`name`, `code`, `status`) VALUES('Mã giao dịch', '009', 1);
        ";
        $this->_executeMultiRaw($query);
    }

    
}
