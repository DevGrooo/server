<?php

namespace App\Services\Transactions;

use App\Models\AccountSystem;
use App\Models\FundDistributorProduct;
use App\Models\FundProduct;
use App\Models\Investor;
use App\Models\InvestorFundProduct;

/**
 * @author MSI
 * @version 1.0
 * @created 08-Jul-2020 11:48:48 AM
 */
class InvestorFundProductTransaction extends Transaction
{

	/**
	 *
	 * @param array $params[
	 * investor_id,
	 * fund_product_id,
	 * created_by
	 * ]
	 * @param allow_commit
	 */
	public function create(array $params, $allow_commit = false)
	{
		$this->beginTransaction($allow_commit);
        try {
			// check investor
			$investor = Investor::find($params['investor_id']);
            $fund_distributor_id = $investor->fund_distributor_id;
			$fund_distributor_product = FundDistributorProduct::where('fund_distributor_id', $fund_distributor_id)->get();
			if (!$investor) {
				$this->error('Nhà đầu tư không tồn tại');
			} elseif ($investor->isLock()) {
				$this->error('Tài khoản nhà đầu tư đang bị đóng hoặc đã hủy');
			}
			// check fund_product
			$fund_product = FundProduct::where('id', $params['fund_product_id'])
				->where('fund_company_id', $investor->fund_company_id)->first();
			if (!$fund_product) {
				$this->error('Sản phẩm quỹ không tồn tại');
			} elseif ($fund_product->status == FundProduct::STATUS_LOCK) {
				$this->error('Sản phẩm quỹ đang bị khóa');
			}
			$model = InvestorFundProduct::create([
				'investor_id' => $investor->id,
				'fund_distributor_id' => $investor->fund_distributor_id,
				'fund_company_id' => $investor->fund_company_id,
				'fund_certificate_id' => $fund_product->fund_certificate_id,
				'fund_product_id' => $fund_product->id,
				'balance' => 0,
				'balance_available' => 0,
				'balance_freezing' => 0,
				'currency' => $fund_product->code,
				'status' => InvestorFundProduct::STATUS_ACTIVE,
				'created_by' => $params['created_by'],
			]);
			if ($model) {
				$response['investor_fund_product_id'] = $model->id;
				$account_system = AccountSystem::where('fund_product_type_id', $fund_product->fund_product_type_id)
					->where('ref_id', $investor->id)
					->where('ref_type', AccountSystem::REF_TYPE_INVESTOR)
					->first();
				if ($account_system) {
					$response['account_system_id'] = $account_system->id;
				} else {
					// create account system
					$result = (new AccountSystemTransaction())->create([
						'ref_type' => AccountSystem::REF_TYPE_INVESTOR , 
						'ref_id' => $investor->id,
						'fund_product_type_id' => $fund_product->fund_product_type_id,
						'status' => AccountSystem::STATUS_ACTIVE, 
						'created_by' => $params['created_by']
					]);
					$response['account_system_id'] = $result['account_system_id'];
				}
			} else {
				$this->error('Có lỗi khi thêm sản phẩm cho nhà đầu tư');
			}
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit);
	}

