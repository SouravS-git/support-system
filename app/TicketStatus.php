<?php

declare(strict_types=1);

namespace App;

enum TicketStatus: string
{
    case OPEN = 'open';
    case IN_PROGRESS = 'in_progress';
    case WAITING_FOR_CUSTOMER = 'waiting_for_customer';
    case RESOLVED = 'resolved';
    case CLOSED = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::OPEN => 'Open',
            self::IN_PROGRESS => 'In Progress',
            self::WAITING_FOR_CUSTOMER => 'Waiting for Customer',
            self::RESOLVED => 'Resolved',
            self::CLOSED => 'Closed',
        };
    }
}
