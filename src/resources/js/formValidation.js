const isFormField = (field) => (
    field instanceof HTMLInputElement
    || field instanceof HTMLSelectElement
    || field instanceof HTMLTextAreaElement
);

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

const updateGeneratedFeedback = (fields) => {
    fields.forEach((field) => {
        if (!isFormField(field) || field.validity.valid) {
            return;
        }

        const feedback = feedbackFor(field);

        if (feedback.dataset.generatedValidationFeedback === 'true' || feedback.textContent.trim() === '') {
            feedback.textContent = field.validationMessage;
        }
    });
};

const focusFirstInvalidField = (form) => {
    const firstInvalidField = form.querySelector(':invalid');

    if (!(firstInvalidField instanceof HTMLElement)) {
        return;
    }

    firstInvalidField.scrollIntoView({
        behavior: 'smooth',
        block: 'center',
    });
    firstInvalidField.focus({ preventScroll: true });
};

const bindFormValidation = (form) => {
    form.noValidate = true;

    const fields = form.querySelectorAll('input, select, textarea');

    const validateForm = () => {
        const valid = form.checkValidity();

        updateGeneratedFeedback(fields);

        return valid;
    };

    const revalidateAfterFeedback = () => {
        if (form.classList.contains('was-validated')) {
            validateForm();
        }
    };

    fields.forEach((field) => {
        field.addEventListener('input', revalidateAfterFeedback);
        field.addEventListener('change', revalidateAfterFeedback);
    });

    form.addEventListener('submit', (event) => {
        if (!validateForm()) {
            event.preventDefault();
            event.stopPropagation();
            focusFirstInvalidField(form);
            window.alertWarning();
        }

        form.classList.add('was-validated');
    });
};

const initFormValidation = () => {
    document.querySelectorAll('[data-validate-form]').forEach(bindFormValidation);
};

export { initFormValidation };
