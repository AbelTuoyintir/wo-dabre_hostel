# Hide Fees from Frontend — TODO

## Steps

1. [x] **BookingController.php** — Remove individual fee fields from `calculate()` JSON response (keep only `success`, `nights`, `room_cost`, `total`).
2. [x] **createGuess.blade.php** — Remove fee rate constants and individual fee calculations from JS.
3. [x] **create.blade.php** — Remove fee rate constants and individual fee calculations from JS.
