import './bootstrap';
import Alpine from 'alpinejs';
import flatpickr from 'flatpickr';
import { Indonesian } from 'flatpickr/dist/l10n/id.js';
import { Html5Qrcode } from 'html5-qrcode';

import 'flatpickr/dist/flatpickr.min.css';
import 'flatpickr/dist/themes/dark.css';

window.Alpine = Alpine;
window.flatpickr = flatpickr;
window.flatpickrIndonesian = Indonesian;
window.Html5Qrcode = Html5Qrcode;

Alpine.start();
