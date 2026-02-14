<?php

declare(strict_types=1);

namespace App\Models;

use App\TicketPriority;
use App\TicketStatus;
use Database\Factories\TicketFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'status',
        'sla_due_at',
        'first_response_at',
        'sla_breached_at',
        'sla_notified_at',
    ];

    protected $casts = [
        'sla_due_at' => 'datetime',
        'first_response_at' => 'datetime',
        'sla_breached_at' => 'datetime',
        'status' => TicketStatus::class,
        'priority' => TicketPriority::class,
        'sla_notified_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => TicketStatus::OPEN->value,
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(TicketReply::class);
    }
}
