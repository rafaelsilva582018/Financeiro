<div class="max-w-xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">
        {{ $account ? 'Editar conta' : 'Nova conta' }}
    </h1>

    <form wire:submit.prevent="save" class="space-y-4">
        <div>
            <label class="block text-sm font-medium">Nome</label>
            <input
                type="text"
                wire:model.defer="name"
                class="w-full border rounded p-2"
            >
            @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium">Tipo</label>
            <select
                wire:model.defer="type"
                class="w-full border rounded p-2"
            >
                <option value="wallet">Carteira</option>
                <option value="bank">Banco</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium">Saldo inicial</label>
            <input
                type="number"
                step="0.01"
                wire:model.defer="initial_balance"
                class="w-full border rounded p-2"
            >
        </div>

        <div class="flex gap-2">
            <button
                type="submit"
                class="px-4 py-2 bg-indigo-600 text-white rounded"
            >
                Salvar
            </button>

            <a
                href="{{ route('accounts.index') }}"
                class="px-4 py-2 border rounded"
            >
                Cancelar
            </a>
        </div>
    </form>
</div>
