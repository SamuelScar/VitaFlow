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
import { registerLivewireReportChartListeners, registerReportCharts } from './reportCharts';

window.Alpine = Alpine;

registerAlertHelpers();
registerReportCharts();
initConfirmActions();
initTheme();
initCepLookup();
initAgendamentoPicker();
initAdminCampanhaCalendar();
Livewire.start();
registerLivewireReportChartListeners(Livewire);
registerLivewireAlertListeners(Livewire);
initFormValidation();
initPopovers();
