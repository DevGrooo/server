<?php

namespace App\Services\Transactions;

use App\Models\AccountCommission;
use App\Models\FundDistributor;

/**
 * @author MSI
 * @version 1.0
 * @created 13-Jul-2020 3:33:29 PM
 */
class FundDistributorTransaction extends Transaction
{
	/**
	 * 
	 * @param params
	 * @param allow_commit
	 */
	public function active(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
            $distributor = FundDistributor::find($params['fund_distributor_id']);
			if ($distributor) {
				if ($distributor->status == FundDistributor::STATUS_LOCK) {
					$distributor->status = FundDistributor::STATUS_ACTIVE;
					$distributor->updated_by = $params['updated_by'];
					if (!$distributor->save()) {
						$this->error('Có lỗi khi kích hoạt đại lý phân phối');
					}
				} else {
					$this->error('Đại lý phân phối không hợp lệ');
				}				
			} else {
				$this->error('Đại lý phân phối không tồn tại');
			}
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
	}

	/**
	 * 
	 * @param array params [
	 * fund_company_id,
	 * name,
	 * code,
	 * license_number,
	 * issuing_date,
	 * head_office,
	 * phone,
	 * website,
	 * status,
	 * created_by
	 * ]
	 * @param boolean allow_commit
	 * @return array [fund_distributor_id]
	 */
	public function create(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$distributor = FundDistributor::create($params);
			if ($distributor) {
				$response['fund_distributor_id'] = $distributor->id;
				$result = (new AccountCommissionTransaction())->create([
					'ref_id' => $distributor->id,
					'ref_type' => 'fund_distributor',
					'status' => AccountCommission::STATUS_ACTIVE,
					'created_by' => $params['created_by'],
				]);
				$response['account_commission_id'] = $result['account_commission_id'];
			} else {
				$this->error('Có lỗi khi thêm đại lý phân phối');
			}				
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
	}

	/**
	 * 
	 * @param array params [
	 * fund_distributor_id,
	 * updated_by
	 * ]
	 * @param boolean allow_commit
	 */
	public function lock(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$distributor = FundDistributor::find($params['fund_distributor_id']);
			if ($distributor) {
				if ($distributor->status == FundDistributor::STATUS_ACTIVE) {
					$distributor->status = FundDistributor::STATUS_LOCK;
					$distributor->updated_by = $params['updated_by'];
					if (!$distributor->save()) {
						$this->error('Có lỗi khi khóa đại lý phân phối');
					}
				} else {
					$this->error('Đại lý phân phối không hợp lệ');
				}				
			} else {
				$this->error('Đại lý phân phối không tồn tại');
			}
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
	}

	/**
	 * 
	 * @param array params[
	 * name,
	 * code,
	 * license_number,
	 * issuing_date,
	 * head_office,
	 * phone,
	 * website,
	 * updated_by
	 * ]
	 * @param allow_commit
	 */
	public function update(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
            $distributor = FundDistributor::find($params['fund_distributor_id']);
			if ($distributor) {
				if (!$distributor->update($params)) {
					$this->error('Có lỗi khi cập nhật đại lý phân phối');
				}
			} else {
				$this->error('Đại lý phân phối không tồn tại');
			}
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
	}
}
