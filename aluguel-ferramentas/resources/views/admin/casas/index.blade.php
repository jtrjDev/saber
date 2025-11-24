<x-app-layout>
    <x-ui.container>

        <h1 class="text-2xl font-bold text-gray-900 mb-6">
            Casas de Oração
        </h1>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 lg:p-8">

            {{-- Filtros --}}
            <form method="GET" class="mb-6">
                <x-ui.filter-box>

                    <x-ui.select name="setor_id" label="Setor">
                        <option value="">Todos</option>
                        @foreach($setores as $setor)
                            <option value="{{ $setor->id }}"
                                @selected(request('setor_id') == $setor->id)>
                                {{ $setor->nome }}
                            </option>
                        @endforeach
                    </x-ui.select>

                </x-ui.filter-box>
            </form>

            {{-- Botão novo --}}
            <div class="mb-6 flex justify-end">
                <x-ui.button-primary href="{{ route('casas.create') }}">
                    + Nova Casa de Oração
                </x-ui.button-primary>
            </div>

            {{-- Tabela --}}
            <x-ui.table>
                <x-slot:head>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600">Nome</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600">Setor</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600">Ações</th>
                </x-slot:head>

                @foreach($casas as $casa)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-gray-900 text-sm font-medium">
                            {{ $casa->nome }}
                        </td>

                        <td class="px-6 py-4 text-gray-700 text-sm">
                            {{ $casa->setor->nome }}
                        </td>

                        <td class="px-6 py-4 text-center text-sm">
                            <div class="flex justify-center gap-2">

                                <x-ui.button-secondary href="{{ route('casas.edit', $casa) }}">
                                    Editar
                                </x-ui.button-secondary>

                                <form action="{{ route('casas.destroy', $casa) }}" method="POST"
                                      onsubmit="return confirm('Excluir esta casa?');">
                                    @csrf
                                    @method('DELETE')
                                    <x-ui.button-danger>
                                        Excluir
                                    </x-ui.button-danger>
                                </form>

                            </div>
                        </td>
                    </tr>
                @endforeach

            </x-ui.table>

            {{-- Paginação --}}
            <div class="mt-6">
                {{ $casas->links() }}
            </div>

        </div>

    </x-ui.container>
</x-app-layout>
