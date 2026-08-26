/**
 * script.js
 * ---------
 * Small client-side touches. The important application logic (saving
 * to the database) happens in PHP — this file just improves the
 * experience in the browser before the form is even submitted.
 */

document.addEventListener('DOMContentLoaded', function () {
    var menuToggle = document.querySelector('.menu-toggle');
    var mainNav = document.querySelector('.main-nav');
    if (menuToggle && mainNav) {
        menuToggle.addEventListener('click', function () {
            this.classList.toggle('active');
            mainNav.classList.toggle('open');
        });
        mainNav.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                menuToggle.classList.remove('active');
                mainNav.classList.remove('open');
            });
        });
    }

    document.querySelectorAll('.nav-dropdown-toggle').forEach(function (toggle) {
        toggle.addEventListener('click', function (e) {
            if (window.innerWidth <= 768) {
                e.preventDefault();
                this.parentElement.classList.toggle('open');
            }
        });
    });

    // Prevent picking a date in the past on any date input.
    const today = new Date().toISOString().split('T')[0];
    document.querySelectorAll('input[type="date"]').forEach(function (input) {
        if (!input.min) input.min = today;
    });

    // Simple client-side check on the application form so users get
    // instant feedback instead of waiting for a server round trip.
    // NOTE: this is a convenience only — process_application.php still
    // re-validates everything server-side.
    const applicationForm = document.querySelector('.application-form');
    if (applicationForm) {
        applicationForm.addEventListener('submit', function (e) {
            const email = applicationForm.querySelector('input[name="email"]');
            if (email && !email.value.includes('@')) {
                e.preventDefault();
                alert('Please enter a valid email address.');
            }
        });
    }
});
