<?php

namespace App\Services\Transactions;

use App\Models\AccountSystem;
use App\Models\FundDistributor;
use App\Models\FundDistributorBankAccount;
use App\Models\FundDistributorProduct;

/**
 * @author MSI
 * @version 1.0
 * @created 13-Jul-2020 3:26:19 PM
 */
class FundDistributorBankAccountTransaction extends Transaction
{

	/**
	 * 
	 * @param array params[
	 * fund_distributor_bank_account_id,
	 * updated_by
	 * ]
	 * @param allow_commit
	 */
	public function active(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
            $model = FundDistributorBankAccount::find($params['fund_distributor_bank_account_id']);
			if ($model) {
				if ($model->status == FundDistributorBankAccount::STATUS_LOCK) {
					$model->status = FundDistributorBankAccount::STATUS_ACTIVE;
					$model->updated_by = $params['updated_by'];
					if (!$model->save()) {
						$this->error('Có lỗi khi kích hoạt tài khoản ngân hàng');
					}
				} else {
					$this->error('Tài khoản ngân hàng không hợp lệ');
				}				
			} else {
				$this->error('Tài khoản ngân hàng không tồn tại');
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
	 * fund_distributor_product_id,
	 * supervising_bank_id,
	 * account_holder,
	 * account_number,
	 * branch,
	 * status,
	 * created_by
	 * ]
	 * @param allow_commit
	 */
	public function create(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$fund_distributor_product = FundDistributorProduct::find($params['fund_distributor_product_id']);
			if (!$fund_distributor_product) {
				$this->error('Sản phẩm quỹ của đại lý phân phối không tồn tại');
			}
			// create account system
			$obj = new AccountSystemTransaction();
			$result = $obj->create([
				'fund_product_type_id' => $fund_distributor_product->fund_product_type_id,
				'ref_type' => AccountSystem::REF_TYPE_FUND_DISTRIBUTOR,
				'ref_id' => $fund_distributor_product->fund_distributor_id,				
				'status' => AccountSystem::STATUS_ACTIVE,
	 			'created_by' => $params['created_by'],
			]);
			$account_system_id = $result['account_system_id'];
			// create bank account
			$inputs = $params;
			$inputs['account_system_id'] = $account_system_id;
			$model = FundDistributorBankAccount::create([
				'fund_company_id' => $fund_distributor_product->fund_company_id,
            	'fund_certificate_id' => $fund_distributor_product->fund_certificate_id,
				'fund_product_id' =>  $fund_distributor_product->fund_product_id,
				'fund_product_type_id' =>  $fund_distributor_product->fund_product_type_id,
				'fund_distributor_id' =>  $fund_distributor_product->fund_distributor_id,
				'fund_distributor_product_id' =>  $fund_distributor_product->id,
				'supervising_bank_id' =>  $params['supervising_bank_id'],
				'account_system_id' =>  $account_system_id,
				'account_holder' =>  $params['account_holder'],
				'account_number' =>  $params['account_number'],
				'branch' =>  $params['branch'],
				'status' =>  $params['status'],
				'created_by' => $params['created_by'],
			]);
			if ($model) {				
				// set response
				$response['account_system_id'] = $account_system_id;
				$response['fund_distributor_bank_account_id'] = $model->id;
			} else {
				$this->error('Có lỗi khi thêm tài khoản ngân hàng đại lý phân phối');
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
	 * @@param array params[
	 * fund_distributor_bank_account_id,
	 * updated_by
	 * ]
	 * @param allow_commit
	 */
	public function lock(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
            $model = FundDistributorBankAccount::find($params['fund_distributor_bank_account_id']);
			if ($model) {
				if ($model->status == FundDistributorBankAccount::STATUS_ACTIVE) {
					$model->status = FundDistributorBankAccount::STATUS_LOCK;
					$model->updated_by = $params['updated_by'];
					if (!$model->save()) {
						$this->error('Có lỗi khi khóa tài khoản ngân hàng');
					}
				} else {
					$this->error('Tài khoản ngân hàng không hợp lệ');
				}				
			} else {
				$this->error('Tài khoản ngân hàng không tồn tại');
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
	 * fund_distributor_bank_account_id,
	 * supervising_bank_id,
	 * account_holder,
	 * account_number,
	 * branch,
	 * updated_by
	 * ]
	 * @param boolean allow_commit
	 */
	public function update(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$model = FundDistributorBankAccount::find($params['fund_distributor_bank_account_id']);
			if ($model) {
				if (!$model->update([
					'supervising_bank_id' => $params['supervising_bank_id'],
					'account_holder' => $params['account_holder'],
					'account_number' => $params['account_number'],
					'branch' => $params['branch'],
					'updated_by' => $params['updated_by'],
				])) {
					$this->error('Có lỗi khi cập nhật tài khoản ngân hàng đại lý phân phối');
				}
			} else {
				$this->error('Tài khoản ngân hàng không tồn tại');
			}
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
	}
}
