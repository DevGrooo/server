<?php

namespace App\Jobs;

use App\Models\FileImport;
use App\Models\FileImportLine;

class ProcessFileImportJob extends Job
{
    protected $file_import;    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(FileImport $file_import)
    {
        $this->file_import = $file_import;        
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->file_import->status == FileImport::STATUS_NEW) {
            $this->file_import->status = FileImport::STATUS_PROCESSING;
            if ($this->file_import->save()) {
                $lines = FileImportLine::where('file_import_id', $this->file_import->id)->where('status', FileImportLine::STATUS_NEW)->get();
                if ($lines) {
                    foreach ($lines as $line) {
                        $job = (new ProcessFileImportLineJob($line))->onConnection(config('queue.default'))->onQueue('imports');
                        dispatch($job);
                    }
                }
            }
        }
    }
}
