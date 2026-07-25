# Persona feature tests (Hostel agents / managers / students)

## Step 1: Agent persona tests (no addCommission/withdraw dependency)
- ✅ Created `tests/Feature/Personas/AgentPersonaTest.php`
- Cover:
  - Agent registration validation + DB creation
  - Profile completion + pending gating
  - Settings update + password update success/failure
  - Approved-only gates (pending/suspended/active)
  - Admin agent management approve/suspend/activate/deactivate
  - Admin process withdrawal approve/reject (refund on reject)




## Step 2: Factories needed for Agent/Admin tests
- Add `database/factories/HostelAgentFactory.php`
- Add `database/factories/AgentCommissionFactory.php` (only if needed for dashboard view)
- Add `database/factories/AgentWithdrawalFactory.php`

## Step 3: Hostel manager persona tests
- Add `tests/Feature/Personas/HostelManagerPersonaTest.php`
- Cover:
  - Auth + middleware access
  - Ownership/permission 403 behavior on hostel/room/booking endpoints

## Step 4: Student persona tests
- Add `tests/Feature/Personas/StudentPersonaTest.php`
- Cover:
  - Auth + StudentMiddleware access
  - Basic student booking-related endpoints that exist in routes

## Step 5: Run and fix
- Run `php artisan test`
- Fix failing assertions/routes/schema issues

