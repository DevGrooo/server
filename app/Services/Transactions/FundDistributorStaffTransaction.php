<?php
namespace App\Services\Transactions;

use App\Models\FundDistributorStaff;
use App\Models\FundDistributor;

/**
 * @author MSI
 * @version 1.0
 * @created 30-Jul-2020 4:18:37 PM
 */

class FundDistributorStaffTransaction extends Transaction
{
	/**
	 * 
	 * @param $params [
	 * fund_distributor_staff_id,
	 * updated_by
	 * ]
	 * @param $allow_commit
	 */
	public function active(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
            $model = FundDistributorStaff::find($params['fund_distributor_staff_id']);
			if ($model) {
				if ($model->status == FundDistributorStaff::STATUS_LOCK) {
					$model->status = FundDistributorStaff::STATUS_ACTIVE;
					$model->updated_by = $params['updated_by'];
					if (!$model->save()) {
						$this->error('Có lỗi khi kích hoạt nhân viên kinh doanh đại lý phân phối');
					}
				} else {
					$this->error('Nhân viên kinh doanh đại lý phân phối không hợp lệ');
				}				
			} else {
				$this->error('Nhân viên kinh doanh đại lý phân phối không tồn tại');
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
	 * certificate_number,
	 * issuing_date,
	 * phone,
	 * email,
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
				$this->error('Đại lý phân phối không tồn tại');
			}
            $model = FundDistributorStaff::create([
				'fund_company_id' => $fund_distributor->fund_company_id,
				'fund_distributor_id' => $params['fund_distributor_id'],				
				'name' => $params['name'],
				'certificate_number' => $params['certificate_number'],
				'issuing_date' => $params['issuing_date'],
				'email' => $params['email'],
				'phone' => $params['phone'],				
				'status' => $params['status'],
				'created_by' => $params['created_by']
			]);
			if ($model) {
				$response['fund_distributor_staff_id'] = $model->id;
			} else {
				$this->error('Có lỗi khi thêm nhân viên kinh doanh cho đại lý phân phối');
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
	 * fund_distributor_staff_id,
	 * name,
	 * certificate_number,
	 * issuing_date,
	 * phone,
	 * email,
	 * updated_by
	 * ]
	 * @param $allow_commit
	 */
	public function update(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {			
			$model = FundDistributorStaff::find($params['fund_distributor_staff_id']);
			if ($model) {
				if (!$model->update([
					'name' => $params['name'],
					'certificate_number' => $params['certificate_number'],
					'issuing_date' => $params['issuing_date'],
					'phone' => $params['phone'],
					'email' => $params['email'],
					'updated_by' => $params['updated_by']
				])) {
					$this->error('Có lỗi khi cập nhật nhân viên kinh doanh đại lý phân phối');
				}
			} else {
				$this->error('Nhân viên kinh doanh đại lý phân phối không tồn tại');
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
	 * fund_distributor_staff_id,
	 * updated_by
	 * ]
	 * @param $allow_commit
	 */
	public function lock(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
            $model = FundDistributorStaff::find($params['fund_distributor_staff_id']);
			if ($model) {
				if ($model->status == FundDistributorStaff::STATUS_ACTIVE) {
					$model->status = FundDistributorStaff::STATUS_LOCK;
					$model->updated_by = $params['updated_by'];
					if (!$model->save()) {
						$this->error('Có lỗi khi khóa nhân viên kinh doanh đại lý phân phối');
					}
				} else {
					$this->error('Nhân viên kinh doanh đại lý phân phối không hợp lệ');
				}				
			} else {
				$this->error('Nhân viên kinh doanh đại lý phân phối không tồn tại');
			}
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
	}

}