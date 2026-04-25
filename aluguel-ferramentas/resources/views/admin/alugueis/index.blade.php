<x-app-layout>
    <x-ui.container>
        <header class="mb-6 pb-4 border-b border-gray-200">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                Aluguéis de Ferramentas
            </h1>
        </header>

        <div class="bg-white shadow-lg rounded-xl p-6 lg:p-8">

            {{-- Indicador de setor (para usuário não admin) --}}
            @if(auth()->user()->role !== 'admin')
                <div class="mb-4 p-3 bg-blue-100 text-blue-800 rounded-lg">
                    <i class="fas fa-building"></i>
                    Visualizando aluguéis do setor: <strong>{{ auth()->user()->setor->nome ?? 'N/A' }}</strong>
                </div>
            @endif

            {{-- FILTRO DE BUSCA --}}
            <form method="GET" class="mb-6">
                <x-ui.filter-box>
                    <x-ui.input name="search" label="Buscar por usuário" placeholder="Nome…" />
                </x-ui.filter-box>
            </form>

            {{-- Botão --}}
            <div class="mb-6 flex justify-end">
                <x-ui.button-primary href="{{ route('alugueis.create') }}">
                    + Novo Aluguel
                </x-ui.button-primary>
            </div>

            {{-- Tabela --}}
            <x-ui.table>
                <x-slot:head>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Usuário</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Casa</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Setor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Responsável</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Retirada</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Previsto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Situação</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase">Ações</th>
                </x-slot:head>

                @foreach($alugueis as $a)
                    <tr class="hover:bg-gray-50">

                        <td class="px-6 py-4">{{ $a->usuario->name }}</td>
                        <td class="px-6 py-4">{{ $a->casa->nome }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded text-xs bg-gray-100">
                                {{ $a->casa->setor->nome ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">{{ $a->responsavel->name }}</td>
                        <td class="px-6 py-4">{{ \Carbon\Carbon::parse($a->data_retirada)->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">{{ \Carbon\Carbon::parse($a->data_prevista)->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">
                            @php
                                $cores = [
                                    'emprestado' => 'bg-blue-200 text-blue-900',
                                    'parcial' => 'bg-yellow-200 text-yellow-900',
                                    'devolvido' => 'bg-green-200 text-green-900',
                                    'atrasado' => 'bg-red-200 text-red-900',
                                ];
                                $cor = $cores[$a->status] ?? 'bg-gray-200 text-gray-800';
                            @endphp
                            <span class="px-2 py-1 text-xs font-semibold rounded {{ $cor }}">
                                {{ ucfirst($a->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center space-x-2">
                                <a href="{{ route('alugueis.show', $a) }}" 
                                   class="px-3 py-2 bg-blue-600 text-white rounded-md text-xs hover:bg-blue-700">
                                    Ver
                                </a>
                                <a href="{{ route('alugueis.edit', $a) }}" 
                                   class="px-3 py-2 bg-yellow-600 text-white rounded-md text-xs hover:bg-yellow-700">
                                    Editar
                                </a>
                                @if($a->status === 'emprestado')
                                    <form action="{{ route('alugueis.devolver.post', $a) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-3 py-2 bg-red-600 text-white rounded-md text-xs hover:bg-red-700"
                                                onclick="return confirm('Confirmar devolução?')">
                                            Devolver
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-ui.table>

            <div class="mt-6">
                {{ $alugueis->appends(request()->query())->links() }}
            </div>

        </div>
    </x-ui.container>
</x-app-layout>