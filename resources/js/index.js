import './bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.min.js';
import '../vendor/linearicons/style.css';
import '../css/main.css';
import '@fortawesome/fontawesome-free/css/brands.css';
import '@fortawesome/fontawesome-free/css/solid.css';
import '@fortawesome/fontawesome-free/css/fontawesome.css';

// Language picker of the authentication pages: navigate to the selected locale
document.querySelectorAll('select[data-locale-switcher]').forEach((select) => {
    select.addEventListener('change', () => {
        window.location.href = select.value;
    });
});
