<?php
namespace App\Services\Transactions;

use App\Models\FundDistributorBankAccount;
use App\Models\FundDistributorProduct;
use App\Models\FundProduct;

/**
 * @author MSI
 * @version 1.0
 * @created 30-Jul-2020 4:18:37 PM
 */

class FundDistributorProductTransaction extends Transaction
{
	/**
	 * 
	 * @param $params [
	 * fund_distributor_product_id,
	 * updated_by
	 * ]
	 * @param $allow_commit
	 */
	public function active(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
            $model = FundDistributorProduct::find($params['fund_distributor_product_id']);
			if ($model) {
				if ($model->status == FundDistributorProduct::STATUS_LOCK) {
					$model->status = FundDistributorProduct::STATUS_ACTIVE;
					$model->updated_by = $params['updated_by'];
					if (!$model->save()) {
						$this->error('Có lỗi khi kích hoạt sản phẩm đại lý phân phối');
					}
				} else {
					$this->error('Sản phẩm đại lý phân phối không hợp lệ');
				}				
			} else {
				$this->error('Sản phẩm đại lý phân phối không tồn tại');
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
	 * fund_product_id,
	 * fund_distributor_id,
	 * supervising_bank_id,
	 * account_holder,
	 * account_number,
	 * branch,
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
			$fund_product = FundProduct::where('id', $params['fund_product_id'])->where('status', FundProduct::STATUS_ACTIVE)->first();
			if (!$fund_product) {
				$this->error('Sản phẩm không tồn tại');
			}
            $model = FundDistributorProduct::create([
				'fund_product_id' => $params['fund_product_id'],
				'fund_product_type_id' => $fund_product->fund_product_type_id,
				'fund_company_id' => $fund_product->fund_company_id,
				'fund_certificate_id' => $fund_product->fund_certificate_id,
				'fund_distributor_id' => $params['fund_distributor_id'],
				'status' => $params['status'],
				'created_by' => $params['created_by']
			]);
			if ($model) {
				$response['fund_distributor_product_id'] = $model->id;
				$result = (new FundDistributorBankAccountTransaction())->create([
					'fund_distributor_product_id' => $response['fund_distributor_product_id'],
					'supervising_bank_id' => $params['supervising_bank_id'],
					'account_holder' => $params['account_holder'],
					'account_number' => $params['account_number'],
					'branch' => $params['branch'],
					'status' => FundDistributorBankAccount::STATUS_ACTIVE,
					'created_by' => $params['created_by']
				]);
				$response['fund_distributor_bank_account_id'] = $result['fund_distributor_bank_account_id'];
				// update fund_distributor_bank_account_id
				$model->fund_distributor_bank_account_id = $result['fund_distributor_bank_account_id'];
				if (!$model->save()) {
					$this->error('Có lỗi khi thêm sản phẩm cho đại lý phân phối');
				}
			} else {
				$this->error('Có lỗi khi thêm sản phẩm cho đại lý phân phối');
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
	 * fund_distributor_product_id,
	 * fund_distributor_bank_account_id,
	 * supervising_bank_id,
	 * account_holder,
	 * account_number,
	 * branch,
	 * updated_by
	 * ]
	 * @param $allow_commit
	 */
	public function update(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$model = FundDistributorProduct::find($params['fund_distributor_product_id']);
			if ($model) {
				(new FundDistributorBankAccountTransaction())->update([
					'fund_distributor_bank_account_id' => $params['fund_distributor_bank_account_id'],
					'supervising_bank_id' => $params['supervising_bank_id'],
					'account_holder' => $params['account_holder'],
					'account_number' => $params['account_number'],
					'branch' => $params['branch'],
					'updated_by' => $params['updated_by']
				]);
			} else {
				$this->error('Sản phẩm đại lý phân phối không tồn tại');
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
	 * fund_distributor_product_id,
	 * updated_by
	 * ]
	 * @param $allow_commit
	 */
	public function lock(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
            $model = FundDistributorProduct::find($params['fund_distributor_product_id']);
			if ($model) {
				if ($model->status == FundDistributorProduct::STATUS_ACTIVE) {
					$model->status = FundDistributorProduct::STATUS_LOCK;
					$model->updated_by = $params['updated_by'];
					if (!$model->save()) {
						$this->error('Có lỗi khi khóa sản phẩm đại lý phân phối');
					}
				} else {
					$this->error('Sản phẩm đại lý phân phối không hợp lệ');
				}				
			} else {
				$this->error('Sản phẩm đại lý phân phối không tồn tại');
			}
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
	}

}