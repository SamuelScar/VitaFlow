<div
    class="card-footer bg-white border-top p-4"
    x-data="{
        confirmingDeletion: @js($errors->deleteConta->any()),
        understood: false,
        deletePassword: '',
    }"
    x-init="confirmingDeletion && $nextTick(() => $refs.deletePassword?.focus())"
>
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div>
            <strong class="d-block">Zona de risco</strong>
            <span class="text-secondary small">A exclusao da conta exige confirmacao por senha.</span>
        </div>

        <button
            class="btn btn-outline-danger d-inline-flex align-items-center gap-2"
            type="button"
            x-bind:aria-expanded="confirmingDeletion.toString()"
            aria-controls="delete-account-panel"
            x-on:click="confirmingDeletion = true; $nextTick(() => $refs.deletePassword?.focus())"
        >
            <i class="bi bi-trash" aria-hidden="true"></i>
            Excluir conta
        </button>
    </div>

    <form
        method="POST"
        action="{{ route('conta.destroy') }}"
        class="border border-danger-subtle rounded-3 p-3 mt-4"
        id="delete-account-panel"
        x-cloak
        x-show="confirmingDeletion"
    >
        @csrf
        @method('DELETE')

        <div class="d-flex gap-2 mb-3">
            <i class="bi bi-exclamation-triangle text-danger mt-1" aria-hidden="true"></i>
            <div>
                <strong class="d-block text-danger">Excluir conta permanentemente</strong>
                <p class="text-secondary small mb-0">
                    Essa acao remove sua conta, encerra sua sessao e exige sua senha atual.
                </p>
            </div>
        </div>

        <div class="form-check mb-3">
            <input
                class="form-check-input"
                id="delete_account_confirmation"
                type="checkbox"
                x-model="understood"
            >
            <label class="form-check-label" for="delete_account_confirmation">
                Entendo que essa acao nao pode ser desfeita.
            </label>
        </div>

        <div class="mb-3">
            <x-forms.input
                error-bag="deleteConta"
                id="delete_account_password"
                label="Senha atual"
                name="password"
                type="password"
                autocomplete="current-password"
                x-model="deletePassword"
                x-ref="deletePassword"
                required
            />
        </div>

        <div class="d-flex flex-column flex-sm-row justify-content-end gap-2">
            <button
                class="btn btn-outline-secondary"
                type="button"
                x-on:click="confirmingDeletion = false; understood = false; deletePassword = ''"
            >
                Cancelar
            </button>
            <button
                class="btn btn-danger d-inline-flex align-items-center justify-content-center gap-2"
                type="submit"
                x-bind:disabled="!understood || deletePassword.length === 0"
            >
                <i class="bi bi-trash" aria-hidden="true"></i>
                Excluir definitivamente
            </button>
        </div>
    </form>
</div>
