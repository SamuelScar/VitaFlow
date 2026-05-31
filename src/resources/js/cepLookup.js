const onlyDigits = (value) => value.replace(/\D/g, '');

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

const fillField = (field, value) => {
    if (!(field instanceof HTMLInputElement) || !value) {
        return;
    }

    field.value = value;
    field.dispatchEvent(new Event('input', { bubbles: true }));
};

const bindCepLookup = (form) => {
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

    const lookupCep = async () => {
        const cep = onlyDigits(cepField.value);
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
        cepField.setCustomValidity('');

        if (onlyDigits(cepField.value).length === 8) {
            lookupCep();
        }
    });
    cepField.addEventListener('blur', lookupCep);
    cepField.addEventListener('change', lookupCep);
};

const initCepLookup = () => {
    document.querySelectorAll('[data-cep-lookup]').forEach(bindCepLookup);
};

export { initCepLookup };
