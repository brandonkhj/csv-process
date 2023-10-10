<?php

namespace App\Jobs;

use App\Imports\PrintsImport;
use App\Models\Upload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class UpsertProductJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $file;
    /**
     * Create a new job instance.
     */
    public function __construct(Upload $upload)
    {
        $this->file = $upload;
    }

    public function failed(\Exception $exception)
    {
        $this->file->update([
            'status' => Upload::STATUS_FAILED
        ]);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->file->update([
            'status' => Upload::STATUS_PROCESSING
        ]);

        Excel::import(new PrintsImport($this->file), $this->file->path, 'public', \Maatwebsite\Excel\Excel::CSV);
    }
}
