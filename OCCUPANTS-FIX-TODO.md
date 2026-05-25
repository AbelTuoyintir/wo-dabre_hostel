# Fix Empty Occupants View in HostelManagerDashboard

## Steps:
- [x] Step 1: Edit app/Http/Controllers/HostelManagerDashboard.php occupants() method:
  - Fix hostelIds relation to managedHostels()
  - Replace all 'status' with 'booking_status' in queries
  - Add hostel_id filter support
- [x] Step 2: Clear caches
- [x] Step 3: Test the page
