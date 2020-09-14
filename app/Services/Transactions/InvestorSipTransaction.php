<?php

namespace App\Services\Transactions;
use App\Models\InvestorSip;
use Carbon\Carbon;

/**
 * @author MSI
 * @version 1.0
 * @created 31-Aug-2020 5:17:06 PM
 */
class InvestorSipTransaction extends Transaction
{
	/**
	 *
	 * @param params
	 * @param allow_commit
	 */
	public function active(array $params, $allow_commit = false)
	{
	}

	/**
	 *
	 * @param params
	 * @param allow_commit
	 */
	public function stop(array $params, $allow_commit = false)
	{
	}

	/**
	 *
	 * @param params
	 * @param allow_commit
	 */
	public function create(array $params, $allow_commit = false)
	{
	    $response = null;
	    $this->beginTransaction($allow_commit);
	    try {
	        $model = InvestorSip::create([
                'fund_company_id' => $params['fund_company_id'],
                'fund_certificate_id' => $params['fund_certificate_id'],
                'fund_product_type_id' => $params['fund_product_type_id'],
                'fund_product_id' => $params['fund_product_id'],
                'fund_distributor_id' => $params['fund_distributor_id'],
                'investor_id' => $params['investor_id'],
                'payment_type' => $params['payment_type'],
                'periodic_amount' => $params['periodic_amount'],
                'start_at' => Carbon::now(),
                'status' => $params['status'],
                'created_by' => $params['created_by']
            ]);
	        if ($model) {
	            $response['id'] = $model->id;
            } else {
	            $this->error('Có lỗi khi thêm mới SIP');
            }
        } catch (\Exception $e) {
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
	}

	/**
	 *
	 * @param params
	 * @param allow_commit
	 */
	public function pause(array $params, $allow_commit = false)
	{
	}

	public function update(array $params, $allow_commit = false) {
	    $response = null;
        $this->beginTransaction($allow_commit);
        $investorSip = InvestorSip::findOrFail($params['id']);
        try {
            if ($investorSip) {
                $investorSip->fund_company_id = $params['fund_company_id'];
                $investorSip->fund_certificate_id = $params['fund_certificate_id'];
                $investorSip->fund_product_type_id = $params['fund_product_type_id'];
                $investorSip->fund_product_id = $params['fund_product_id'];
                $investorSip->fund_distributor_id = $params['fund_distributor_id'];
                $investorSip->investor_id = $params['investor_id'];
                $investorSip->payment_type = $params['payment_type'];
                $investorSip->periodic_amount = $params['periodic_amount'];
                $investorSip->start_at = Carbon::now();
                $investorSip->status = $params['status'];
                $investorSip->created_by = $params['created_by'];
                //save
                $update = $investorSip->save();
                if (!$update) {
                    $this->errors('Có lỗi xảy ra khi cập nhập SIP');
                }
            }
        } catch (\Exception $e) {
            $this->rollback($allow_commit, $e);
        }
        return $this->response($allow_commit, $response);
    }

}
