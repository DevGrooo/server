<?php

namespace App\Jobs;

use App\Models\FileImportLine;
use App\Services\Transactions\FileImportLineTransaction;
use Exception;

class ProcessFileImportLineJob extends Job
{
    protected $line;
    protected $import;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(FileImportLine $line)
    {
        $this->line = $line;
        $this->import = $this->line->file_import->getClassImport();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $transaction = new FileImportLineTransaction();
        $transaction->updateProcessing([
            'file_import_line_id' => $this->line->id,             
        ], true);        
        $check = $this->import->checkFileImportLine($this->line);
        $data_result = $this->import->getDataResult();
        $warnings = $this->import->getWarnings();
        $errors = $this->import->getErrors();
        if ($check) {
            $transaction->updateSuccess([
                'file_import_line_id' => $this->line->id,
                'data_result' => $data_result,
                'warnings' => $warnings,
                'updated_by' => 0,                
            ], true);
        } else {
            $transaction->updateError([
                'file_import_line_id' => $this->line->id,
                'data_result' => $data_result,
                'warnings' => $warnings,
                'errors' => $errors,
                'updated_by' => 0,
            ], true);
        }     
    }
}
