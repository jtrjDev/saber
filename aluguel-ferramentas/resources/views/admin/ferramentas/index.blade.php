<x-app-layout>

    <x-ui.container>
        <header class="mb-6 pb-4 border-b border-gray-200">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                Ferramentas
            </h1>
        </header>

        <div class="bg-white shadow-lg rounded-xl p-6 lg:p-8">

            {{-- FILTROS --}}
            <form method="GET" class="mb-6">
                <x-ui.filter-box>

                    {{-- Filtro por setor - SÓ MOSTRA SE FOR ADMIN --}}
                    @if(auth()->user()->role === 'admin')
                        <x-ui.select name="setor_id" label="Setor">
                            <option value="">Todos</option>
                            @foreach($setores as $s)
                                <option value="{{ $s->id }}" @selected(request('setor_id') == $s->id)>
                                    {{ $s->nome }}
                                </option>
                            @endforeach
                        </x-ui.select>
                    @endif

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

            {{-- Indicador de setor (para usuário não admin) --}}
            @if(auth()->user()->role !== 'admin')
                <div class="mb-4 p-3 bg-blue-100 text-blue-800 rounded-lg">
                    <i class="fas fa-building"></i>
                    Visualizando ferramentas do setor: <strong>{{ auth()->user()->setor->nome ?? 'N/A' }}</strong>
                </div>
            @endif

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

                        <td class="px-6 py-4 font-medium">{{ $f->name ?? $f->nome }}  <!-- Corrigi para nome --></td>

                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded text-xs bg-gray-100">
                                {{ $f->setor->nome }}
                            </span>
                        </td>

                        <td class="px-6 py-4 capitalize">
                            @php
                                $cores = [
                                    'bom'         => 'bg-green-200 text-green-900',
                                    'ruim'        => 'bg-yellow-200 text-yellow-900',
                                    'manutenção'  => 'bg-orange-200 text-orange-900',
                                    'quebrado'    => 'bg-red-200 text-red-900',
                                ];
                            @endphp

                            <span class="px-2 py-1 rounded text-xs font-semibold {{ $cores[$f->estado] ?? 'bg-gray-200 text-gray-800' }}">
                                {{ $f->estado }}
                            </span>

                            {{-- Indicativo especial para manutenção --}}
                            @if($f->estado === 'manutenção')
                                <span class="ml-2 text-[10px] px-2 py-1 rounded-full bg-orange-500 text-white font-bold">
                                    ⚠ Em Manutenção
                                </span>
                            @endif
                         </td>

                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center space-x-2">
                                <!-- BOTÃO VER (SHOW) -->
                                <a href="{{ route('ferramentas.show', $f) }}" 
                                   class="inline-flex items-center px-3 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    Ver
                                </a>

                                <!-- BOTÃO EDITAR -->
                                <a href="{{ route('ferramentas.edit', $f) }}" 
                                   class="inline-flex items-center px-3 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-800 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Editar
                                </a>

                                <!-- BOTÃO EXCLUIR -->
                                <form method="POST" action="{{ route('ferramentas.destroy', $f) }}" class="inline">
                                    @csrf 
                                    @method('DELETE')
                                    <button type="submit" 
                                            onclick="return confirm('Tem certeza que deseja excluir {{ $f->nome }}?')"
                                            class="inline-flex items-center px-3 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Excluir
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach

            </x-ui.table>

            <div class="mt-6">
                {{ $ferramentas->appends(request()->query())->links() }}
            </div>

        </div>
    </x-ui.container>
</x-app-layout>