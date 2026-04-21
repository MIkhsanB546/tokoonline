# Fix Google OAuth: Customer not created after User

## Steps

- [ ]   1. Add detailed logging to CustomerController::callback()
- [ ]   2. Test OAuth flow and check storage/logs/laravel.log
- [ ]   3. Fix based on log output
- [ ]   4. Complete task

Current Status (2026-04-21):

- [x] Logging added & tested: Found google_token too long error
- [x] Migration created & ran: google_token → longText
- [ ] Test OAuth login again (/auth/redirect)
- [ ] Confirm Customer record created (php artisan tinker: Customer::latest(1)->first())
- [ ] Remove debugging logs if desired
