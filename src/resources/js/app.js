import 'bootstrap';
import 'sweetalert2/dist/sweetalert2.min.css';
import Swal from 'sweetalert2';

const confirmButtonColor = '#c62828';
const pauseToastOnHover = (toast) => {
    toast.onmouseenter = Swal.stopTimer;
    toast.onmouseleave = Swal.resumeTimer;
};

window.alertSuccess = ({ title = 'Sucesso', text, redirectUrl, timer = 3000 } = {}) => {
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

window.alertWarning = ({
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

document.querySelectorAll('[data-validate-form]').forEach((form) => {
    form.noValidate = true;

    const fields = form.querySelectorAll('input, select, textarea');
    const defaultDateMessage = 'A data deve ser posterior ou igual a data de referencia.';
    const defaultMatchMessage = 'Os campos informados precisam ser iguais.';

    const updateCustomRules = () => {
        form.querySelectorAll('[data-after-or-equal-to]').forEach((field) => {
            if (!(field instanceof HTMLInputElement)) {
                return;
            }

            const referenceField = form.querySelector(field.dataset.afterOrEqualTo);

            if (!(referenceField instanceof HTMLInputElement)) {
                return;
            }

            field.min = referenceField.value;
            field.setCustomValidity('');

            if (referenceField.value && field.value && field.value < referenceField.value) {
                field.setCustomValidity(field.dataset.afterOrEqualMessage || defaultDateMessage);
            }
        });

        form.querySelectorAll('[data-matches-field]').forEach((field) => {
            if (!(field instanceof HTMLInputElement)) {
                return;
            }

            const referenceField = form.querySelector(field.dataset.matchesField);

            if (!(referenceField instanceof HTMLInputElement)) {
                return;
            }

            field.setCustomValidity('');

            if ((referenceField.value || field.value) && field.value !== referenceField.value) {
                field.setCustomValidity(field.dataset.matchesMessage || defaultMatchMessage);
            }
        });
    };

    const feedbackFor = (field) => {
        const existingFeedback = field.parentElement?.querySelector('.invalid-feedback');

        if (existingFeedback) {
            return existingFeedback;
        }

        const generatedFeedback = document.createElement('div');
        generatedFeedback.className = 'invalid-feedback';
        generatedFeedback.dataset.generatedValidationFeedback = 'true';
        field.insertAdjacentElement('afterend', generatedFeedback);

        return generatedFeedback;
    };

    const updateGeneratedFeedback = () => {
        fields.forEach((field) => {
            if (!(field instanceof HTMLInputElement || field instanceof HTMLSelectElement || field instanceof HTMLTextAreaElement)) {
                return;
            }

            if (field.validity.valid) {
                return;
            }

            const feedback = feedbackFor(field);

            if (feedback.dataset.generatedValidationFeedback === 'true' || feedback.textContent.trim() === '') {
                feedback.textContent = field.validationMessage;
            }
        });
    };

    const validateForm = () => {
        updateCustomRules();
        updateGeneratedFeedback();

        return form.checkValidity();
    };

    const revalidateAfterFeedback = () => {
        if (form.classList.contains('was-validated')) {
            validateForm();
        }
    };

    updateCustomRules();

    fields.forEach((field) => {
        field.addEventListener('input', revalidateAfterFeedback);
        field.addEventListener('change', revalidateAfterFeedback);
    });

    form.addEventListener('submit', (event) => {
        if (!validateForm()) {
            event.preventDefault();
            event.stopPropagation();

            const firstInvalidField = form.querySelector(':invalid');

            if (firstInvalidField instanceof HTMLElement) {
                firstInvalidField.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center',
                });
                firstInvalidField.focus({ preventScroll: true });
            }

            window.alertWarning();
        }

        form.classList.add('was-validated');
    });
});
