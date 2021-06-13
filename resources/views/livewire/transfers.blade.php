<div class="row">
    <div class="col-9">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                <tr>
                    <th>&nbsp;</th>
                    <th>Status</th>
                    <th>Batch ID</th>
                    <th>Storage</th>
                    <th>Delete</th>
                </tr>
                </thead>
                <tbody>
                @forelse($transfers as $transfer)
                    <tr>
                        @if (is_null($transfer->jobBatch))
                            <td>%</td>
                            <td>
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </td>
                        @elseif ($transfer->jobBatch->hasPendingJobs())
                            <td>%</td>
                            <td>
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" style="width: {{ $transfer->jobBatch->progress() / 100 }}%" aria-valuenow="{{ $transfer->jobBatch->progress() / 100 }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </td>
                        @elseif ($transfer->jobBatch->finished() && $transfer->jobBatch->failed())
                            <td>X</td>
                            <td>
                                Failed
                            </td>
                        @elseif ($transfer->jobBatch->finished() && $transfer->jobBatch->hasFailures())
                            <td>!!</td>
                            <td>
                                Finished with errors
                            </td>
                        @elseif ($transfer->jobBatch->finished())
                            <td>V</td>
                            <td>
                                Uploaded
                            </td>
                        @else
                            <td> - </td>
                            <td> - </td>
                        @endif
                        <td>
                            <strong>{{ $transfer->batch_id }}</strong>
                            <p>
                                @forelse($transfer->files as $file)
                                    - <a href="{{ route('show', ['transfer_id' => $transfer->id, 'file_id' => $file->id]) }}" target="_blank">
                                        {{ $file->path }}
                                    </a>
                                @empty
                                @endforelse
                            </p>
                        </td>
                        <td>{{ $transfer->files ? round($transfer->files->sum('size') / (1024 * 1024), 2) : 0 }} MB</td>
                        <td>
                            <button type="button" wire:click="deleteBatch('{{ $transfer->batch_id }}')" class="btn btn-danger btn-sm">
                                Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">You have no transfers.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-3">
        <div class="bg-white shadow w-100 p-4">
            <h3>Create Batch</h3>
            <p>Select the files you want to upload</p>

            <div class="border">
                <input type="file" wire:model="pendingFiles" id="files" multiple>
            </div>

            @error('pendingFiles.*')
                <div class="alert alert-danger">
                    {{ $message }}
                </div>
            @enderror

            <div class="mt-4">
                <button wire:click="initiateUpload" type="button" class="btn btn-primary btn-block">
                    Start Upload
                </button>
            </div>

        </div>
    </div>
</div>
