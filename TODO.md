# TODO: Fix guestShow.blade.php Issues

## Issues to Fix:
1. [x] Image column name: change `path` to `image_path` for hostel and room images
2. [x] Contact fields: change `contact_phone` to `phone`, `contact_email` to `email`
3. [x] Similar hostels variable: change `$similarHostels` to `$relatedHostels` in the view
4. [x] Reviews count: add accessor or use `$reviewCount` from controller
5. [x] Rating: use `$averageRating` instead of `$hostel->rating` for consistency
