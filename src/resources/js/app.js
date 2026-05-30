import 'bootstrap';
import 'sweetalert2/dist/sweetalert2.min.css';
import Swal from 'sweetalert2';

const confirmButtonColor = '#c62828';
const themeStorageKey = 'vitaflow-theme';
const themeOptions = ['system', 'light', 'dark'];
const themeLabels = {
    system: 'Sistema',
    light: 'Claro',
    dark: 'Escuro',
};
const themeIcons = {
    system: 'bi-circle-half',
    light: 'bi-sun',
    dark: 'bi-moon-stars',
};
const systemThemeQuery = window.matchMedia('(prefers-color-scheme: dark)');

const isValidTheme = (theme) => themeOptions.includes(theme);

const getStoredTheme = () => {
    try {
        const storedTheme = localStorage.getItem(themeStorageKey);

        return isValidTheme(storedTheme) ? storedTheme : 'system';
    } catch {
        return 'system';
    }
};

const storeTheme = (theme) => {
    try {
        localStorage.setItem(themeStorageKey, theme);
    } catch {
        // localStorage can be unavailable in restrictive browser settings.
    }
};

const resolveTheme = (theme) => {
    if (theme === 'system') {
        return systemThemeQuery.matches ? 'dark' : 'light';
    }

    return theme;
};

const updateThemeControls = (theme) => {
    document.querySelectorAll('[data-theme-value]').forEach((button) => {
        const active = button.dataset.themeValue === theme;

        button.classList.toggle('active', active);
        button.setAttribute('aria-pressed', active ? 'true' : 'false');
        button.querySelector('[data-theme-check]')?.classList.toggle('d-none', !active);
    });

    document.querySelectorAll('[data-theme-toggle-label]').forEach((label) => {
        label.textContent = themeLabels[theme] || themeLabels.system;
    });

    document.querySelectorAll('[data-theme-toggle-icon]').forEach((icon) => {
        icon.className = `bi ${themeIcons[theme] || themeIcons.system}`;
    });

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        const label = `Tema: ${themeLabels[theme] || themeLabels.system}`;

        button.setAttribute('aria-label', label);
        button.setAttribute('title', label);
    });
};

const applyTheme = (theme, persist = false) => {
    const preference = isValidTheme(theme) ? theme : 'system';
    const resolvedTheme = resolveTheme(preference);

    document.documentElement.dataset.themePreference = preference;
    document.documentElement.dataset.bsTheme = resolvedTheme;
    document.documentElement.style.colorScheme = resolvedTheme;

    if (persist) {
        storeTheme(preference);
    }

    updateThemeControls(preference);
};

applyTheme(getStoredTheme());

document.querySelectorAll('[data-theme-value]').forEach((button) => {
    button.addEventListener('click', () => {
        applyTheme(button.dataset.themeValue, true);
    });
});

const refreshSystemTheme = () => {
    if (document.documentElement.dataset.themePreference === 'system') {
        applyTheme('system');
    }
};

if (systemThemeQuery.addEventListener) {
    systemThemeQuery.addEventListener('change', refreshSystemTheme);
} else {
    systemThemeQuery.addListener(refreshSystemTheme);
}

const onlyDigits = (value) => value.replace(/\D/g, '');

const formatCep = (value) => {
    const digits = onlyDigits(value).slice(0, 8);

    if (digits.length <= 5) {
        return digits;
    }

    return `${digits.slice(0, 5)}-${digits.slice(5)}`;
};

const fetchViaCep = async (cep) => {
    const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);

    if (!response.ok) {
        return null;
    }

    const data = await response.json();

    if (data.erro) {
        return null;
    }

    return {
        logradouro: data.logradouro || '',
        bairro: data.bairro || '',
        cidade: data.localidade || '',
        uf: data.uf || '',
    };
};

const fetchBrasilApiCep = async (cep) => {
    const response = await fetch(`https://brasilapi.com.br/api/cep/v1/${cep}`);

    if (!response.ok) {
        return null;
    }

    const data = await response.json();

    return {
        logradouro: data.street || '',
        bairro: data.neighborhood || '',
        cidade: data.city || '',
        uf: data.state || '',
    };
};

const fetchAddressByCep = async (cep) => {
    try {
        const viaCepAddress = await fetchViaCep(cep);

        if (viaCepAddress) {
            return viaCepAddress;
        }
    } catch {
        // BrasilAPI is used as a fallback when ViaCEP is unavailable.
    }

    try {
        return await fetchBrasilApiCep(cep);
    } catch {
        return null;
    }
};

document.querySelectorAll('[data-cep-lookup]').forEach((form) => {
    const cepField = form.querySelector('[data-cep-field="cep"]');

    if (!(cepField instanceof HTMLInputElement)) {
        return;
    }

    const fields = {
        logradouro: form.querySelector('[data-cep-field="logradouro"]'),
        bairro: form.querySelector('[data-cep-field="bairro"]'),
        cidade: form.querySelector('[data-cep-field="cidade"]'),
        uf: form.querySelector('[data-cep-field="uf"]'),
        numero: form.querySelector('[data-cep-field="numero"]'),
    };
    let lastResolvedCep = null;

    const fillField = (field, value) => {
        if (!(field instanceof HTMLInputElement) || !value) {
            return;
        }

        field.value = value;
        field.dispatchEvent(new Event('input', { bubbles: true }));
    };

    const lookupCep = async () => {
        const cep = onlyDigits(cepField.value);
        cepField.value = formatCep(cepField.value);
        cepField.setCustomValidity('');

        if (cep.length !== 8 || cep === lastResolvedCep) {
            return;
        }

        const address = await fetchAddressByCep(cep);

        if (!address) {
            cepField.setCustomValidity('CEP nao encontrado.');
            lastResolvedCep = null;
            return;
        }

        lastResolvedCep = cep;

        fillField(fields.logradouro, address.logradouro);
        fillField(fields.bairro, address.bairro);
        fillField(fields.cidade, address.cidade);
        fillField(fields.uf, address.uf.toUpperCase());

        if (fields.numero instanceof HTMLInputElement && !fields.numero.value) {
            fields.numero.focus();
        }
    };

    cepField.addEventListener('input', () => {
        cepField.value = formatCep(cepField.value);
        cepField.setCustomValidity('');

        if (onlyDigits(cepField.value).length === 8) {
            lookupCep();
        }
    });
    cepField.addEventListener('blur', lookupCep);
    cepField.addEventListener('change', lookupCep);
});

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

