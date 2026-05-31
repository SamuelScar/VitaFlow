import 'bootstrap';
import 'sweetalert2/dist/sweetalert2.min.css';
import Alpine from 'alpinejs';
import mask from '@alpinejs/mask';
import { registerAlertHelpers } from './alerts';
import { initCepLookup } from './cepLookup';
import { initFormValidation } from './formValidation';
import { initTheme } from './theme';

Alpine.plugin(mask);

window.Alpine = Alpine;

registerAlertHelpers();
initTheme();
initCepLookup();
Alpine.start();
initFormValidation();
