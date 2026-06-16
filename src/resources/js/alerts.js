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
    confirmDelayMs = 0,
} = {}) => {
    const delayMs = Number(confirmDelayMs) || 0;
    let delayInterval;

    return Swal.fire({
        icon: 'question',
        title,
        text,
        showCancelButton: true,
        confirmButtonColor: buttonColor,
        confirmButtonText,
        cancelButtonText,
        reverseButtons: true,
        didOpen: () => {
            if (delayMs <= 0) {
                return;
            }

            const confirmButton = Swal.getConfirmButton();
            const startedAt = Date.now();

            if (!confirmButton) {
                return;
            }

            confirmButton.disabled = true;

            const updateConfirmButton = () => {
                const remainingMs = delayMs - (Date.now() - startedAt);
                const remainingSeconds = Math.ceil(Math.max(remainingMs, 0) / 1000);

                if (remainingMs > 0) {
                    confirmButton.textContent = `${confirmButtonText} (${remainingSeconds})`;

                    return;
                }

                confirmButton.textContent = confirmButtonText;
                confirmButton.disabled = false;
                window.clearInterval(delayInterval);
            };

            updateConfirmButton();
            delayInterval = window.setInterval(updateConfirmButton, 250);
        },
        willClose: () => {
            window.clearInterval(delayInterval);
        },
    }).then(({ isConfirmed }) => isConfirmed);
};

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

        if (form.matches('[data-validate-form]') && !form.checkValidity()) {
            return;
        }

        event.preventDefault();

        const confirmed = await confirmAction({
            title: form.dataset.confirmTitle,
            text: form.dataset.confirmText,
            confirmButtonText: form.dataset.confirmButtonText,
            buttonColor: form.dataset.confirmButtonColor,
            confirmDelayMs: form.dataset.confirmDelayMs,
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
