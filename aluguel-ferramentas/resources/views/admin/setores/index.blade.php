<x-app-layout>
    <x-ui.container>

        <h1 class="text-2xl font-bold text-gray-900 mb-6">
            Gestão de Setores
        </h1>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">

            {{-- Filtros --}}
            <form method="GET" class="mb-6">
                <x-ui.filter-box />
            </form>

            {{-- Botão novo --}}
            <div class="mb-6 flex justify-end">
                <x-ui.button-primary href="{{ route('setores.create') }}">
                    + Novo Setor
                </x-ui.button-primary>
            </div>

            {{-- Tabela --}}
            <x-ui.table>
                <x-slot:head>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600">Nome</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600">Ações</th>
                </x-slot:head>

                @foreach($setores as $setor)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $setor->nome }}</td>

                        <td class="px-6 py-4 text-sm text-center">
                            <div class="flex justify-center gap-2">

                                <x-ui.button-secondary href="{{ route('setores.edit', $setor) }}">
                                    Editar
                                </x-ui.button-secondary>

                                <form action="{{ route('setores.destroy', $setor) }}" method="POST"
                                      onsubmit="return confirm('Excluir este setor?');">
                                    @csrf
                                    @method('DELETE')
                                    <x-ui.button-danger>Excluir</x-ui.button-danger>
                                </form>

                            </div>
                        </td>
                    </tr>
                @endforeach

            </x-ui.table>

            <div class="mt-6">
                {{ $setores->links() }}
            </div>

        </div>

    </x-ui.container>
</x-app-layout>
