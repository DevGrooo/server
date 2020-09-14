<?php
namespace App\Services\Transactions;

use App\Exceptions\ApiException;
use App\Models\Investor;
use App\Models\InvestorBankAccount;
/**
 * @author MSI
 * @version 1.0
 * @created 08-Jul-2020 1:39:09 PM
 */
class InvestorBankAccountTransaction extends Transaction
{
	/**
	 * 
	 * @param array params [
	 * investor_id,
	 * bank_id,
	 * account_holder,
	 * account_number,
	 * branch,
	 * description,
	 * is_default,
	 * status,
	 * created_by
	 * ]
	 * @param boolean allow_commit
	 * @throws ApiException
	 * @return integer investor_bank_account_id
	 */
	public function create(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$investor = Investor::find($params['investor_id']);
			if ($investor) {				
				$bank_account = InvestorBankAccount::create([
					'fund_company_id' => $investor->fund_company_id,
					'fund_distributor_id' => $investor->fund_distributor_id,
					'investor_id' => $params['investor_id'], 
					'bank_id' => $params['bank_id'], 
					'account_holder' => $params['account_holder'], 
					'account_number' => $params['account_number'], 
					'branch' => $params['branch'], 
					'description' => $params['description'], 
					'is_default' => InvestorBankAccount::NOT_DEFAULT, 
					'status' => $params['status'], 
					'created_by' => $params['created_by'],
					'updated_by' => $params['created_by'],
				]);
				if ($bank_account) {
					if ($params['is_default'] == InvestorBankAccount::DEFAULT) {
						$response['investor_bank_account_id'] = $bank_account->id;
						$inputs = array(
							'investor_bank_account_id' => $bank_account->id,
							'updated_by' => $params['created_by'],
						);
						$this->setDefault($inputs);
					}
				} else {
					$this->error('Có lỗi khi thêm tài khoản ngân hàng');
				}				
			} else {
				$this->error('Nhà đầu tư không tồn tại');
			}						
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        //commit and response
        return $this->response($allow_commit, $response);
	}

	/**
	 * @param array params [
	 * investor_bank_account_id,
	 * updated_by
	 * ]
	 * @param boolean allow_commit
	 * @return array
	 */
	public function setDefault(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$bank_account = InvestorBankAccount::find($params['investor_bank_account_id']);
			if ($bank_account) {
				// get other default
				$other_bank_account = InvestorBankAccount::where('investor_id', $bank_account->investor_id)
					->where('is_default', InvestorBankAccount::DEFAULT); 
				if ($other_bank_account->count()) {
					// update if other default exists
					if (!$other_bank_account->update([
						'is_default' => InvestorBankAccount::NOT_DEFAULT,
						'updated_by' => $params['updated_by'],
					])) {
						$this->error('Có lỗi khi cập nhật tài khoản mặc định');
					}
				}
				// update current bank account is default
				$bank_account->is_default = InvestorBankAccount::DEFAULT;
				$bank_account->updated_by = $params['updated_by'];
				if (!$bank_account->save()) {
					$this->error('Có lỗi khi cập nhật tài khoản ngân hàng');
				}
			} else {
				$this->error('Tài khoản ngân hàng không tồn tại');
			}			
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        //commit and response
        return $this->response($allow_commit, $response);
	}

	/**
	 * 
	 * @param $params [
	 * investor_bank_account_id,
	 * updated_by
	 * ]
	 * @param $allow_commit
	 */
	public function active(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
            $model = InvestorBankAccount::find($params['investor_bank_account_id']);
			if ($model) {
				if ($model->status == InvestorBankAccount::STATUS_LOCK) {
					$model->status = InvestorBankAccount::STATUS_ACTIVE;
					$model->updated_by = $params['updated_by'];
					if (!$model->save()) {
						$this->error('Có lỗi khi kích hoạt tài khoản ngân hàng nhà đầu tư');
					}
				} else {
					$this->error('Tài khoản ngân hàng nhà đầu tư không hợp lệ');
				}				
			} else {
				$this->error('Tài khoản ngân hàng nhà đầu tư không tồn tại');
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
	 * investor_bank_account_id,
	 * updated_by
	 * ]
	 * @param $allow_commit
	 */
	public function lock(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
            $model = InvestorBankAccount::find($params['investor_bank_account_id']);
			if ($model) {
				if ($model->status == InvestorBankAccount::STATUS_ACTIVE) {
					$model->status = InvestorBankAccount::STATUS_LOCK;
					$model->updated_by = $params['updated_by'];
					if (!$model->save()) {
						$this->error('Có lỗi khi kích hoạt tài khoản ngân hàng nhà đầu tư');
					}
				} else {
					$this->error('Tài khoản ngân hàng nhà đầu tư không hợp lệ');
				}				
			} else {
				$this->error('Tài khoản ngân hàng nhà đầu tư không tồn tại');
			}
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
	}
}
