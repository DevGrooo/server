<?php

namespace App\Services\Transactions;

use App\Jobs\ProcessFileImportJob;
use App\Models\FileImport;
use App\Models\FileImportLine;
use Carbon\Carbon;

/**
 * @author MSI
 * @version 1.0
 * @created 19-Aug-2020 5:54:52 PM
 */
class FileImportTransaction extends Transaction
{

	/**
	 * 
	 * @param array $params[
	 * type,
	 * file_name,
	 * file_path,
	 * lines,	 
	 * created_by
	 * ]
	 * @param allow_commit
	 */
	public function create(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$model = FileImport::create([
				'type' => $params['type'],
				'file_name' => $params['file_name'],
				'file_path' => $params['file_path'],
				'total_line' => count($params['lines']),
				'processed_line' => 0,
				'total_error' => 0,
				'total_warning' => 0,
				'status' => FileImport::STATUS_NEW,
				'created_by' => $params['created_by'],
			]);
			if ($model) {
				$response['file_import_id'] = $model->id;
				$transaction = new FileImportLineTransaction();
				foreach ($params['lines'] as $line) {
					$transaction->create([
						'file_import_id' => $model->id,
						'data' => $line,
					]);
				}
				// add job process
				$job = (new ProcessFileImportJob($model))->onConnection(config('queue.default'))->onQueue('imports');
				dispatch($job);			
			} else {
				$this->error('Có lỗi khi thêm sao kê ngân hàng');
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
	 * file_import_id,
	 * number_error,
	 * number_warning,
	 * updated_by
	 * ]
	 * @param allow_commit
	 */
	public function increaseProcessedLine(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$query = "UPDATE file_imports  
						SET processed_line = (SELECT COUNT(id) FROM file_import_lines WHERE file_import_id = file_imports.id AND status IN (:line_status_error, :line_status_success)), 
						total_error = total_error + :number_error,
						total_warning = total_warning + :number_warning,
						status = IF(total_line > processed_line, :status_processing, :status_processed),
						updated_at = :now
						WHERE id = :id 
						AND status != :status_processed 
						AND total_line > processed_line ";
			$update = FileImport::updateRawQuery($query, [
				'id' => $params['file_import_id'],
				'number_error' => $params['number_error'],
				'number_warning' => $params['number_warning'],
				'status_processing' => FileImport::STATUS_PROCESSING,
				'status_processed' => FileImport::STATUS_PROCESSED,
				'line_status_error' => FileImportLine::STATUS_ERROR,
				'line_status_success' => FileImportLine::STATUS_SUCCESS,
				'now' => Carbon::now()->toDateTimeString()
			]);
			if ($update) {
				$model = FileImport::find($params['file_import_id']);
				if ($model->status == FileImport::STATUS_PROCESSED ) {
					$import = $model->getClassImport();
					$import->callTransactionAfterFileImportProcessed($model);
				}
			} else {
				$this->error('Có lỗi khi cập nhật số dòng xử lý');
			}
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        //commit and response
        return $this->response($allow_commit, $response);
	}
}
