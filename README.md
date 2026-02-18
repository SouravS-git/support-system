# Support System – Ticket & SLA Management

A workflow-driven support ticket system built with **Laravel 12**, focusing on domain integrity, event-driven architecture, and SLA automation.

This project simulates a real-world internal support tool where customers create tickets, agents manage conversations, and administrators oversee lifecycle and SLA compliance.

---

## 🚀 Features

### 🎟 Ticket Lifecycle
- Customer creates ticket → `OPEN`
- First agent reply → `IN_PROGRESS`
- Agent resolves → `RESOLVED`
- Admin closes → `CLOSED`
- Controlled state transitions enforced at model level

### 👥 Role-Based Access
- **Admin** – Assign, close, monitor SLA
- **Agent** – Reply, resolve
- **Customer** – Create ticket, reply
- Authorization handled via Laravel Policies

### 💬 Ticket Conversation
- Public and internal replies
- First response tracking
- Guarded transition logic
- Clean separation of reply handling using Action classes

### ⏱ SLA Management
- First-response SLA tracking
- Scheduled job checks for SLA breach
- Idempotent breach detection (no duplicate alerts)
- Admin notified via queued email

### 📢 Event-Driven Architecture
- `TicketStatusChanged`
- `TicketAssigned`
- `TicketSlaBreached`

Side effects handled via listeners:
- Email notifications
- Activity logging (audit trail)

### 🧾 Activity Timeline (Audit Log)
All major actions are recorded:
- Assignment
- Status changes
- SLA breach
- Resolution
- Closure

Provides full traceability of ticket lifecycle.

### 🧪 Testing (Pest)
Feature tests covering:
- Authorization rules
- Status transition protection
- SLA job idempotency
- Assignment workflow
- Activity logging

---

## 🏗 Architectural Highlights

- Domain rules enforced inside the `Ticket` model
- Controlled state machine using enum-based transitions
- Application layer separated via Action classes
- Events and Listeners used for side effects
- Idempotent scheduled job design
- Thin controllers
- Clean commit history following conventional commits

This project emphasizes **correctness, maintainability, and architectural clarity over feature quantity.**

---

## 🛠 Tech Stack

- PHP 8.x
- Laravel 12
- MySQL
- Laravel Queue (database driver)
- Laravel Scheduler
- Pest (testing)
- Mailpit (local email testing)
- Laravel Pint (code formatting)

---

## ⚙ Installation

```bash
git clone https://github.com/SouravS-git/support-system.git
cd support-system

composer install
cp .env.example .env
php artisan key:generate

# Configure database in .env

php artisan migrate
php artisan db:seed

php artisan serve
