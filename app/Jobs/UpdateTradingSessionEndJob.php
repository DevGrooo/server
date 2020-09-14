<?php

namespace App\Jobs;

use App\Models\TradingSession;
use App\Services\Transactions\TradingSessionTransaction;
use Illuminate\Support\Carbon;

class UpdateTradingSessionEndJob extends Job
{
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // tìm kiếm các phiên giao dịch đã kết thúc mà chưa cập nhật trạng thái kết thúc và tiến hành cập nhật
        $trading_sessions = TradingSession::where('end_at', '<=', Carbon::now())->where('status', TradingSession::STATUS_ACTIVE)->get();
        if ($trading_sessions) {
            $transaction = new TradingSessionTransaction();
            foreach ($trading_sessions as $trading_session) {
                $transaction->updateStatusEnd([
                    'trading_session_id' => $trading_session->id, 
                    'updated_by' => 0,
                ], true);
            }
        }
    }
}
