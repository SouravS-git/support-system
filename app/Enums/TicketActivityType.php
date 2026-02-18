<?php

declare(strict_types=1);

namespace App\Enums;

enum TicketActivityType: string
{
    case CREATED = 'ticket_created';
    case ASSIGNED = 'ticket_assigned';
    case STATUS_CHANGED = 'status_changed';
    case SLA_BREACHED = 'sla_breached';

    public function label(): string
    {
        return match ($this) {
            self::CREATED => 'Ticket Created',
            self::ASSIGNED => 'Ticket Assigned',
            self::STATUS_CHANGED => 'Status Changed',
            self::SLA_BREACHED => 'SLA Breached',
        };
    }
}
