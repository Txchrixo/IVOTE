import './bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.min.js';
import '../vendor/linearicons/style.css';
import '../css/main.css';
import '@fortawesome/fontawesome-free/css/brands.css';
import '@fortawesome/fontawesome-free/css/solid.css';
import '@fortawesome/fontawesome-free/css/fontawesome.css';

// Shared UI: designed dropdowns, page motion and loading feedback
import '../css/ui.css';
import { initDropdowns } from './ui/dropdown.js';
import { initMotion } from './ui/motion.js';
import { initToasts } from './ui/toast.js';

initDropdowns();
initToasts();
initMotion();

