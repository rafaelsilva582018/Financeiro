<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Contas</h1>

        <a
            href="{{ route('accounts.create') }}"
            class="px-4 py-2 bg-indigo-600 text-white rounded"
        >
            Nova conta
        </a>
    </div>

    <table class="w-full border">
        <thead>
            <tr class="bg-gray-100">
                <th class="p-2 text-left">Nome</th>
                <th class="p-2 text-left">Tipo</th>
                <th class="p-2 text-right">Saldo inicial</th>
                <th class="p-2 w-32"></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($accounts as $account)
                <tr class="border-t">
                    <td class="p-2">{{ $account->name }}</td>
                    <td class="p-2 capitalize">{{ $account->type }}</td>
                    <td class="p-2 text-right">
                        R$ {{ number_format($account->initial_balance, 2, ',', '.') }}
                    </td>
                    <td class="p-2 flex gap-2 justify-end">
                        <a
                            href="{{ route('accounts.edit', $account) }}"
                            class="text-indigo-600"
                        >
                            Editar
                        </a>

                        <button
                            wire:click="delete({{ $account->id }})"
                            class="text-red-600"
                        >
                            Excluir
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="p-4 text-center text-gray-500">
                        Nenhuma conta cadastrada
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
