<?php

namespace App\Services\Transactions;

use App\Models\Api;
use App\Models\Role;
use App\Models\RoleApi;
use App\Models\FundProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FundProductTransaction extends Transaction {
    /**
     * @param array $params
     * @param boolean $allow_commit
     * @return array
     * @throws \Exception
     */
    public function add($params, $allow_commit = false)
    {
        $response = null;
        $this->beginTransaction($allow_commit);
        try {
        $code_exist = FundProduct::where('code','=',$params['code'])->first();
        if($code_exist){
            $this->error('Code này đã tồn tại');
        }else {
           $new_fund_product = new FundProduct();
           $new_fund_product->fund_company_id = $params['fund_company_id'];
           $new_fund_product->fund_certificate_id = $params['fund_certificate_id'];
           $new_fund_product->fund_product_type_id = $params['fund_product_type_id'];
           $new_fund_product->name = $params['name'];
           $new_fund_product->code = $params['code'];
           $new_fund_product->description = $params['description'];
           $new_fund_product->status = $params['status'];
           $new_fund_product->created_by = $params['created_by'];
           $create = $new_fund_product->save();
            if(!$create){
                $this->error('Có lỗi xảy ra khi thêm quỹ sản phẩm !');
            }
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
            $fund_product = FundProduct::where('id', '=' , $params['fund_product_id'])->first();
            if ($fund_product){
                $fund_product->name = $params['name'];
                $fund_product->description = $params['description'];
                $fund_product->updated_by = $params['update_by'];
                $update = $fund_product->save();
                if(!$update){
                    $this->error('Có lỗi xảy ra khi sửa quỹ sản phẩm !');
                }
            }else {
                $this->error('Không tìm thấy quỹ sản phẩm này ');
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
    public function lock($params, $allow_commit = false){
        $response = null;
        $this->beginTransaction($allow_commit);
        try {
            //Check user id exist
            $fund_product = FundProduct::where('id', '=', $params['fund_product_id'])->first();
            if ($fund_product) {
                if ($fund_product::STATUS_ACTIVE) { //Lock user
                    $fund_product->status = FundProduct::STATUS_LOCK;
                    $fund_product->updated_by = $params['updated_by'];
                    $lock = $fund_product->save();
                    if(!$lock){
                        $this->error('Có lỗi xảy ra khi khóa quỹ sản phẩm !');
                    }
                } else {
                    $this->error('Quỹ sản phẩm không hợp lệ');
                }
            } else {
                $this->error('Quỹ sản phẩm không tồn tại');
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
    public function active($params, bool $allow_commit = true){
        $response = null;
        $this->beginTransaction($allow_commit);
        try {
            //Check user id exist
            $fund_product_id = FundProduct::where('id', '=', $params['fund_product_id'])->first();
            if($fund_product_id){
                if ($fund_product_id->status == FundProduct::STATUS_LOCK) { //Lock user
                    $fund_product_id->status = FundProduct::STATUS_ACTIVE;
                    $fund_product_id->updated_by = $params['updated_by'];
                    $active = $fund_product_id->save();
                    if(!$active){
                        $this->error('Có lỗi xảy ra khi mở khóa quỹ sản phẩm !');
                    }
                } else {
                    $this->error('Quỹ sản phẩm không hợp lệ');
                }
            }else {
                $this->error('Quỹ sản phẩm này không tồn tại');
            }
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
    }
}
