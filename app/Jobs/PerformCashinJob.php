<?php

namespace App\Jobs;

use App\Models\Cashin;
use App\Services\Transactions\CashinTransaction;
use Illuminate\Support\Carbon;

class PerformCashinJob extends Job
{
    protected $cashin;    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Cashin $cashin)
    {
        $this->cashin = $cashin;        
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        (new CashinTransaction())->perform([
            'cashin_id' => $this->cashin->id, 
            'perform_at' => Carbon::now()->toDateTimeString(), 
            'updated_by' => 0,
        ], true);
    }
}
