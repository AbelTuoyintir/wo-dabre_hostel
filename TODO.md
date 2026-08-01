# Fix: "Load More Rooms" infinite loop in student/hostels/show.blade.php

## Context
- **Issue:** Clicking the "Load More Rooms" button causes an infinite loop (spinning loader overlay that never stops).
- **File:** `resources/views/student/hostels/show.blade.php`
- **Root cause:** The button is a plain `<button>` without `type="button"`. The global loader script in `resources/views/layouts/student.blade.php` attaches a click listener to `button:not([type="button"]):not(.no-loader)`, which calls `showLoading()` showing the full-screen infinite spinner. Since the button has no real action/handler, the spinner never goes away — appearing as an infinite loop.

## Plan
- [x] 1. Read `show.blade.php`, `student.blade.php` layout, `StudentController.php`, routes to understand the issue.
- [x] 2. Add `id="available-rooms-grid"` and `id="load-more-rooms-btn"` to the rooms grid + button.
- [x] 3. Hide room cards beyond the first 6 using `$index` in the `@foreach` loop.
- [x] 4. Add `type="button"` to the "Load More Rooms" button so the global loader no longer intercepts the click.
- [x] 5. Add JS in the existing `@section('scripts')` to reveal the next 6 hidden cards on click, and hide the button once all rooms are shown (and hide it initially if there are <= 6 rooms).
- [x] 6. Verify no infinite loop and that "Load More Rooms" reveals additional rooms correctly.

