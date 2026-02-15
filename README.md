# Support Ticket & SLA Management System

A production-style internal support system built with Laravel 12, implementing role-based authorization, first-response SLA tracking, and queued background processing.

This project demonstrates clean domain modeling, business rule enforcement, and idempotent job design.

---

## 🚀 Tech Stack

- Laravel 12
- PHP 8.2+
- MySQL 8.x
- Blade + Tailwind (Breeze)
- Database Queue
- Scheduler (cron-style execution)
- Mailpit (local email testing)
- Pest (feature testing)
- Laravel Pint (code style)

---

## 👥 Roles

The system supports three roles:

- **Admin**
    - View all tickets
    - Receive SLA breach notifications

- **Agent**
    - View assigned tickets
    - Reply to tickets
    - Add internal notes

- **Customer**
    - Create tickets
    - View own tickets
    - Reply publicly

Authorization is enforced using Laravel Policies.

---

## 🎫 Features

### Ticket Lifecycle
- Create support tickets
- Assign tickets to agents
- Ticket status management
- Public and internal replies
- Soft deletes

### SLA Management
- First-response SLA based on priority
- SLA deadline calculated at ticket creation
- Automatic SLA breach detection
- Admin notification on breach
- Idempotent background job (no duplicate alerts)

### Background Processing
- SLA check runs via scheduled job
- Email notifications are queued
- Safe to rerun without duplicate side effects

### Testing
- Feature tests for:
    - Authorization rules
    - Ticket visibility
    - Reply permissions
    - SLA breach logic
- Business rules are protected by automated tests

---

## ⏱ SLA Rules

| Priority | First Response SLA |
|----------|-------------------|
| High     | 1 hour            |
| Medium   | 4 hours           |
| Low      | 24 hours          |

SLA is considered breached if:
- `first_response_at` is null
- `sla_due_at` has passed

Notification deduplication is handled using `sla_notified_at`.

---

## 🏗 Architecture Decisions

### 1. Business Logic in Actions
Ticket creation and SLA calculation are encapsulated in Action classes.

### 2. Authorization via Policies
All access control logic is centralized in `TicketPolicy`.

### 3. Idempotent Jobs
The SLA job:
- Marks breaches
- Sends notifications only once
- Is safe to run multiple times

### 4. Clear Separation of Concerns
- Controllers remain thin
- Validation handled via Form Requests
- Side effects handled via Jobs & Notifications

---

## ⚙ Installation

```bash
git clone https://github.com/SouravS-git/support-system.git
cd support-system

composer install
npm install
npm run build

php artisan migrate --seed
php artisan queue:table
php artisan migrate

php artisan queue:work
php artisan schedule:work

php artisan test
