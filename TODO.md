# Fix: Image Upload Progress Bar Not Working on `/admin/rooms/create`

## Root Cause (Issue 1)
The `layouts/app.blade.php` layout is missing `@stack('scripts')` and `@stack('styles')` directives. The `create.blade.php` view uses `@push('scripts')` and `@push('styles')` to inject JavaScript that powers the upload progress bars, but these pushed content blocks are never rendered because the layout doesn't have the corresponding `@stack()` calls.

## Root Cause (Issue 2 - Validation Error)
The original static hidden input `<input type="hidden" name="temp_gallery_paths[]" id="temp_gallery_paths" value="">` had an empty string value. The JavaScript `updateGalleryHiddenInputs()` function tried to clear it via `container.innerHTML = ''`, but this doesn't work on `<input>` elements (void elements). The empty-valued input was always submitted with the form, causing the `temp_gallery_paths.0` field validation error. The `temp_gallery_paths` was replaced with a `<div>` container, and the JavaScript now properly appends hidden inputs to it.

## Steps

### Step 1: ✅ Add `@stack('styles')` to `layouts/app.blade.php`
- Added `@stack('styles')` in the `<head>` section after the existing SweetAlert2 CSS link

### Step 2: ✅ Add `@stack('scripts')` to `layouts/app.blade.php`
- Added `@stack('scripts')` before closing `</body>` tag, after the existing script blocks

### Step 3: ✅ Fix the `temp_gallery_paths` hidden input handling
- Replaced the static `<input name="temp_gallery_paths[]">` with `<div id="temp_gallery_paths_container">`
- Updated `updateGalleryHiddenInputs()` to use the container div and `appendChild()` instead of the buggy `insertBefore` approach

### Step 4: ✅ Clear view cache
- Ran `php artisan view:clear` successfully
