<?php

namespace App\Http\Livewire;

use App\Events\LocalTransferCreated;
use App\Models\Transfer;
use App\Models\TransferFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class Transfers extends Component
{

    use WithFileUploads;
    public $pendingFiles = [];

    public function getListeners()
    {
        $userId = auth()->id();

        return [
            "echo-private:notifications.{$userId},FileTransferredToCloud" => '$refresh',
            "echo-private:notifications.{$userId},TransferCompleted" => 'fireConfetti',
            "fireConfetti" => 'fireConfetti'
        ];
    }

    public function initiateUpload()
    {
        $this->validate([
            'pendingFiles' => ['required'],
            'pendingFiles.*' => ['file', 'mimes:jpg,gif,png,mp4,mov,avi,mp3,wav', 'max:1024000']
        ]);

        $transfer = auth()->user()->transfers()->create();
        $transfer->files()->saveMany(
            collect($this->pendingFiles)
            ->map(function (TemporaryUploadedFile $pendingFile) {
                return new TransferFile([
                    'disk' => $pendingFile->disk,
                    'path' => $pendingFile->getRealPath(),
                    'size' => $pendingFile->getSize(),
                ]);
            })
        );

        $this->pendingFiles = [];

        LocalTransferCreated::dispatch($transfer);
    }

    public function deleteBatch($batch_id)
    {
        $transfer = Transfer::where('batch_id', $batch_id)->first();
        if ($transfer->files->count() > 0) {
            foreach ($transfer->files as $file) {
                Storage::disk($file->disk)->delete($file->path);
                $file->delete();
            }
        }
        $transfer->delete();

        $this->emit('fireConfetti');
    }

    public function fireConfetti()
    {
        $this->emit('confetti');
    }

    public function render()
    {
        return view('livewire.transfers', [
            'transfers' => auth()->user()->transfers()->with('jobBatch')->withSum('files', 'size')->get(),
        ]);
    }
}
