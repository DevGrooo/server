<?php

namespace App\Services\Transactions;

use App\Models\TradingFrequency;
use App\Models\TradingSession;

class TradingFrequencyTransaction extends Transaction
{
    /**
     * @param array $params
     * @param boolean $allow_commit
     * @return array
     * @throws \Exception
     */
    public function create($params, $allow_commit = false)
    {
        $response = null;
        $this->beginTransaction($allow_commit);
        try {
            $newTradingFrequency = new TradingFrequency();
            $newTradingFrequency->fund_company_id = $params['fund_company_id'];
            $newTradingFrequency->type = $params['type'];
            //Kiểm tra type = 1 or type = 2
            if ($newTradingFrequency->type == 1) {
                $newTradingFrequency->mday = null;
                $newTradingFrequency->wday = $params['wday'];
            } else if ($newTradingFrequency->type == 2) {
                $newTradingFrequency->wday = null;
                $newTradingFrequency->mday = $params['mday'];
            } else {
                $this->error('Không tồn tại loại này !');
            }
            $newTradingFrequency->name = $params['name'];
            $newTradingFrequency->cut_off_date = $params['cut_off_date'];
            $newTradingFrequency->cut_off_hour = $params['cut_off_hour'];
            $newTradingFrequency->cut_off_time = $params['cut_off_hour'] . " on T-" . $params['cut_off_date'];
            $newTradingFrequency->status = $params['status'];
            $newTradingFrequency->created_by = $params['created_by'];
            $create = $newTradingFrequency->save();
            if ($create) {
                if ($params['status'] == TradingFrequency::STATUS_ACTIVE) {
                    (new TradingSessionTransaction)->create([
                        'previous_trading_session_id' => 0,
                        'trading_frequency_id' => $newTradingFrequency->id,
                        'created_by' => $params['created_by']
                    ]);
                }
            } else {
                $this->error('Có lỗi xảy ra khi thêm tần suất giao dịch !');
            }
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
    }
    /**
     * @param array $params
     * @param boolean $allow_commit
     * @return array
     * @throws \Exception
     */
    public function lock($params, $allow_commit = false)
    {
        $response = null;
        $this->beginTransaction($allow_commit);
        try {
            $trading_Frequency = TradingFrequency::where('id', '=', $params['trading_frequency_id'])->first();
            if ($trading_Frequency) {
                if ($trading_Frequency::STATUS_ACTIVE) { //Lock user
                    $trading_Frequency->status = TradingFrequency::STATUS_LOCK;
                    $trading_Frequency->updated_by = $params['updated_by'];
                    $lock = $trading_Frequency->save();
                    if (!$lock) {
                        $this->error('Có lỗi xảy ra khi khóa tần suất giao dịch !');
                    }
                } else {
                    $this->error('Tần suất giao dịch này không hợp lệ');
                }
            } else {
                $this->error('Không tìm thấy tần suất giao dịch này');
            }
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
    }
    /**
     * @param array $params
     * @param boolean $allow_commit
     * @return array
     * @throws \Exception
     */
    public function active($params, bool $allow_commit = true)
    {
        $response = null;
        $this->beginTransaction($allow_commit);
        try {
            $trading_Frequency = TradingFrequency::where('id', '=', $params['trading_frequency_id'])->first();
            if ($trading_Frequency) {
                if ($trading_Frequency->status == TradingFrequency::STATUS_LOCK) { //Lock user
                    $trading_Frequency->status = TradingFrequency::STATUS_ACTIVE;
                    $trading_Frequency->updated_by = $params['updated_by'];
                    $trading_Frequency->save();
                    $active = $trading_Frequency->save();
                    if ($active) {
                        $trading_session = TradingSession::where('trading_frequency_id', $trading_Frequency->id)
                            ->orderBy('start_at', 'DESC')
                            ->first();
                        if ($trading_session) {
                            if (!$trading_session->isAllowOrder()) {
                                (new TradingSessionTransaction)->updateStatusTimeUp([
                                    'trading_session_id' => $trading_session->id,
                                    'updated_by' => $params['updated_by']
                                ]);
                            }
                        } else {
                            (new TradingSessionTransaction)->create([
                                'previous_trading_session_id' => 0,
                                'trading_frequency_id' => $trading_Frequency->id,
                                'created_by' => $params['updated_by']
                            ]);
                        }
                    } else {
                        $this->error('Có lỗi xảy ra khi mở khóa tần suất giao dịch !');
                    }
                } else {
                    $this->error('Tần suất giao dịch này không hợp lệ');
                }
            } else {
                $this->error('Không tìm thấy tần suất giao dịch này');
            }
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
    }
}