window.confirmAccountDeletion = async ({ form, initialError = null } = {}) => {
    if (!(form instanceof HTMLFormElement)) {
        return;
    }

    const passwordField = form.querySelector('[data-delete-account-password]');

    if (!(passwordField instanceof HTMLInputElement)) {
        return;
    }

    if (!initialError) {
        let intervalId;
        let remainingSeconds = 5;

        const warningResult = await Swal.fire({
            icon: 'warning',
            title: 'Excluir conta',
            html: `
                <p class="mb-2">Essa acao remove sua conta e encerra seu acesso ao sistema.</p>
                <p class="mb-0 text-secondary" id="delete-account-countdown">
                    Leia com atencao. Voce podera continuar em ${remainingSeconds} segundos.
                </p>
            `,
            confirmButtonText: `Continuar (${remainingSeconds})`,
            cancelButtonText: 'Cancelar',
            confirmButtonColor,
            showCancelButton: true,
            reverseButtons: true,
            allowOutsideClick: false,
            didOpen: () => {
                const confirmButton = Swal.getConfirmButton();
                const countdown = document.querySelector('#delete-account-countdown');

                if (!confirmButton) {
                    return;
                }

                confirmButton.disabled = true;

                intervalId = window.setInterval(() => {
                    remainingSeconds -= 1;

                    if (remainingSeconds <= 0) {
                        window.clearInterval(intervalId);
                        confirmButton.disabled = false;
                        confirmButton.textContent = 'Continuar';

                        if (countdown) {
                            countdown.textContent = 'Voce ja pode continuar se tiver certeza.';
                        }

                        return;
                    }

                    confirmButton.textContent = `Continuar (${remainingSeconds})`;

                    if (countdown) {
                        countdown.textContent = `Leia com atencao. Voce podera continuar em ${remainingSeconds} segundos.`;
                    }
                }, 1000);
            },
            willClose: () => {
                window.clearInterval(intervalId);
            },
        });

        if (!warningResult.isConfirmed) {
            return;
        }
    }

    const passwordResult = await Swal.fire({
        icon: initialError ? 'error' : 'question',
        title: 'Confirme sua senha',
        text: initialError || 'Informe sua senha atual para confirmar a exclusao.',
        input: 'password',
        inputLabel: 'Senha atual',
        inputAttributes: {
            autocomplete: 'current-password',
        },
        confirmButtonText: 'Excluir conta',
        cancelButtonText: 'Cancelar',
        confirmButtonColor,
        showCancelButton: true,
        reverseButtons: true,
        preConfirm: (password) => {
            if (!password) {
                Swal.showValidationMessage('Informe sua senha atual.');
                return false;
            }

            return password;
        },
    });

    if (!passwordResult.isConfirmed) {
        return;
    }

    passwordField.value = passwordResult.value;

    Swal.fire({
        title: 'Excluindo conta',
        text: 'Validando senha, encerrando sessao e removendo a conta.',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        },
    });

    window.setTimeout(() => {
        form.submit();
    }, 700);
};

document.querySelectorAll('[data-delete-account-button]').forEach((button) => {
    button.addEventListener('click', () => {
        window.confirmAccountDeletion({
            form: document.querySelector('[data-delete-account-form]'),
        });
    });
});

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
    const defaultRequiredWithMessage = 'Informe este campo antes de continuar.';

    const updateCustomRules = () => {
        form.querySelectorAll('[data-after-or-equal-to], [data-matches-field], [data-required-with]').forEach((field) => {
            if (field instanceof HTMLInputElement) {
                field.setCustomValidity('');
            }
        });

        form.querySelectorAll('[data-after-or-equal-to]').forEach((field) => {
            if (!(field instanceof HTMLInputElement)) {
                return;
            }

            const referenceField = form.querySelector(field.dataset.afterOrEqualTo);

            if (!(referenceField instanceof HTMLInputElement)) {
                return;
            }

            field.min = referenceField.value;

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

            if ((referenceField.value || field.value) && field.value !== referenceField.value) {
                field.setCustomValidity(field.dataset.matchesMessage || defaultMatchMessage);
            }
        });

        form.querySelectorAll('[data-required-with]').forEach((field) => {
            if (!(field instanceof HTMLInputElement)) {
                return;
            }

            const referenceField = form.querySelector(field.dataset.requiredWith);

            if (!(referenceField instanceof HTMLInputElement)) {
                return;
            }

            if (referenceField.value && !field.value) {
                field.setCustomValidity(field.dataset.requiredWithMessage || defaultRequiredWithMessage);
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
