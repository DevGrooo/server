<?php

namespace App\Jobs;

use App\Models\StatementLine;
use App\Services\Banks\StandardCharteredBank;
use App\Services\Transactions\StatementLineTransaction;
use Exception;

class ProcessStatementLineJob extends Job
{
    protected $statement_line;    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(StatementLine $statement_line)
    {
        $this->statement_line = $statement_line;        
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $transaction = new StatementLineTransaction();
        $transaction->updateProcessing([
            'statement_line_id' => $this->statement_line->id, 
            'updated_by' => 0,
        ], true);
        $obj = new StandardCharteredBank($this->statement_line);
        $check = $obj->checkData();
        $data_result = $obj->getDataResult();
        $warnings = $obj->getWarnings();
        $errors = $obj->getErrors();
        if ($check) {
            $transaction->updateSuccess([
                'statement_line_id' => $this->statement_line->id,
                'data_result' => $data_result,
                'warnings' => $warnings,
                'updated_by' => 0,
            ], true);
        } else {
            $transaction->updateError([
                'statement_line_id' => $this->statement_line->id,
                'data_result' => $data_result,
                'warnings' => $warnings,
                'errors' => $errors,
                'updated_by' => 0,
            ], true);
        }     
    }
}
