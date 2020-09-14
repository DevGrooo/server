<?php
namespace App\Services\Transactions;

use App\Models\FundDistributorLocation;
use App\Models\FundDistributor;

/**
 * @author MSI
 * @version 1.0
 * @created 30-Jul-2020 4:18:37 PM
 */

class FundDistributorLocationTransaction extends Transaction
{
	/**
	 * 
	 * @param $params [
	 * fund_distributor_location_id,
	 * updated_by
	 * ]
	 * @param $allow_commit
	 */
	public function active(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
            $model = FundDistributorLocation::find($params['fund_distributor_location_id']);
			if ($model) {
				if ($model->status == FundDistributorLocation::STATUS_LOCK) {
					$model->status = FundDistributorLocation::STATUS_ACTIVE;
					$model->updated_by = $params['updated_by'];
					if (!$model->save()) {
						$this->error('Có lỗi khi kích hoạt chi nhánh đại lý phân phối');
					}
				} else {
					$this->error('Chi nhánh đại lý phân phối không hợp lệ');
				}				
			} else {
				$this->error('Chi nhánh đại lý phân phối không tồn tại');
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
	 * @param $params[
	 * fund_distributor_id,
	 * name,
	 * address,
	 * phone,
	 * fax,
	 * status,
	 * created_by
	 * ]
	 * @param $allow_commit
	 */
	public function create(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$fund_distributor = FundDistributor::where('id', $params['fund_distributor_id'])->where('status', FundDistributor::STATUS_ACTIVE)->first();
			if (!$fund_distributor) {
				$this->error('Chi nhánh không tồn tại');
			}
            $model = FundDistributorLocation::create([
				'fund_company_id' => $fund_distributor->fund_company_id,
				'fund_distributor_id' => $params['fund_distributor_id'],				
				'name' => $params['name'],
				'address' => $params['address'],
				'phone' => $params['phone'],
				'fax' => $params['fax'],
				'status' => $params['status'],
				'created_by' => $params['created_by']
			]);
			if ($model) {
				$response['fund_distributor_location_id'] = $model->id;
			} else {
				$this->error('Có lỗi khi thêm chi nhánh cho đại lý phân phối');
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
	 * @param $params[	 
	 * fund_distributor_location_id,
	 * name,
	 * address,
	 * phone,
	 * fax,
	 * updated_by
	 * ]
	 * @param $allow_commit
	 */
	public function update(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {			
			$model = FundDistributorLocation::find($params['fund_distributor_location_id']);
			if ($model) {
				if (!$model->update([
					'name' => $params['name'],
					'address' => $params['address'],
					'phone' => $params['phone'],
					'fax' => $params['fax'],
					'updated_by' => $params['updated_by']
				])) {
					$this->error('Có lỗi khi cập nhật chi nhánh đại lý phân phối');
				}
			} else {
				$this->error('Chi nhánh đại lý phân phối không tồn tại');
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
	 * @param $params [
	 * fund_distributor_location_id,
	 * updated_by
	 * ]
	 * @param $allow_commit
	 */
	public function lock(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
            $model = FundDistributorLocation::find($params['fund_distributor_location_id']);
			if ($model) {
				if ($model->status == FundDistributorLocation::STATUS_ACTIVE) {
					$model->status = FundDistributorLocation::STATUS_LOCK;
					$model->updated_by = $params['updated_by'];
					if (!$model->save()) {
						$this->error('Có lỗi khi khóa chi nhánh đại lý phân phối');
					}
				} else {
					$this->error('Chi nhánh đại lý phân phối không hợp lệ');
				}				
			} else {
				$this->error('Chi nhánh đại lý phân phối không tồn tại');
			}
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
	}

}