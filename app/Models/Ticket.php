<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Events\TicketStatusChanged;
use Database\Factories\TicketFactory;
use DomainException;
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
        'status' => TicketStatus::OPEN,
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

    public function activities(): HasMany
    {
        return $this->hasMany(TicketActivity::class)->oldest();
    }

    public function hasFirstResponse(): bool
    {
        return $this->first_response_at !== null;
    }

    // Domain modeling for business rule enforcement
    public function canTransitionTo(TicketStatus $newStatus): bool
    {
        return match ($this->status) {
            TicketStatus::OPEN, TicketStatus::WAITING_FOR_CUSTOMER => $newStatus === TicketStatus::IN_PROGRESS,

            TicketStatus::IN_PROGRESS => in_array($newStatus, [TicketStatus::WAITING_FOR_CUSTOMER, TicketStatus::RESOLVED]),

            TicketStatus::RESOLVED => $newStatus === TicketStatus::CLOSED,

            default => false,
        };
    }

    public function transitionTo(TicketStatus $newStatus): void
    {
        if (! $this->canTransitionTo($newStatus)) {
            throw new DomainException('Invalid status transition.');
        }

        $oldStatus = $this->status;
        $this->update(['status' => $newStatus]);

    }
}
