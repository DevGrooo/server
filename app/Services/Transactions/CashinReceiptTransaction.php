<?php
namespace App\Services\Transactions;

use App\Models\CashinReceipt;

/**
 * @author MSI
 * @version 1.0
 * @created 08-Jul-2020 11:48:13 AM
 */
class CashinReceiptTransaction extends Transaction
{
	/**
	 * 
	 * @param array params[
	 * cashin_id,
	 * supervising_bank_id,
	 * receipt,
	 * ]
	 * @param boolean allow_commit
	 */
	public function create(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
            $model = CashinReceipt::create($params);
			if ($model) {
				$response['cashin_receipt_id'] = $model->id;
			} else {
				$this->error('Có lỗi khi lưu chứng từ thu');
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
	 * @param array params[
	 * cashin_id,
	 * ]
	 * @param boolean allow_commit
	 */
	public function deleteByCashin(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$model = CashinReceipt::where('cashin_id', $params['cashin_id'])->first();
			if ($model && !$model->delete()) {
				$this->error('Có lỗi khi xóa chứng từ thu');
			}
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        //commit and response
        return $this->response($allow_commit, $response);
	}

}
