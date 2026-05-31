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

const registerAlertHelpers = () => {
    window.alertSuccess = alertSuccess;
    window.alertError = alertError;
    window.alertWarning = alertWarning;
};

export { confirmButtonColor, registerAlertHelpers };
