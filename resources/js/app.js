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

// Shared UI: designed dropdowns, page motion and loading feedback
import '../css/ui.css';
import '../css/layout.css';
import { initDropdowns } from './ui/dropdown.js';
import { initMotion } from './ui/motion.js';
import { initSidebar } from './ui/sidebar.js';

initDropdowns();
initSidebar();
// `$` is the jQuery instance shared with the page scripts (see @rollup/plugin-inject)
initMotion({ jquery: $ });

