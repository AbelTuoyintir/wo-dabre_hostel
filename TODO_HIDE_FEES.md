# Hide Fees from Frontend — TODO

## Steps

1. [ ] **BookingController.php** — Remove individual fee fields from `calculate()` JSON response (keep only `success`, `nights`, `room_cost`, `total`).
2. [ ] **createGuess.blade.php** — Remove fee rate constants and individual fee calculations from JS.
3. [ ] **create.blade.php** — Remove fee rate constants and individual fee calculations from JS.

