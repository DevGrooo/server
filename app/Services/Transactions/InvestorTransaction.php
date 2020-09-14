<?php

namespace App\Services\Transactions;

use App\Events\ActiveInvestorEvent;
use App\Models\FileImport;
use App\Models\FileImportLine;
use App\Models\FundDistributor;
use App\Models\Investor;
use App\Models\InvestorBankAccount;

/**
 * @author MSI
 * @version 1.0
 * @created 08-Jul-2020 11:48:51 AM
 */
class InvestorTransaction extends Transaction
{

	/**
	 * Add Investor
	 * 
	 * @param array params [
	 * fund_distributor_code, 
	 * fund_distributor_staff_id, 
	 * referral_bank_id, 
	 * trading_account_number, 
	 * trading_account_type,
	 * code,
	 * zone_type,
	 * scale_type,
	 * invest_type,
	 * name,
	 * gender,
	 * country_id,
	 * birthday,
	 * id_type_id,
	 * id_number,
	 * id_issuing_date,
	 * id_issuing_place,
	 * id_expiration_date,
	 * phone,
	 * fax,
	 * email,
	 * tax_id,
	 * tax_country_id,
	 * permanent_address,
	 * permanent_country_id,
	 * current_address,
	 * current_country_id,
	 * visa_number,
	 * visa_issuing_date,
	 * visa_issuing_place,
	 * temporary_address,
	 * re_fullname,
	 * re_position,
	 * re_country_id,
	 * re_id_number,
	 * re_id_type,
	 * re_id_issuing_date,
	 * re_id_issuing_place,
	 * re_id_expiration_date,
	 * re_phone,
	 * re_address,
	 * re_country_id,
	 * au_fullname,
	 * au_country_id,
	 * au_id_number,
	 * au_id_type,
	 * au_id_issuing_date,
	 * au_id_issuing_place,
	 * au_id_expiration_date,
	 * au_phone,
	 * au_address,
	 * au_country_id,
	 * au_start_date,
	 * au_end_date,
	 * fatca_link_auth,
	 * fatca_recode,
	 * fatca_funds,
	 * fatca1,
	 * fatca2,
	 * fatca3,
	 * fatca4,
	 * fatca5,
	 * fatca6,
	 * fatca7,
	 * created_by,
	 * ]
	 * @param allow_commit
	 * @return [investor_id]
	 */
	public function create(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$fund_distributor = FundDistributor::where('code', $params['fund_distributor_code'])->where('status', FundDistributor::STATUS_ACTIVE)->first();
			if ($fund_distributor) {
				if (Investor::isTradingAccountNumber($params['trading_account_number'], $fund_distributor->code)) {
					$params['fund_company_id'] = $fund_distributor->fund_company_id;
					$params['fund_distributor_id'] = $fund_distributor->id;
					$params['status'] = Investor::STATUS_NEW;
					$params['vsd_status'] = Investor::VSD_STATUS_NEW;
					// var_dump($params);die();
					$model = Investor::create($params);					
					if ($model) {
						// set response
						$response['investor_id'] = $model->id;
					} else {
						$this->error('Có lỗi khi thêm nhà đầu tư');
					}
				} else {
					$this->error('Số TKDG không hợp lệ');
				}
			} else {
				$this->error('Đại lý phân phối không tồn tại');
			}			
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        //commit and response
        return $this->response($allow_commit, $response);
	}

