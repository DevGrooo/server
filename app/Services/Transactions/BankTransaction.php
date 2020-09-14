<?php


namespace App\Services\Transactions;


use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class BankTransaction extends Transaction
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
//        dd($params);
        $this->beginTransaction($allow_commit);
         try {
             $name_bank_exist = Bank::where('name', $params['name'])->first();
             if ($name_bank_exist == null) {
                 $trade_name_exist = Bank::where('trade_name', $params['trade_name'])->first();
                 if ($trade_name_exist == null) {
                     $bank = new Bank();
                     $bank->name = $params['name'];
                     $bank->trade_name = $params['trade_name'];
                     $bank->status = $params['status'];
//                     $bank->logo = $params['logo'];
                     $bank->created_by = $params['created_by'];
                     $create = $bank->save();
                     if(!$create){
                         $this->error('Có lỗi xảy ra khi thêm ngân hàng !');
                     }
                 } else {
                     $this->error('Tên Thương Mại Ngân Hàng đã tồn tại');
                 }
             } else {
                 $this->error('Tên Ngân Hàng đã tồn tại');
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
     public function update($params, $allow_commit = false)
     {
        $response = null;
        $this->beginTransaction($allow_commit);
         try {
             $bank = Bank::where('id', $params['bank_id'])->first();
             if ($bank) {
                 $name_bank_exist = Bank::whereNotIn('id',[$params['bank_id']])->where('name', $params['name'])->first();
                 if(!$name_bank_exist) {
                     $trade_name_exist = Bank::whereNotIn('id',[$params['bank_id']])->where('trade_name', $params['trade_name'])->first();
                     if (!$trade_name_exist) {
                         $bank->name = $params['name'];
                         $bank->trade_name = $params['trade_name'];
                         $bank->updated_by = $params['updated_by'];
                         $update = $bank->save();
                         if(!$update){
                             $this->error('Có lỗi xảy ra khi cập nhật ngân hàng !');
                         }
                     } else {
                         $this->error('Tên thương mại của ngân hàng đã tồn tại!');
                     }
                 } else {
                     $this->error('Tên ngân hàng đã tồn tại !');
                 }

             }else {
                 $this->error('Ngân hàng này không tồn tại');
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
            $bank = Bank::where('id', $params['bank_id'])->first();
            if ($bank) {
                if ($bank->status == $bank::STATUS_ACTIVE) {
                    $bank->status = $bank::STATUS_LOCK;
                    $lock = $bank->save();
                    if(!$lock){
                        $this->error('Có lỗi xảy ra khi khóa ngân hàng !');
                    }
                }
            } else {
                $this->error('Ngân hàng không tồn tại');
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
            $bank = Bank::where('id', $params['bank_id'])->first();
            if ($bank) {
                if ($bank->status == $bank::STATUS_LOCK) {
                    $bank->status =  $bank::STATUS_ACTIVE;
                    $active = $bank->save();
                    if(!$active){
                        $this->error('Có lỗi xảy ra khi mở khóa ngân hàng !');
                    }
                }
            } else {
                $this->error('Ngân hàng không tồn tại');
            }
        }catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
    }


}
