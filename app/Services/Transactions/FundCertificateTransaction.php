<?php

namespace App\Services\Transactions;

use App\Models\FundCertificate;
use App\Models\FundCompany;
use App\Models\FundDistributorProduct;
use App\Models\FundProduct;

class FundCertificateTransaction extends Transaction
{

    /**
     * @param array $params
     * @param boolean $allow_commit
     * @return array
     * @throws \Exception
     */
    public function create($params, $allow_commit = false)
    {
        $this->beginTransaction($allow_commit);
        try {
            FundCertificate::create($params);
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }

        // commit and response
        return $this->response($allow_commit);
    }

    /**
     * @param array $params
     * @param boolean allow_commit
     * @return array
     * @throws \Exception
     */
    public function update(array $params, $allow_commit = false)
    {
        $this->beginTransaction($allow_commit);
        try {
            $model = FundCertificate::find($params['fund_certificate_id']);
            if (!$model->update($params)) {
                $this->error('Có lỗi khi cập nhật chứng chỉ quỹ này');
            }
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }

        // commit and response
        return $this->response($allow_commit);
    }

    /**
     * @param array $params
     * @param boolean allow_commit
     * @return array
     * @throws \Exception
     */
    public function active(array $params, $allow_commit = false)
    {
        $this->beginTransaction($allow_commit);
        try {
            $model = FundCertificate::find($params['fund_certificate_id']);
            $model->status = FundCertificate::STATUS_ACTIVE;
            $model->save();
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }

        // commit and response
        return $this->response($allow_commit, $model);
    }

    /**
     * @param array $params
     * @param boolean allow_commit
     * @return array
     * @throws \Exception
     */
    public function lock(array $params, $allow_commit = false)
    {
        $this->beginTransaction($allow_commit);
        try {
            $model = FundCertificate::find($params['fund_certificate_id']);
            $model->status = FundCertificate::STATUS_LOCK;
            $model->save();
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }

        // commit and response
        return $this->response($allow_commit, $model);
    }

    /**
     * @param array $params
     * @param boolean allow_commit
     * @return array
     * @throws \Exception
     */
    public function getFundProduct(array $params, $allow_commit = false)
    {
        $response= null;
        $this->beginTransaction($allow_commit);
        try {
            $fund_products = FundProduct::where('fund_certificate_id', $params['fund_certificate_id'])->get();
            $response = $fund_products;
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        return $this->responseSuccess(FundProduct::getArrayForSelectBox($fund_products, 'id', 'name'));

    }
}
