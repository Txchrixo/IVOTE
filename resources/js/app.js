import './bootstrap';

// Images referenced with Vite::image() must be part of the build manifest
import.meta.glob(['../img/**']);


import 'jquery/dist/jquery.min';
import '@popperjs/core/dist/umd/popper.min.js';
import 'jquery-slimscroll/jquery.slimscroll.min.js';
import 'bootstrap/dist/js/bootstrap.min';
import 'sweetalert2/dist/sweetalert2.min.js';
import '@fortawesome/fontawesome-free/js/all';
import '../vendor/ring-signature/sha1.js';
import '../js/scripts/common.js';
import '../js/scripts/main.js';

