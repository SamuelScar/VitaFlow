import 'bootstrap';
import 'sweetalert2/dist/sweetalert2.min.css';
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import { registerAlertHelpers } from './alerts';
import { initCepLookup } from './cepLookup';
import { initFormValidation } from './formValidation';
import { initTheme } from './theme';

window.Alpine = Alpine;

registerAlertHelpers();
initTheme();
initCepLookup();
Livewire.start();
initFormValidation();
