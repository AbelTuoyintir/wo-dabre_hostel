# Database SQLite Connection Fix

## Current Issue
- DB_DATABASE=wodabre in .env (relative path, file missing)
- database/database.sqlite exists but not used.

## Steps to Fix

1. **Edit .env** (VSCode has it open):
   ```
   DB_CONNECTION=sqlite
   DB_DATABASE=database/database.sqlite
   ```
   - Comment out MySQL lines if not using:
   ```
   # DB_HOST=127.0.0.1
   # DB_PORT=3306
   # DB_USERNAME=root
   # DB_PASSWORD=
   ```

2. **Clear config cache**:
   ```
   php artisan config:clear
   ```

3. **Run migrations**:
   ```
   php artisan migrate
   ```

4. **Verify**:
   ```
   php artisan migrate:status
   ```

5. **Test app** (e.g., php artisan serve)

✅ Mark steps as done and delete this file when complete.
