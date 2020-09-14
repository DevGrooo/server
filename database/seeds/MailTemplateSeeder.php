<?php

require_once('MySeeder.php');

class MailTemplateSeeder extends MySeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $query = "
        INSERT INTO `mail_templates` (`id`, `name`, `code`, `description`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES (1, 'Gửi mail thông báo cho nhà đầu tư khi phiếu thu được chuyển ngân', 'CASHIN_PERFORM', NULL, '1', NULL, NULL, NULL, NULL);
        ";
        $this->_executeMultiRaw($query);
    }

    
}
