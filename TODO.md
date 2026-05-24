# TODO - RajaOngkir select_shipping fix

- [ ] Update `OrderController.php` endpoints (`getProvinces`, `getCities`, `getCost`) to return JSON shape expected by `select_shipping.blade.php`.
- [ ] Add graceful fallback datasets (static provinces/cities) when RajaOngkir API fails.
- [ ] Make `select_shipping.blade.php` AJAX handlers defensive and show message if API response format missing.
- [ ] (After code changes) run a quick Laravel command to ensure routes compile and check for syntax errors.