	/**
	 * Update Investor
	 * 
	 * @param array params [	 
	 * fund_distributor_staff_id, 
	 * referral_bank_id, 
	 * trading_account_number, 
	 * trading_account_type,
	 * code,
	 * zone_type,
	 * scale_type,
	 * invest_type,
	 * name,
	 * gender,
	 * country_id,
	 * birthday,
	 * id_type,
	 * id_number,
	 * id_issuing_date,
	 * id_issuing_place,
	 * id_expiration_date,
	 * phone,
	 * fax,
	 * email,
	 * tax_id,
	 * tax_country_id,
	 * permanent_address,
	 * permanent_country_id,
	 * current_address,
	 * current_country_id,
	 * visa_number,
	 * visa_issuing_date,
	 * visa_issuing_place,
	 * temporary_address,
	 * re_fullname,
	 * re_position,
	 * re_country_id,
	 * re_id_number,
	 * re_id_type,
	 * re_id_issuing_date,
	 * re_id_issuing_place,
	 * re_id_expiration_date,
	 * re_phone,
	 * re_address,
	 * re_country_id,
	 * au_fullname,
	 * au_country_id,
	 * au_id_number,
	 * au_id_type,
	 * au_id_issuing_date,
	 * au_id_issuing_place,
	 * au_id_expiration_date,
	 * au_phone,
	 * au_address,
	 * au_country_id,
	 * au_start_date,
	 * au_end_date,
	 * fatca_link_auth,
	 * fatca_recode,
	 * fatca_funds,
	 * fatca1,
	 * fatca2,
	 * fatca3,
	 * fatca4,
	 * fatca5,
	 * fatca6,
	 * fatca7,
	 * status,
	 * created_by,
	 * ]
	 * @param allow_commit
	 * @return array
	 */
	public function update(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$model = Investor::find($params['investor_id']);
			if ($model) {
				$fund_distributor = FundDistributor::find($model->fund_distributor_id);
				if (!$fund_distributor) {
					$this->error('Đại lý phân phối không tồn tại');
				}
				if (Investor::isTradingAccountNumber($params['trading_account_number'], $fund_distributor->code)) {
					unset($params['investor_id']);
					$model->loadParams($params);
					if (!$model->save()) {
						$this->error('Có lỗi khi cập nhật nhà đầu tư');
					}
				} else {
					$this->error('Số TKDG không hợp lệ');
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
	 * Add Investor and InvestorBankAccount
	 * 
	 * @return [investor_id, investor_bank_account_id]
	 */
	public function createAndAddBankAccount(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$inputs = $params;
			unset($inputs['bank_id']);
			unset($inputs['account_holder']);
			unset($inputs['account_number']);
			unset($inputs['branch']);
			unset($inputs['description']);
			unset($inputs['is_default']);
			// create investor
			$response = $this->create($inputs);
			// create bank account for investor
			$result = (new InvestorBankAccountTransaction())->create([
				'investor_id' => $response['investor_id'], 
				'bank_id' => $params['bank_id'], 
				'account_holder' => $params['account_holder'], 
				'account_number' => $params['account_number'], 
				'branch' => $params['branch'], 
				'description' => $params['description'], 
				'is_default' => $params['is_default'], 
				'status' => InvestorBankAccount::STATUS_ACTIVE, 
				'created_by' => $params['created_by']
			]);
			$response['investor_bank_account_id'] = $result['investor_bank_account_id'];
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
	 * investor_id,
	 * updated_by
	 * ]
	 * @param $allow_commit
	 */
	public function cancel(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
            $model = Investor::find($params['investor_id']);
			if ($model) {
				if ($model->status == Investor::STATUS_NEW) {
					$model->status = Investor::STATUS_CANCEL;
					$model->updated_by = $params['updated_by'];
					if (!$model->save()) {
						$this->error('Có lỗi khi hủy tài khoản nhà đầu tư');
					}
				} else {
					$this->error('Nhà đầu tư không hợp lệ');
				}				
			} else {
				$this->error('Nhà đầu tư không tồn tại');
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
	 * investor_id,
	 * updated_by
	 * ]
	 * @param $allow_commit
	 */
	public function closed(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
            $model = Investor::find($params['investor_id']);
			if ($model) {
				if ($model->status == Investor::STATUS_IMPORT_VSD) {
					$model->status = Investor::STATUS_CLOSED;
					$model->updated_by = $params['updated_by'];
					if (!$model->save()) {
						$this->error('Có lỗi khi đóng tài khoản nhà đầu tư');
					}
				} else {
					$this->error('Nhà đầu tư không hợp lệ');
				}				
			} else {
				$this->error('Nhà đầu tư không tồn tại');
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
	 * investor_id,
	 * updated_by
	 * ]
	 * @param $allow_commit
	 */
	public function reopen(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
            $model = Investor::find($params['investor_id']);
			if ($model) {
				if ($model->status == Investor::STATUS_CLOSED) {
					$model->status = Investor::STATUS_IMPORT_VSD;
					$model->updated_by = $params['updated_by'];
					if (!$model->save()) {
						$this->error('Có lỗi khi mở lại tài khoản nhà đầu tư');
					}
				} else {
					$this->error('Nhà đầu tư không hợp lệ');
				}				
			} else {
				$this->error('Nhà đầu tư không tồn tại');
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
	 * investor_id,
	 * updated_by
	 * ]
	 * @param $allow_commit
	 */
	public function updateStatusImportVSD(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
            $model = Investor::find($params['investor_id']);
			if ($model) {
				if ($model->status == Investor::STATUS_NEW) {
					$model->status = Investor::STATUS_IMPORT_VSD;
					$model->updated_by = $params['updated_by'];
					if (!$model->save()) {
						$this->error('Có lỗi khi cập nhật trạng thái tài khoản nhà đầu tư');
					}
				} else {
					$this->error('Nhà đầu tư không hợp lệ');
				}				
			} else {
				$this->error('Nhà đầu tư không tồn tại');
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
	 * investor_id,
	 * updated_by
	 * ]
	 * @param $allow_commit
	 */
	public function active(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
            $model = Investor::find($params['investor_id']);
			if ($model) {
				if ($model->vsd_status == Investor::VSD_STATUS_NEW || $model->vsd_status == Investor::VSD_STATUS_REJECT) {
					$model->vsd_status = Investor::VSD_STATUS_ACTIVE;
					$model->updated_by = $params['updated_by'];
					if ($model->save()) {
						event(new ActiveInvestorEvent($model));
					} else {
						$this->error('Có lỗi khi cập nhật trạng thái tài khoản nhà đầu tư');
					}
				} else {
					$this->error('Nhà đầu tư không hợp lệ');
				}				
			} else {
				$this->error('Nhà đầu tư không tồn tại');
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
	 * investor_id,
	 * updated_by
	 * ]
	 * @param $allow_commit
	 */
	public function reject(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
            $model = Investor::find($params['investor_id']);
			if ($model) {
				if ($model->vsd_status == Investor::VSD_STATUS_NEW) {
					$model->vsd_status = Investor::VSD_STATUS_REJECT;
					$model->updated_by = $params['updated_by'];
					if (!$model->save()) {
						$this->error('Có lỗi khi cập nhật trạng thái tài khoản nhà đầu tư');
					}
				} else {
					$this->error('Nhà đầu tư không hợp lệ');
				}				
			} else {
				$this->error('Nhà đầu tư không tồn tại');
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
	 * investor_id,
	 * updated_by
	 * ]
	 * @param $allow_commit
	 */
	public function updateStatusSendMail(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
            $model = Investor::find($params['investor_id']);
			if ($model) {
				if ($model->vsd_status == Investor::VSD_STATUS_NEW || $model->vsd_status == Investor::VSD_STATUS_ACTIVE) {
					$model->vsd_status = Investor::VSD_STATUS_SEND_MAIL;
					$model->updated_by = $params['updated_by'];
					if (!$model->save()) {
						$this->error('Có lỗi khi cập nhật trạng thái tài khoản nhà đầu tư');
					}
				} else {
					$this->error('Nhà đầu tư không hợp lệ');
				}				
			} else {
				$this->error('Nhà đầu tư không tồn tại');
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
	 * file_import_id,
	 * created_by
	 * ]
	 * @param $allow_commit
	 */
	public function import(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$file_import = FileImport::where('id', $params['file_import_id'])
				->where('type', FileImport::TYPE_INVESTOR)
				->where('status', FileImport::STATUS_PROCESSED)
				->where('total_error', 0)
				->where('total_warning', 0)
				->first();
			if (!$file_import) {
				$this->error('File import không hợp lệ');
			}
			$file_import_lines = FileImportLine::where('file_import_id', $params['file_import_id'])->get();
			if (!$file_import_lines) {
				$this->error('Không có dữ liệu để import');
			}
			foreach ($file_import_lines as $file_import_line) {
				$this->_importByFileImportLine($file_import_line, $params['created_by']);
			}
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
	}

	private function _importByFileImportLine($file_import_line, $created_by)
	{
		if ($file_import_line->data_result['investor_id'] == 0) {
			$inputs = $file_import_line->data_result;
			$inputs['is_default'] = InvestorBankAccount::DEFAULT;
			$inputs['created_by'] = $created_by;
			$this->createAndAddBankAccount($inputs);
		}
	}
}
