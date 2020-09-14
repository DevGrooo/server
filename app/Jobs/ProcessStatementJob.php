<?php

namespace App\Jobs;

use App\Models\Statement;
use App\Models\StatementLine;

class ProcessStatementJob extends Job
{
    protected $statement;    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Statement $statement)
    {
        $this->statement = $statement;        
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->statement->status == Statement::STATUS_NEW) {
            $this->statement->status = Statement::STATUS_PROCESSING;
            if ($this->statement->save()) {
                $statement_lines = StatementLine::where('statement_id', $this->statement->id)->where('status', StatementLine::STATUS_NEW)->get();
                if ($statement_lines) {
                    foreach ($statement_lines as $statement_line) {
                        $job = (new ProcessStatementLineJob($statement_line))->onConnection(config('queue.default'))->onQueue('imports');
                        dispatch($job);
                    }
                }
            }
        }
    }
}
