<?php


namespace App\Services\Transactions;


use App\Models\FundProduct;
use App\Models\FundProductType;
use App\Models\User;

class FundProductTypeTransaction extends Transaction
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
            $code_exist = FundProductType::where('code', $params['code'])->first();
            if ($code_exist == null) {
                $fund_product_type = new FundProductType();
                $fund_product_type->name = $params['name'];
                $fund_product_type->code = $params['code'];
                $fund_product_type->description = $params['description'];
                $fund_product_type->status = $params['status'];
                $fund_product_type->created_by = $params['created_by'];
                $create = $fund_product_type->save();
                if(!$create) {
                    $this->error('Có lỗi xảy ra khi thêm loại sản phẩm quỹ !');
                }
            } else {
                $this->error('Mã Code này đã tồn tại');
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
            $fund_produdct_type = FundProductType::where('id',$params['fund_product_type_id'])->first();
            if ($fund_produdct_type){
                        $fund_produdct_type->name = $params['name'];
                        $fund_produdct_type->description = $params['description'];
                        $fund_produdct_type->updated_by = $params['updated_by'];
                        $update = $fund_produdct_type->save();
                        if(!$update) {
                            $this->error('Có lỗi xảy ra khi cập nhật loại sản phẩm quỹ !');
                        }
                } else {
                    $this->error('Loại sản phẩm quỹ này không tồn tại');
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
    public function lock($params, $allow_commit = false)
    {
        $response = null;
        $this->beginTransaction($allow_commit);
        try {
            $fund_product_type = FundProductType::where('id', $params['fund_product_type_id'])->first();
//            dd($fund_product_type);
            if ($fund_product_type) {
                if ($fund_product_type->status == $fund_product_type::STATUS_ACTIVE) {
                    $fund_product_type->status = $fund_product_type::STATUS_LOCK;
                    $lock = $fund_product_type->save();
                    if(!$lock) {
                        $this->error('Có lỗi xảy ra khi khóa loại sản phẩm quỹ !');
                    }
                } else {
                    $this->error('Loại sản phẩm qũy không hợp lệ');
                }
            } else {
                $this->error('Loại sản phẩm qũy không tồn tại');
            }
        }catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
    }

    public function active($params, $allow_commit = false)
    {
        $response = null;
        $this->beginTransaction($allow_commit);
        try {
            $fund_product_type = FundProductType::where('id', $params['fund_product_type_id'])->first();
//            dd($fund_product_type);
            if ($fund_product_type) {
                if ($fund_product_type->status == $fund_product_type::STATUS_LOCK) {
                    $fund_product_type->status = $fund_product_type::STATUS_ACTIVE;
                    $active = $fund_product_type->save();
                    if(!$active){
                        $this->error('Có lỗi xảy ra khi mở khóa loại sản phẩm quỹ !');
                    }
                } else {
                    $this->error('Loại sản phẩm qũy không hợp lệ');
                }
            } else {
                $this->error('Loại sản phẩm qũy không tồn tại');
            }
        }catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
    }
}
