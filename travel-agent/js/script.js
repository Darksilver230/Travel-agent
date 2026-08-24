/**
 * script.js
 * ---------
 * Small client-side touches. The important booking logic (saving
 * to the database) happens in PHP — this file just improves the
 * experience in the browser before the form is even submitted.
 */

document.addEventListener('DOMContentLoaded', function () {
    // Prevent picking a travel date in the past on any date input.
    const today = new Date().toISOString().split('T')[0];
    document.querySelectorAll('input[type="date"]').forEach(function (input) {
        if (!input.min) input.min = today;
    });

    // Simple client-side check on the booking form so users get
    // instant feedback instead of waiting for a server round trip.
    // NOTE: this is a convenience only — process_booking.php still
    // re-validates everything server-side, because client-side
    // checks can always be bypassed.
    const bookingForm = document.querySelector('.booking-form');
    if (bookingForm) {
        bookingForm.addEventListener('submit', function (e) {
            const email = bookingForm.querySelector('input[name="email"]').value;
            if (!email.includes('@')) {
                e.preventDefault();
                alert('Please enter a valid email address.');
            }
        });
    }
});
