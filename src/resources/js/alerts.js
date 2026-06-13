import Swal from 'sweetalert2';

const confirmButtonColor = '#c62828';

const pauseToastOnHover = (toast) => {
    toast.onmouseenter = Swal.stopTimer;
    toast.onmouseleave = Swal.resumeTimer;
};

const alertSuccess = ({ title = 'Sucesso', text, redirectUrl, timer = 3000 } = {}) => {
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title,
        text,
        timer,
        timerProgressBar: true,
        showConfirmButton: false,
        didOpen: pauseToastOnHover,
    }).then(() => {
        if (redirectUrl) {
            window.location.href = redirectUrl;
        }
    });
};

const alertError = ({
    title = 'Nao foi possivel continuar',
    text = 'Verifique os campos informados e tente novamente.',
} = {}) => {
    Swal.fire({
        icon: 'error',
        title,
        text,
        confirmButtonColor,
    });
};

const alertWarning = ({
    title = 'Revise os campos destacados',
    text = 'Ha informacoes invalidas no formulario.',
    timer = 3500,
} = {}) => {
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'warning',
        title,
        text,
        timer,
        timerProgressBar: true,
        showConfirmButton: false,
        didOpen: pauseToastOnHover,
    });
};

const confirmAction = ({
    title = 'Confirmar acao?',
    text,
    confirmButtonText = 'Confirmar',
    cancelButtonText = 'Cancelar',
    buttonColor = confirmButtonColor,
} = {}) => Swal.fire({
    icon: 'question',
    title,
    text,
    showCancelButton: true,
    confirmButtonColor: buttonColor,
    confirmButtonText,
    cancelButtonText,
    reverseButtons: true,
}).then(({ isConfirmed }) => isConfirmed);

const registerAlertHelpers = () => {
    window.alertSuccess = alertSuccess;
    window.alertError = alertError;
    window.alertWarning = alertWarning;
    window.confirmAction = confirmAction;
};

const registerLivewireAlertListeners = (Livewire) => {
    Livewire.on('alert-success', ({ message }) => {
        alertSuccess({ text: message });
    });

    Livewire.on('alert-error', ({ message }) => {
        alertError({ text: message });
    });
};

const initConfirmActions = () => {
    document.addEventListener('submit', async (event) => {
        const form = event.target;

        if (!(form instanceof HTMLFormElement) || !form.dataset.confirmTitle || form.dataset.confirmed === 'true') {
            return;
        }

        event.preventDefault();

        const confirmed = await confirmAction({
            title: form.dataset.confirmTitle,
            text: form.dataset.confirmText,
            confirmButtonText: form.dataset.confirmButtonText,
            buttonColor: form.dataset.confirmButtonColor,
        });

        if (!confirmed) {
            return;
        }

        form.dataset.confirmed = 'true';
        form.requestSubmit();
    });
};

export {
    confirmButtonColor,
    initConfirmActions,
    registerAlertHelpers,
    registerLivewireAlertListeners,
};
