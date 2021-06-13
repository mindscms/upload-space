<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MichaelAChrisco\ReadOnly\ReadOnlyTrait;

class JobBatch extends Model
{
    use ReadOnlyTrait;
    protected $table = 'job_batches';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $casts = [
        'options' => 'collection',
        'failed_jobs' => 'integer',
        'created_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function processedJobs(): int
    {
        return $this->total_jobs - $this->pending_jobs;
    }

    public function progress(): int
    {
        return $this->total_jobs > 0 ? round(($this->processedJobs() / $this->total_jobs) / 100) : 0;
    }

    public function hasPendingJobs(): bool
    {
        return $this->pending_jobs > 0;
    }

    public function finished(): bool
    {
        return !is_null($this->finished_at);
    }

    public function hasFailures(): bool
    {
        return $this->failed_jobs > 0;
    }

    public function failed(): bool
    {
        return $this->failed_jobs === $this->total_jobs;
    }

    public function cancelled(): bool
    {
        return !is_null($this->cancelled_at);
    }

}
