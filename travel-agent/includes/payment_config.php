<?php
/**
 * payment_config.php
 * -------------------
 * Settings for the two payment methods. Edit the values below.
 *
 * CARD PAYMENTS — WHY THIS USES STRIPE INSTEAD OF A RAW FORM:
 * We do NOT have an HTML form that asks for a card number, expiry,
 * or CVV. Stripe handles card entry on their hosted page, keeping
 * your server out of PCI-DSS scope entirely.
 *
 * TO GO LIVE:
 * 1. Create a free account at https://dashboard.stripe.com/register
 * 2. Go to Developers > API keys
 * 3. Copy your "Secret key" and "Publishable key" (use TEST keys
 *    while developing — they start with sk_test_ / pk_test_)
 * 4. Paste them below
 */

define('STRIPE_SECRET_KEY', '');       // e.g. 'sk_test_51AbCdEf...'
define('STRIPE_PUBLISHABLE_KEY', '');  // e.g. 'pk_test_51AbCdEf...'

define('BANK_TRANSFER_DETAILS', [
    'account_name'   => 'OLOWO Corp',
    'account_number' => '9064501644',
    'sort_code'      => '12-34-56',
    'bank_name'      => 'OPAY',
    'reference_note' => 'Please use your application ID as the payment reference.',
]);
