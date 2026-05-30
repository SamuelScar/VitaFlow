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

document.querySelectorAll('[data-editable-pass]').forEach((form) => {
    const editButton = form.querySelector('[data-edit-pass-button]');
    const saveButton = form.querySelector('[data-save-pass-button]');
    const fields = form.querySelectorAll('[data-pass-field]');

    const enableEditing = () => {
        fields.forEach((field) => {
            field.removeAttribute('readonly');
            field.removeAttribute('disabled');
        });

        form.classList.add('is-editing');
        editButton?.classList.add('d-none');
        saveButton?.classList.remove('d-none');
        fields[0]?.focus();
    };

    if (form.dataset.editing === 'true') {
        enableEditing();
    }

    editButton?.addEventListener('click', enableEditing);
});
