<?php

namespace App\Services\Transactions;

use App\Models\MailTemplateLocale;

class MailTemplateLocaleTransaction extends Transaction
{

    /**
     * @param array $params
     * @param boolean $allow_commit
     * @return array
     * @throws \Exception
     */
    public function create($params, $allow_commit = false)
    {
        $this->beginTransaction($allow_commit);
        try {
            MailTemplateLocale::create($params);
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }

        // commit and response
        return $this->response($allow_commit);
    }

    /**
     * @param array $params
     * @param boolean allow_commit
     * @return array
     * @throws \Exception
     */
    public function update(array $params, $allow_commit = false)
    {
        $this->beginTransaction($allow_commit);
        try {
            $model = MailTemplateLocale::find($params['mail_template_locale_id']);
            if (!$model->update($params)) {
                $this->error('Có lỗi khi cập nhật mail template locale này');
            }
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }

        // commit and response
        return $this->response($allow_commit);
    }
}
