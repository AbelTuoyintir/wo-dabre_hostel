# TODO

- [x] Identify missing behavior: amenity “buttons” had no link/submit action.
- [x] Update `resources/views/welcome.blade.php` to make those filters functional via query-string links.
- [ ] Ensure amenity keys used in query string match backend JSON keys expected by `HostelController@index` (`whereJsonContains('amenities', ...)`).
- [ ] Test by visiting `/` and clicking those filters; verify URL query params change and results update.


