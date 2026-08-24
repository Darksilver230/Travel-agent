# Wanderlux Travel — Starter Website

A working travel-booking website: create an account, search packages,
book a trip, and pay by card (Stripe) or bank transfer.

## What's in here

```
travel-agent/
├── index.php                    Homepage: search form + featured packages
├── destinations.php             Search results / full package listing
├── package.php                  One package's details + booking form (login required to book)
├── process_booking.php          Saves the booking, then sends the user to payment.php
│
├── register.php / process_register.php    Create an account
├── login.php / process_login.php          Log in
├── logout.php                             Destroys the session
├── my_bookings.php                        Logged-in user's bookings + payment status
│
├── payment.php                  Booking summary + choice of Card / Bank Transfer
├── create_stripe_checkout.php   Starts a Stripe Checkout session (card payments)
├── payment_success.php          Verifies payment with Stripe, marks booking paid
├── process_payment.php          Records a bank transfer reference
│
├── includes/
│   ├── db.php                    Database connection (PDO)
│   ├── auth.php                  Session/login helper functions
│   ├── payment_config.php        Stripe keys + bank transfer details (EDIT THIS)
│   ├── header.php                 Shared <head> + nav (shows login state)
│   └── footer.php                 Shared footer
├── css/style.css                All styling
├── js/script.js                 Small browser-side touches
└── database/schema.sql          Creates tables + sample destinations/packages
```

## How accounts + booking + payment connect

1. Visitor **registers** (`register.php`) → password is hashed with
   `password_hash()` and stored in the `users` table. We never store
   or can ever look up someone's actual password — only a hash that
   can be checked against, via `password_verify()` at login.
2. Once logged in, visiting a package page (`package.php`) shows a
   booking form (no need to re-type name/email — that comes from the
   account). Submitting it creates a row in `bookings` with
   `payment_status = 'unpaid'`.
3. The user is sent to `payment.php`, which shows the booking total
   and two ways to pay:
   - **Card** → `create_stripe_checkout.php` creates a Stripe Checkout
     Session and redirects the browser to Stripe's own hosted payment
     page. The customer types their card there, not on your site.
     Stripe then redirects back to `payment_success.php`, which asks
     Stripe's API directly "was this actually paid?" before marking
     the booking paid — it never just trusts the redirect happening.
   - **Bank Transfer** → shows your bank details and a form for the
     customer to submit their transfer reference. This can't be
     auto-confirmed (there's no live connection to your bank), so the
     booking is marked `pending_verification` until you manually
     check your bank statement and confirm it (currently done via
     phpMyAdmin — see "Next steps" below for building an admin page).

## Why there's no card number field anywhere

This is intentional, not a missing feature. A form that collects and
stores raw card numbers/CVVs in your own database puts your whole
server under **PCI-DSS** (Payment Card Industry Data Security
Standard) compliance — a serious, ongoing legal/security obligation.
Every real business instead uses a processor like Stripe, PayPal, or
Square: the customer enters their card on the *processor's* page, and
your server only ever receives a success/fail result plus a reference
ID. That's what `create_stripe_checkout.php` does here.

## Setup (local development)

You need PHP + MySQL running locally — the easiest way is **XAMPP**
(Windows/Mac/Linux) or **MAMP** (Mac/Windows). Stripe also requires
the PHP **cURL** extension, which XAMPP/MAMP include by default.

1. **Install XAMPP**: https://www.apachefriends.org/
2. **Copy this whole `travel-agent` folder** into XAMPP's `htdocs`
   directory (e.g. `C:\xampp\htdocs\travel-agent`)
3. **Start Apache and MySQL** from the XAMPP control panel.
4. **Create the database**:
   - Open `http://localhost/phpmyadmin`
   - Click "New", name it `travel_agent`, click Create
   - Click the `travel_agent` database → **Import** tab → choose
     `database/schema.sql` → **Go**
5. **Check `includes/db.php`** — defaults (`root` / no password)
   match XAMPP's defaults.
6. **(Optional) Enable card payments**:
   - Create a free account at https://dashboard.stripe.com/register
   - Go to Developers → API keys, copy the **test** secret + publishable keys
   - Paste them into `includes/payment_config.php`
   - Use Stripe's test card `4242 4242 4242 4242`, any future expiry,
     any CVC, to test a successful payment without real money moving
   - If you skip this step, the site still works — the "Pay by Card"
     button just shows a setup message, and bank transfer works fine
     on its own.
7. **Visit the site**: `http://localhost/travel-agent/index.php`

Try: register an account → search a destination → book a package →
pay via bank transfer (works immediately, no setup) or card (needs
Stripe keys) → check `my_bookings.php`.

## Things to build next (suggested order)

- **Admin panel**: password-protected pages to confirm bank transfers
  (flip `payment_status` to `paid`), add/edit packages, and view all
  bookings (currently done via phpMyAdmin directly)
- **Stripe webhook**: a more bulletproof way to confirm card payments
  even if the customer closes the tab before `payment_success.php`
  loads (Stripe calls your server directly when a payment completes)
- **Email confirmations**: send a real email on successful booking/payment
- **"Forgot password" flow**: currently there's no password reset
- **Booking cancellation**: let users cancel an unpaid booking

## Security notes (already handled, but good to understand)

- All database queries use **prepared statements** — never raw string
  concatenation — which blocks SQL injection.
- Passwords are hashed with `password_hash()` / verified with
  `password_verify()` — never stored or compared as plain text.
- All user-supplied text printed into HTML goes through
  `htmlspecialchars()` — blocks basic XSS.
- Booking totals are recalculated server-side from the database,
  never trusted from the browser.
- Every page that deals with a specific booking (`payment.php`,
  `process_payment.php`) checks that the booking's `user_id` matches
  the logged-in user — so no one can view or pay for someone else's
  booking just by changing the URL.
- Card data never touches this server or database — see "Why there's
  no card number field anywhere" above.
