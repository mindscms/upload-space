<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferFile extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'disk' => 'string',
        'path' => 'string',
        'size' => 'integer'
    ];

    public function transfer()
    {
        return $this->belongsTo(Transfer::class);
    }

}
