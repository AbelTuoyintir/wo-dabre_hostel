# TODO: Fix Images Not Showing (Windows path separator bug)

## Goal
Images not displaying because the `image.proxy` route security boundary check fails on Windows
due to mixed path separators (`realpath()` returns `\`, `storage_path()` returns `/`).

## Steps
1. ✅ Fix `image.proxy` route boundary check in `routes/web.php` (normalize separators).
2. ✅ Fix `/storage/{path}` route boundary check in `routes/web.php` (normalize separators).
3. ✅ Fix `public/image.php` to normalize separators for consistency.
4. ✅ Clean up temporary inspection files (`inspect_image.php`, `inspect_output.txt`).
5. ✅ Run `ImageProxySecurityTest` (4 passed) and `StoragePathTraversalTest` (5 passed) to verify.
