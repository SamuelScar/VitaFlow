import 'bootstrap';
import 'sweetalert2/dist/sweetalert2.min.css';
import Swal from 'sweetalert2';

const confirmButtonColor = '#c62828';

window.alertSuccess = ({ title = 'Sucesso', text, redirectUrl, timer = 3000 } = {}) => {
    Swal.fire({
        icon: 'success',
        title,
        text,
        confirmButtonColor,
        timer,
        timerProgressBar: true,
        allowOutsideClick: !redirectUrl,
        allowEscapeKey: !redirectUrl,
        showConfirmButton: !redirectUrl,
    }).then(() => {
        if (redirectUrl) {
            window.location.href = redirectUrl;
        }
    });
};

window.alertError = ({
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