	/**
	 *
	 * @param array $params[
	 * investor_fund_product_id,
	 * updated_by
	 * ]
	 * @param allow_commit
	 */
	public function active(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
            $model = InvestorFundProduct::find($params['investor_fund_product_id']);
			if ($model) {
				if ($model->status == InvestorFundProduct::STATUS_LOCK) {
					$model->status = InvestorFundProduct::STATUS_ACTIVE;
					if (!$model->save()) {
						$this->error('Có lỗi khi kích hoạt sản phẩm nhà đầu tư');
					}
				} else {
					$this->error('Sản phẩm nhà đầu tư không hợp lệ');
				}
			} else {
				$this->error('Sản phẩm nhà đầu tư không tồn tại');
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
	 * @param array $params[
	 * investor_fund_product_id,
	 * amount,
	 * updated_by
	 * ]
	 * @param allow_commit
	 */
	public function decreaseBalance(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
            $query = "UPDATE investor_fund_products
				SET balance = balance - :amount,
					balance_available = balance_available - :amount,
					updated_by = :updated_by,
					updated_at = :updated_at
				WHERE id = :id
				AND status = :status
				AND balance >= :amount
				AND balance_available >= :amount";
			$update = InvestorFundProduct::updateRawQuery($query, [
				'id' => $params['investor_fund_product_id'],
				'amount' => $params['amount'],
				'status' => InvestorFundProduct::STATUS_ACTIVE,
				'updated_by' => $params['updated_by'],
				'updated_at' => date('Y-m-d H:i:s')
			]);
			if (!$update) {
				$this->error('Có lỗi khi cập nhật số dư sản phẩm nhà đầu tư');
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
	 * @param array $params[
	 * investor_fund_product_id,
	 * amount,
	 * updated_by
	 * ]
	 * @param allow_commit
	 */
	public function decreaseBalanceFreezing(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
            $query = "UPDATE investor_fund_products
				SET balance_freezing = balance_freezing - :amount,
					balance_available = balance_available + :amount,
					updated_by = :updated_by,
					updated_at = :updated_at
				WHERE id = :id
				AND status = :status
				AND balance_freezing >= :amount";
			$update = InvestorFundProduct::updateRawQuery($query, [
				'id' => $params['investor_fund_product_id'],
				'amount' => $params['amount'],
				'status' => InvestorFundProduct::STATUS_ACTIVE,
				'updated_by' => $params['updated_by'],
				'updated_at' => date('Y-m-d H:i:s')
			]);
			if (!$update) {
				$this->error('Có lỗi khi cập nhật số dư sản phẩm nhà đầu tư');
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
	 * @param array $params[
	 * investor_fund_product_id,
	 * amount,
	 * updated_by
	 * ]
	 * @param allow_commit
	 */
	public function increaseBalance(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
            $query = "UPDATE investor_fund_products
				SET balance = balance + :amount,
					balance_available = balance_available + :amount,
					updated_by = :updated_by,
					updated_at = :updated_at
				WHERE id = :id
				AND status = :status";
			$update = InvestorFundProduct::updateRawQuery($query, [
				'id' => $params['investor_fund_product_id'],
				'amount' => $params['amount'],
				'status' => InvestorFundProduct::STATUS_ACTIVE,
				'updated_by' => $params['updated_by'],
				'updated_at' => date('Y-m-d H:i:s')
			]);
			if (!$update) {
				$this->error('Có lỗi khi cập nhật số dư sản phẩm nhà đầu tư');
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
	 * @param array $params[
	 * investor_fund_product_id,
	 * amount,
	 * updated_by
	 * ]
	 * @param allow_commit
	 */
	public function increaseBalanceFreezing(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
            $query = "UPDATE investor_fund_products
				SET balance_freezing = balance_freezing + :amount,
					balance_available = balance_available - :amount,
					updated_by = :updated_by,
					updated_at = :updated_at
				WHERE id = :id
				AND status = :status
				AND balance_available >= :amount";
			$update = InvestorFundProduct::updateRawQuery($query, [
				'id' => $params['investor_fund_product_id'],
				'amount' => $params['amount'],
				'status' => InvestorFundProduct::STATUS_ACTIVE,
				'updated_by' => $params['updated_by'],
				'updated_at' => date('Y-m-d H:i:s')
			]);
			if (!$update) {
				$this->error('Có lỗi khi cập nhật số dư sản phẩm nhà đầu tư');
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
	 * @param array $params[
	 * investor_fund_product_id,
	 * updated_by
	 * ]
	 * @param allow_commit
	 */
	public function lock(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
            $model = InvestorFundProduct::find($params['investor_fund_product_id']);
			if ($model) {
				if ($model->status == InvestorFundProduct::STATUS_ACTIVE) {
					$model->status = InvestorFundProduct::STATUS_LOCK;
					if (!$model->save()) {
						$this->error('Có lỗi khi khóa sản phẩm nhà đầu tư');
					}
				} else {
					$this->error('sản phẩm nhà đầu tư không hợp lệ');
				}
			} else {
				$this->error('Sản phẩm nhà đầu tư không tồn tại');
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
     * @param array $params
     * @param $allow_commit
     */
     public function getFundProduct($params, $allow_commit = false)
     {
         $response = null;
         $this->beginTransaction($allow_commit);
         try {
             $investor = Investor::find($params['investor_id']);
             $fund_distributor_products = FundDistributorProduct::where('fund_distributor_id', $investor->fund_distributor_id)
                 ->pluck('fund_product_id')
                 ->toArray();
             $investor_fund_products = InvestorFundProduct::where('fund_distributor_id', $investor->fund_distributor_id)
                 ->where('investor_id', $investor->id)
                 ->pluck('fund_product_id')
                 ->toArray();
             $fund_product_ids = array_diff($fund_distributor_products, $investor_fund_products);

             $response = FundProduct::whereIn('id', $fund_product_ids)->get(['id as value', 'name as title']);
         }catch (\Exception $e) {
             // rollback transaction
             $this->rollback($allow_commit, $e);
         }
         // commit and response
         return $this->response($allow_commit, $response);
     }
}
