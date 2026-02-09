<?php

declare(strict_types=1);

namespace App\Models;

use App\TicketPriority;
use App\TicketStatus;
use Database\Factories\TicketFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    /** @use HasFactory<TicketFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'created_by',
        'assigned_to',
        'subject',
        'description',
        'priority',
        'sla_due_at',
        'first_response_at',
        'sla_breached_at',
    ];

    protected $casts = [
        'sla_due_at' => 'datetime',
        'first_response_at' => 'datetime',
        'sla_breached_at' => 'datetime',
        'status' => TicketStatus::class,
        'priority' => TicketPriority::class,
    ];

    protected $attributes = [
        'status' => TicketStatus::OPEN->value,
    ];
}
