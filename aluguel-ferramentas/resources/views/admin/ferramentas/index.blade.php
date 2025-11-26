<x-app-layout>

    <header class="mb-6 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
            Ferramentas
        </h1>
    </header>

    <div class="bg-white shadow-lg rounded-xl p-6 lg:p-8">

        {{-- FILTROS --}}
        <form method="GET" class="mb-6">
            <x-ui.filter-box>

                {{-- Filtro por setor --}}
                <x-ui.select name="setor_id" label="Setor">
                    <option value="">Todos</option>
                    @foreach($setores as $s)
                        <option value="{{ $s->id }}" @selected(request('setor_id') == $s->id)>
                            {{ $s->nome }}
                        </option>
                    @endforeach
                </x-ui.select>

                {{-- filtro por estado --}}
                <x-ui.select name="estado" label="Estado">
                    <option value="">Todos</option>
                    <option value="bom" @selected(request('estado')=='bom')>Bom</option>
                    <option value="ruim" @selected(request('estado')=='ruim')>Ruim</option>
                    <option value="manutenção" @selected(request('estado')=='manutenção')>Manutenção</option>
                    <option value="quebrado" @selected(request('estado')=='quebrado')>Quebrado</option>
                </x-ui.select>

            </x-ui.filter-box>
        </form>

        <div class="mb-6 flex justify-end">
            <x-ui.button-primary href="{{ route('ferramentas.create') }}">
                + Nova Ferramenta
            </x-ui.button-primary>
        </div>

        <x-ui.table>
            <x-slot:head>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Foto</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Setor</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Ações</th>
            </x-slot:head>

            @foreach($ferramentas as $f)
                <tr class="hover:bg-gray-50">

                    {{-- Foto --}}
                    <td class="px-6 py-4">
                        @if($f->foto)
                            <img src="{{ asset('storage/'.$f->foto) }}" class="h-12 w-12 rounded object-cover">
                        @else
                            <span class="text-gray-400 text-sm">Sem foto</span>
                        @endif
                    </td>

                    <td class="px-6 py-4">{{ $f->nome }}</td>

                    <td class="px-6 py-4">{{ $f->setor->nome }}</td>

                    <td class="px-6 py-4 capitalize">
                        @php
                            $cores = [
                                    'nova'        => 'bg-green-200 text-green-900',
                                    'boa'         => 'bg-blue-200 text-blue-900',
                                    'ruim'        => 'bg-yellow-200 text-yellow-900',
                                    'manutenção'  => 'bg-orange-200 text-orange-900',
                                    'quebrado'    => 'bg-red-200 text-red-900',
                                ];
                        @endphp


                        <span class="px-2 py-1 rounded text-xs font-semibold {{ $cores[$f->estado] }}">
                            {{ $f->estado }}
                        </span>
                    </td>

                    <td class="px-6 py-4 text-center">
                        <div class="inline-flex space-x-2">
                            <x-ui.button-secondary href="{{ route('ferramentas.edit', $f) }}">
                                Editar
                            </x-ui.button-secondary>

                            <form method="POST" action="{{ route('ferramentas.destroy', $f) }}">
                                @csrf @method('DELETE')
                                <x-ui.button-danger>Excluir</x-ui.button-danger>
                            </form>
                        </div>
                    </td>

                </tr>
            @endforeach

        </x-ui.table>

        <div class="mt-6">{{ $ferramentas->links() }}</div>

    </div>

</x-app-layout>
