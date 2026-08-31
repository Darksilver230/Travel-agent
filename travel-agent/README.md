# OLOWO Corp — Starter Website

A working scholarships-&-travel website: browse scholarships and travel
packages, create an account, apply for scholarships / book trips,
pay by card (Stripe) or bank transfer, and send travel requests through
the contact page (saved to the database).

## What's in here

```
travel-agent/
├── index.php                     Homepage: search + featured scholarships & packages
├── services.php                  Services overview page
│
├── universities.php              / Scholarships: all scholarships (+ ?q= search)
├── scholarship.php               One scholarship's details + application form (login required to apply)
├── process_application.php       Saves an application, then sends user to payment.php
├── my_applications.php           Logged-in user's scholarship applications + payment status
│
├── travel.php                    / Travel: all packages (+ ?q= search)
├── trip.php                      One package's details + booking form (login required to book)
├── process_trip.php              Saves a booking, then sends user to payment.php
├── my_trips.php                  Logged-in user's trip bookings + payment status
│
├── register.php / process_register.php    Create an account
├── login.php / process_login.php          Log in
├── logout.php                             Destroys the session
├── my_bookings.php                        Logged-in user's bookings + payment status
│
├── contact.php                   Contact page + travel-request form (public, saved to DB)
│
├── payment.php                   Payment summary + choice of Card / Bank Transfer
├── create_stripe_checkout.php    Starts a Stripe Checkout session (card payments)
├── payment_success.php           Verifies payment with Stripe, marks booking paid
├── process_payment.php           Records a bank transfer reference
│
├── includes/
│   ├── db.php                    Database connection (PDO) + auto-creates tables + seeds data
│   ├── auth.php                  Session/login helper functions
│   ├── mailer.php                Transactional email helper (booking/payment confirmations)
│   ├── payment_config.php        Stripe keys + bank transfer details (EDIT THIS)
│   ├── header.php                Shared <head> + nav (shows login state + Contact link)
│   └── footer.php                Shared footer
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

## Contact page & travel requests (saved to the database)

`contact.php` is a public page reachable from the **Contact** link in
the main navigation (header). Anyone — logged in or not — can fill in
a travel-request form that includes:

- Full Name
- Email
- Destination
- Preferred Travel Date
- Number of Travelers
- Travel Type (Vacation / Business / Study / Family / Other)
- Your Message / Special Requests
- **Send Request** button

Instead of just showing a "Message sent!" alert, the form actually
persists the request to MySQL:

1. **User** fills in and submits the form (`contact.php`, method POST).
2. The **PHP handler** validates every field server-side — it never
   trusts the browser. It checks required fields, email format
   (`filter_var`), that the travel date is a valid `Y-m-d` (via
   `DateTime::createFromFormat`), that the traveler count is at least 1,
   and that "Travel Type" is one of the whitelisted values
   (`vacation | business | study | family | other`). Semantically empty
   date/travel-type just fall back to safe defaults.
3. On success it runs an **INSERT** using a **prepared statement** into
   the `contact_submissions` table, then redirects back with a success
   flash message — so refreshing the page won't double-submit.
4. The **database** (`contact_submissions`) stores the row with a
   `created_at` timestamp. You can view the submissions in phpMyAdmin
   with something like `SELECT id, full_name, email, destination,
   travel_date, travel_type, created_at FROM contact_submissions
   ORDER BY created_at DESC;`. There's no admin UI for this yet — see
   "Things to build next".

The `contact_submissions` table is created automatically by
`includes/db.php` on page load (so it appears even if you never import
`schema.sql`), and it's also declared in `database/schema.sql` for
manual imports.

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

> **Note on the contact date picker:** the "Preferred Travel Date" field
> on `contact.php` uses **Flatpickr** (a small date-picker library) loaded
> from a CDN, and it's themed to match the site's blue palette. This needs
> an internet connection on the visitor's browser. If there's no
> connection (or the CDN is unreachable), the field gracefully falls back
> to a plain text/date field — and PHP still validates the date server-side,
> so nothing breaks.

Try: browse scholarships (`universities.php`) and travel packages
(`travel.php`) → register an account → apply for a scholarship
(`scholarship.php`) or book a trip (`trip.php`) → pay via bank transfer
(works immediately, no setup) or card (needs Stripe keys) → check
`my_applications.php` / `my_trips.php` / `my_bookings.php`. You can also
submit a travel request from the **Contact** page without an account.

## Things to build next (suggested order)

- **Admin panel**: password-protected pages to confirm bank transfers
  (flip `payment_status` to `paid`), add/edit packages, and view all
  bookings (currently done via phpMyAdmin directly)
- **Contact-request inbox**: a simple admin page listing the rows in
  `contact_submissions` (name, email, destination, travel date, number
  of travelers, travel type, message, date received) so you can respond
  to travel requests without opening phpMyAdmin; optionally wire it to
  `mailer.php` so each submission also emails you
- **Stripe webhook**: a more bulletproof way to confirm card payments
  even if the customer closes the tab before `payment_success.php`
  loads (Stripe calls your server directly when a payment completes)
- **Configure real email sending**: transactional emails are sent via
  `includes/mailer.php` using PHP's `mail()`. On XAMPP this needs an SMTP
  agent (e.g. Mercury) or php.ini SMTP settings — see the notes at the
  top of `mailer.php`.
- **"Forgot password" flow**: currently there's no password reset
- **Booking cancellation**: let users cancel an unpaid booking

## Security notes (already handled, but good to understand)

- All database queries use **prepared statements** — never raw string
  concatenation — which blocks SQL injection.
- Passwords are hashed with `password_hash()` / verified with
  `password_verify()` — never stored or compared as plain text.
- All user-supplied text printed into HTML goes through
  `htmlspecialchars()` — blocks basic XSS.
- The contact form (`contact.php`) is validated **server-side**, never
  from the browser: email is checked with `filter_var`, dates with
  `DateTime::createFromFormat('Y-m-d', ...)`, the traveler count must be
  ≥ 1, and the travel type must be on a fixed whitelist before anything
  is written to the database.
- Booking totals are recalculated server-side from the database,
  never trusted from the browser.
- Every page that deals with a specific booking (`payment.php`,
  `process_payment.php`) checks that the booking's `user_id` matches
  the logged-in user — so no one can view or pay for someone else's
  booking just by changing the URL.
- Card data never touches this server or database — see "Why there's
  no card number field anywhere" above.
