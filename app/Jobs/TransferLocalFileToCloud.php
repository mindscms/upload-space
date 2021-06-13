<?php

namespace App\Jobs;

use App\Events\FileTransferredToCloud;
use App\Models\TransferFile;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\File;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class TransferLocalFileToCloud implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $file;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(TransferFile $file)
    {
        $this->file = $file;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $cloudPath = Storage::disk('s3')->put('images', new File($localPath = $this->file->path));

        $this->file->update([
            'disk' => 's3',
            'path' => $cloudPath
        ]);

        Storage::delete(explode('livewire-tmp/', $localPath)[1]);

        FileTransferredToCloud::dispatch($this->file);
    }
}
