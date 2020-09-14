<?php

namespace App\Jobs;

use App\Models\TradingOrder;

class SendMailVerifyBuyTradingOrderJob extends MailJob
{
    protected $trading_order;    
    protected $locale;
    
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(TradingOrder $trading_order, String $locale)
    {
        $this->trading_order = $trading_order;
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
        return 'VERIFY_BUY_TRADING_ORDER';
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
