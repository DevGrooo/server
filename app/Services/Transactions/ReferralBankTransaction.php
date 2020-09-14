<?php

namespace App\Services\Transactions;

use App\Models\ReferralBank;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReferralBankTransaction extends Transaction
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
        $referral_bank_exist = ReferralBank::where('name',$params['name'])->first();
        if ($referral_bank_exist == null){
            $trade_name_exist = ReferralBank::where('trade_name', $params['trade_name'])->first();
            if ($trade_name_exist == null) {
                $referral_bank = new ReferralBank();
                $referral_bank->fund_company_id = $params['fund_company_id'];
                $referral_bank->name = $params['name'];
//                $referral_bank->logo = $params['logo'];
                $referral_bank->trade_name = $params['trade_name'];
                $referral_bank->status = $params['status'];
                $referral_bank->created_by = $params['created_by'];
                $create = $referral_bank->save();
                if(!$create){
                    $this->error('Có lỗi xảy ra khi thêm ngân hàng giới thiệu !');
                }
            }else {
                $this->error('Tên Thương Mại Ngân Hàng giới thiệu đã tồn tại');
            }
        }else {
            $this->error('Tên Ngân Hàng giới thiệu đã tồn tại');
        }
        }catch (\Exception $e) {
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
    public function update($params, $allow_commit = false)
    {
        $response = null;
        $this->beginTransaction($allow_commit);
        try {
            $referral_bank = ReferralBank::where('id', $params['referral_bank_id'])->first();
            if ($referral_bank) {
                $name_referral_bank_exist = ReferralBank::whereNotIn('id',[$params['referral_bank_id']])->where('name', $params['name'])->first();
                if(!$name_referral_bank_exist) {
                    $trade_name_exist = ReferralBank::whereNotIn('id',[$params['referral_bank_id']])->where('trade_name', $params['trade_name'])->first();
                    if (!$trade_name_exist) {
                        $referral_bank->name = $params['name'];
                        $referral_bank->trade_name = $params['trade_name'];
                        $referral_bank->updated_by = $params['updated_by'];
                        $update = $referral_bank->save();
                        if(!$update){
                            $this->error('Có lỗi xảy ra khi cập nhật ngân hàng giới thiệu  !');
                        }
                    } else {
                        $this->error('Tên thương mại của ngân hàng giới thiệu đã tồn tại  !');
                    }
                } else {
                    $this->error('Tên ngân hàng giới thiệu đã tồn tại  !');
                }

            }else {
                $this->error('Ngân hàng giới thiệu này không tồn tại');
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
            $referral_bank = ReferralBank::where('id', $params['referral_bank_id'])->first();
            if ($referral_bank) {
                if ($referral_bank->status == $referral_bank::STATUS_ACTIVE) {
                    $referral_bank->status = $referral_bank::STATUS_LOCK;
                    $referral_bank->updated_by = $params['updated_by'];
                    $lock = $referral_bank->save();
                    if(!$lock){
                        $this->error('Có lỗi xảy ra khi khóa ngân hàng giới thiệu !');
                    }
                }
            } else {
                $this->error('Ngân hàng giới thiệu không tồn tại');
            }
        }catch (\Exception $e) {
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
    public function active($params, $allow_commit = false)
    {
        $response = null;
        $this->beginTransaction($allow_commit);
        try {
            $referral_bank = ReferralBank::where('id', $params['referral_bank_id'])->first();
            if ($referral_bank) {
                if ($referral_bank->status == $referral_bank::STATUS_LOCK) {
                    $referral_bank->status =  $referral_bank::STATUS_ACTIVE;
                    $referral_bank->updated_by = $params['updated_by'];
                    $active = $referral_bank->save();
                    if(!$active){
                        $this->error('Có lỗi xảy ra khi mở khóa ngân hàng giới thiệu !');
                    }
                }
            } else {
                $this->error('Ngân hàng giới thiệu không tồn tại');
            }
        }catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
    }
}
