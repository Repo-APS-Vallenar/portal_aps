<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketDocument extends Model
{
    protected $fillable = [
        'ticket_id',
        'user_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'description'
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
} 