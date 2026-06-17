import 'bootstrap';
import 'sweetalert2/dist/sweetalert2.min.css';
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import { initAgendamentoPicker } from './agendamentoPicker';
import { initConfirmActions, registerAlertHelpers, registerLivewireAlertListeners } from './alerts';
import { initCepLookup } from './cepLookup';
import { initFormValidation } from './formValidation';
import { initTheme } from './theme';
import { initPopovers } from './popovers';
import { initAdminCampanhaCalendar } from './adminCampanhaCalendar';

window.Alpine = Alpine;

registerAlertHelpers();
initConfirmActions();
initTheme();
initCepLookup();
initAgendamentoPicker();
initAdminCampanhaCalendar();
Livewire.start();
registerLivewireAlertListeners(Livewire);
initFormValidation();
initPopovers();
