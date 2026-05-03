# Restaurant Reservation System — PHP OOP & Laravel Deep Dive

A guided project building a reservation & waitlist system twice: first in vanilla PHP applying SOLID rigorously, then rebuilt in Laravel. Designed to develop interview-ready depth in OOP, design patterns, and framework architecture.

---

## Tech Stack

**Vanilla PHP layer:** PHP 8+, PDO, PHPUnit, hand-rolled DI container via Reflection
**Laravel layer:** Laravel 11, Eloquent, Pest, queued jobs, Mailables
**Testing:** Unit, integration (transaction-based), and feature tests across both layers

---

## Core Concepts Demonstrated

### Object-Oriented Design
- **Rich domain models** over anemic data containers — entities enforce their own invariants
- **Value objects** (`TimeSlot`, `PartySize`) — immutable, equal by value, refuse invalid construction
- **Entities** (`Reservation`, `Customer`) — equal by identity, encapsulate state transitions
- **Tell, Don't Ask** — `$reservation->cancel($reason)` instead of `$reservation->setStatus(CANCELLED)`
- **Enum-driven state machines** for reservation lifecycle (Pending → Confirmed → Seated → Completed)

### SOLID Principles, Applied
- **SRP** — `AvailabilityChecker`, `BookingPolicy`, `Notifier` each have one reason to change
- **OCP** — new business rules (blackout dates, party-size limits) added as new policy classes, never by editing `BookingService`
- **LSP** — composition over inheritance throughout
- **ISP** — `Notifier` split so `EmailNotifier` doesn't fake SMS methods
- **DIP** — `BookingService` depends on `ReservationRepositoryInterface`, not on PDO or Eloquent

### Design Patterns
- **Repository pattern** — abstracts persistence, enables swapping PDO for Eloquent without touching business logic
- **Strategy pattern** — interchangeable `BookingPolicy` implementations
- **Dependency Injection** — constructor injection, no service locators, no static state
- **Observer / Event-driven** — reservation cancellation triggers waitlist promotion via Laravel events
- **Factory pattern** — test factories, model factories
- **Service layer** — business logic lives in services, not controllers or models

### Framework Internals (Laravel)
- **Service Container** — understand it by first writing one (~50 lines, Reflection-based)
- **Service Providers** — boot order, deferred providers, tagged bindings
- **Facades** — what they actually are (proxies to container bindings) and when they hurt testability
- **Eloquent** — relationships, casts, scopes (global + local), accessors, model events
- **Form Requests** — validation + authorization, kept out of controllers
- **Policies & Gates** — authorization logic separated from business logic
- **Middleware** — the right home for cross-cutting concerns (logging, slow-query detection)
- **Route model binding** — implicit and scoped to authenticated user
- **Queued jobs** — async work via `PromoteFromWaitlist`, tested with `Queue::fake()`
- **Artisan commands** — custom commands for scheduled cleanup tasks

### Testing Strategy
- **Unit tests** — value objects, individual policies, in isolation
- **Integration tests** — repository against a real database, transaction-rolled-back per test
- **Feature tests** — booking flow end-to-end, including failure paths
- **Test doubles** — `Mail::fake()`, `Queue::fake()` for verifying side effects without executing them

---

## Architecture Highlights

```
Vanilla PHP                    Laravel
─────────────                  ─────────────
TimeSlot (VO)        ───►      Custom cast / VO wrapper
PartySize (VO)       ───►      Custom cast
Reservation (entity) ───►      Eloquent model + service-enforced invariants
BookingService       ───►      BookingService (still earns its keep)
BookingPolicy[]      ───►      Tagged container bindings, injected as iterable
PdoRepository        ───►      Eloquent (or repository wrapping it)
Hand-rolled DI       ───►      Service Container
bootstrap.php        ───►      Service Providers
Manual routing       ───►      routes/api.php + controllers
```

The deliberate mapping makes framework "magic" legible: every Laravel feature is a known solution to a problem you've already felt.

---

## Capstone Feature: Waitlist with Auto-Promotion

When a slot is full, customers join a waitlist. On cancellation:
1. Model event fires on `Reservation::cancel()`
2. Listener dispatches a queued `PromoteFromWaitlist` job
3. Job promotes the next waitlist entry to a confirmed reservation
4. Mailable notifies the promoted customer
5. Feature test verifies the entire flow with faked queue and mail

This exercises events, queues, jobs, mailables, and the full testing pyramid — and is itself a strong systems-design interview talking point.

---
