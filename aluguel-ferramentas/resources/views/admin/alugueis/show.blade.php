<x-app-layout>

    <header class="mb-6 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
            Detalhes do Aluguel #{{ $aluguel->id }}
        </h1>
    </header>

    <div class="bg-white shadow-lg rounded-xl p-6 lg:p-8 max-w-4xl mx-auto">

        {{-- Dados Gerais --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div>
                <p class="text-sm text-gray-500">Casa de Oração</p>
                <p class="font-semibold">
                    {{ $aluguel->casa?->nome ?? '—' }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Membro / Usuário</p>
                <p class="font-semibold">
                    {{ $aluguel->usuario?->name ?? '—' }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Responsável pela Entrega</p>
                <p class="font-semibold">
                    {{ $aluguel->responsavel?->name ?? '—' }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Data da Retirada</p>
                <p class="font-semibold">
                    {{ $aluguel->data_retirada }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Data Prevista</p>
                <p class="font-semibold">
                    {{ $aluguel->data_prevista }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Data da Devolução</p>
                <p class="font-semibold">
                    {{ $aluguel->data_devolucao ?? '—' }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Status</p>
                <span class="px-3 py-1 text-sm rounded-full 
                    {{ $aluguel->status === 'ativo' ? 'bg-yellow-200 text-yellow-800' : 'bg-green-200 text-green-800' }}">
                    {{ ucfirst($aluguel->status) }}
                </span>
            </div>

        </div>

        {{-- Divisor --}}
        <div class="my-8 border-b pb-3">
            <h2 class="text-xl font-bold text-gray-800">Itens do Aluguel</h2>
        </div>

        {{-- Itens --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-300">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-sm font-medium">Ferramenta</th>
                        <th class="px-4 py-2 text-left text-sm font-medium">Qtd</th>
                        <th class="px-4 py-2 text-left text-sm font-medium">Observação</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">

                    @foreach($aluguel->itens as $item)
                        <tr>
                            <td class="px-4 py-2">
                                {{ $item->ferramenta?->nome ?? 'Apagada' }}
                            </td>
                            <td class="px-4 py-2">
                                {{ $item->quantidade }}
                            </td>
                            <td class="px-4 py-2">
                                {{ $item->observacao ?? '—' }}
                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>

        {{-- Botão --}}
        <div class="mt-8">
            <x-ui.button-secondary href="{{ route('alugueis.index') }}">
                Voltar
            </x-ui.button-secondary>
        </div>

    </div>

</x-app-layout>
