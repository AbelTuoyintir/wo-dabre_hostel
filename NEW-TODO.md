 Fix Admin Reports Carbon Error

## Plan Steps:
- [x] Step 1: Update resources/views/admin/report.blade.php to parse string months with Carbon::createFromFormat('m', $month)
- [x] Step 2: Clear view cache (php artisan view:clear)
- [x] Step 3: Test /admin/reports loads without error
- [x] Complete
