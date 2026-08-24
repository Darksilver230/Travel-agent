<?php
/**
 * payment_config.php
 * -------------------
 * Settings for the two payment methods. Edit the values below.
 *
 * CARD PAYMENTS — WHY THIS USES STRIPE INSTEAD OF A RAW FORM:
 * We deliberately do NOT have an HTML form anywhere on this site
 * that asks for a card number, expiry, or CVV. If you built that
 * yourself and pointed it at your own PHP/database, you would be
 * directly handling cardholder data — which means your whole server
 * falls under PCI-DSS compliance rules (the payment card industry's
 * security standard), you'd need to encrypt/secure that data
 * correctly, and a bug or breach becomes a serious legal/financial
 * problem for you personally.
 *
 * Stripe (like PayPal, Square, etc.) solves this: the customer
 * enters their card on STRIPE'S hosted page, never yours. Your
 * server only ever gets back a success/fail result and a reference
 * ID — no card data ever touches your database. This is the
 * standard approach every real business uses, and it also means you
 * don't have to build/maintain fraud detection, 3D Secure, etc.
 * yourself.
 *
 * TO GO LIVE:
 * 1. Create a free account at https://dashboard.stripe.com/register
 * 2. Go to Developers > API keys
 * 3. Copy your "Secret key" and "Publishable key" (use the TEST
 *    keys while developing — they start with sk_test_ / pk_test_)
 * 4. Paste them below
 */

define('STRIPE_SECRET_KEY', '');       // e.g. 'sk_test_51AbCdEf...'
define('STRIPE_PUBLISHABLE_KEY', '');  // e.g. 'pk_test_51AbCdEf...'

// Shown to customers who choose "Bank Transfer" — replace with your
// real business bank details.
define('BANK_TRANSFER_DETAILS', [
    'account_name'   => 'OLOWOlux Travel Ltd',
    'account_number' => '9064501644',
    'sort_code'      => '12-34-56',   // or routing number, IBAN, etc. depending on your country
    'bank_name'      => 'OPAY',
    'reference_note' => 'Please use your booking ID as the payment reference.',
]);
