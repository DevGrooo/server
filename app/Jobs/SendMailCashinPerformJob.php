<?php

namespace App\Jobs;

use App\Models\Cashin;
use App\Services\Mails\Mail;

class SendMailCashinPerformJob extends MailJob
{
    protected $cashin;    
    protected $locale;
    
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Cashin $cashin, String $locale)
    {
        $this->cashin = $cashin;
        $this->locale = $locale;
    }

    protected function _toEmail()
    {
        return trim($this->cashin->investor->email);
    }

    protected function _toName()
    {
        return trim($this->cashin->investor->name);
    }
 
    protected function _getTemplateCode()
    {
        return 'CASHIN_PERFORM';
    }

    protected function _getLocale()
    {
        return $this->locale;
    }

    protected function _getData()
    {
        return [];
    }

}
