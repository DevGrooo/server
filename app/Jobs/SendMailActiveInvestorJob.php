<?php

namespace App\Jobs;

use App\Models\Investor;

class SendMailActiveInvestorJob extends MailJob
{
    protected $investor;    
    protected $locale;
    
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Investor $investor, String $locale)
    {
        $this->investor = $investor;
        $this->locale = $locale;
    }

    protected function _toEmail()
    {
        return trim($this->investor->email);
    }

    protected function _toName()
    {
        return trim($this->investor->name);
    }
 
    protected function _getTemplateCode()
    {
        return 'ACTIVE_INVESTOR';
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
