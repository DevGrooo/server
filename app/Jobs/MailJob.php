<?php

namespace App\Jobs;

use App\Services\Mails\Mail;
use App\Models\MailTemplate;
use App\Models\MailTemplateLocale;

abstract class MailJob extends Job
{
    protected $mail_template_locale = null;

    abstract protected function _toEmail();
    abstract protected function _toName();
 
    protected function _getTemplateCode()
    {
        return false;
    }

    protected function _getLocale()
    {
        return '';
    }

    protected function _getData()
    {
        return [];
    }

    protected function _getSubject()
    {
        if ($this->_getMailTemplate()) {
            return $this->mail_template_locale->subject;
        }
        return '';
    }

    protected function _getContent() 
    {
        if ($this->_getMailTemplate()) {
            return $this->mail_template_locale->content;
        }
        return '';
    }

    final protected function _getMailTemplate()
    {
        if ($this->mail_template_locale === null) {
            $this->_setMailTemplate();
        }        
        return $this->mail_template_locale;
    }

    final protected function _setMailTemplate()
    {
        $template_code = $this->_getTemplateCode();
        if ($template_code != false) {                
            $mail_template = MailTemplate::where('code', $template_code)->first();
            if ($mail_template) {
                $mail_template_locale = MailTemplateLocale::where('mail_template_id', $mail_template->id)
                    ->whereIn('locale', [$this->_getLocale(), ''])
                    ->orderBy('locale', 'DESC')->first();
                if ($mail_template_locale) {
                    $this->mail_template_locale = $mail_template_locale;
                    return true;
                }                    
            }
        }
        $this->mail_template_locale = false;
        return false;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    final public function handle()
    {
        // Mail::send($this->_toEmail(), $this->_toName(), $this->_getSubject(), $this->_getContent(), $this->_getData());
    }
}
