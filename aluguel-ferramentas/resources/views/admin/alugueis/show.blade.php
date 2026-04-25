<x-app-layout>
    <x-ui.container>
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900 tracking-tight">
                        Detalhes do Aluguel
                    </h1>
                    <p class="text-sm text-gray-600 mt-1">
                        ID: <span class="font-semibold text-indigo-600">#{{ $aluguel->id }}</span>
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    @if($aluguel->status !== 'devolvido')
                        <span class="px-4 py-2 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800">
                            {{ ucfirst($aluguel->status) }}
                        </span>
                    @else
                        <span class="px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                            Devolvido
                        </span>
                    @endif
                </div>
            </div>
            <div class="h-1 bg-gradient-to-r from-indigo-500 to-blue-500 rounded-full w-20"></div>
        </div>

        {{-- Informações Principais --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-xl shadow-md p-5 border-l-4 border-indigo-500">
                <p class="text-xs font-semibold text-gray-500 uppercase">Casa de Oração</p>
                <p class="text-lg font-bold mt-2">{{ $aluguel->casa?->nome ?? '—' }}</p>
                <p class="text-xs text-gray-500 mt-1">Setor: {{ $aluguel->casa->setor->nome ?? 'N/A' }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-5 border-l-4 border-blue-500">
                <p class="text-xs font-semibold text-gray-500 uppercase">Usuário</p>
                <p class="text-lg font-bold mt-2">{{ $aluguel->usuario?->name ?? '—' }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-5 border-l-4 border-green-500">
                <p class="text-xs font-semibold text-gray-500 uppercase">Responsável</p>
                <p class="text-lg font-bold mt-2">{{ $aluguel->responsavel?->name ?? '—' }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-5 border-l-4 border-purple-500">
                <p class="text-xs font-semibold text-gray-500 uppercase">Status</p>
                @php
                    $cores = [
                        'emprestado' => 'bg-blue-100 text-blue-800',
                        'parcial' => 'bg-yellow-100 text-yellow-800',
                        'devolvido' => 'bg-green-100 text-green-800',
                        'atrasado' => 'bg-red-100 text-red-800',
                    ];
                @endphp
                <span class="inline-flex items-center mt-2 px-3 py-1 rounded-full text-sm font-semibold {{ $cores[$aluguel->status] ?? 'bg-gray-200' }}">
                    {{ ucfirst($aluguel->status) }}
                </span>
            </div>
        </div>

        {{-- Cronograma --}}
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <h2 class="text-xl font-bold mb-6">Cronograma</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase">Data de Retirada</p>
                    <p class="text-lg font-bold">{{ Carbon\Carbon::parse($aluguel->data_retirada)->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase">Previsão de Devolução</p>
                    <p class="text-lg font-bold {{ now()->gt($aluguel->data_prevista) && $aluguel->status !== 'devolvido' ? 'text-red-600' : '' }}">
                        {{ Carbon\Carbon::parse($aluguel->data_prevista)->format('d/m/Y') }}
                    </p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase">Devolução Realizada</p>
                    <p class="text-lg font-bold">
                        {{ $aluguel->data_devolucao ? Carbon\Carbon::parse($aluguel->data_devolucao)->format('d/m/Y H:i') : '—' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Itens --}}
        <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8">
            <div class="px-6 py-4 bg-gray-50 border-b">
                <h2 class="text-xl font-bold flex items-center gap-2">
                    Itens do Aluguel
                    <span class="ml-auto text-sm font-semibold bg-indigo-100 px-3 py-1 rounded-full">
                        {{ $aluguel->itens->count() }} item(s)
                    </span>
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase">Ferramenta</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase">Quantidade</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase">Observação</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($aluguel->itens as $item)
                            <tr>
                                <td class="px-6 py-4">
                                    {{ $item->ferramenta?->nome ?? 'Ferramenta Removida' }}
                                    <div class="text-xs text-gray-500">Setor: {{ $item->ferramenta->setor->nome ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">{{ $item->quantidade }}</td>
                                <td class="px-6 py-4">
                                    @php
                                        $itemColors = [
                                            'emprestado' => 'bg-blue-100 text-blue-800',
                                            'renovado' => 'bg-purple-100 text-purple-800',
                                            'parcial' => 'bg-yellow-100 text-yellow-800',
                                            'devolvido' => 'bg-green-100 text-green-800',
                                            'perdido' => 'bg-red-100 text-red-800',
                                        ];
                                    @endphp
                                    <span class="px-2 py-1 rounded text-xs font-semibold {{ $itemColors[$item->status] ?? 'bg-gray-200' }}">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">{{ $item->observacao ?? '—' }}</td>
                                <td class="px-6 py-4 text-center">
                                    @if($item->status === 'emprestado')
                                        <div class="flex gap-2 justify-center">
                                            <form action="{{ route('alugueis.item.devolver', $item) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="px-2 py-1 text-xs bg-green-600 text-white rounded hover:bg-green-700"
                                                    onclick="return confirm('Devolver esta ferramenta?')">
                                                    Devolver
                                                </button>
                                            </form>
                                            <form action="{{ route('alugueis.item.renovar', $item) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="dias" value="3">
                                                <button type="submit" class="px-2 py-1 text-xs bg-yellow-500 text-white rounded hover:bg-yellow-600">
                                                    Renovar
                                                </button>
                                            </form>
                                            <form action="{{ route('alugueis.item.perdido', $item) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="px-2 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700"
                                                    onclick="return confirm('Marcar como perdido?')">
                                                    Perdido
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-6 text-gray-500">
                                    Nenhum item registrado
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Botões de Ação --}}
        <div class="flex flex-wrap gap-4">
            @if($aluguel->status !== 'devolvido')
                <a href="{{ route('alugueis.devolver', $aluguel) }}"
                   class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow transition">
                    📦 Finalizar Devolução
                </a>
                <a href="{{ route('alugueis.edit', $aluguel) }}"
                   class="px-6 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg shadow transition">
                    ✏️ Editar Aluguel
                </a>
            @endif
            
            @if(!$aluguel->contrato)
                <a href="{{ route('contrato.gerar', $aluguel) }}"
                   class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow transition">
                    📄 Gerar Contrato
                </a>
            @else
                <a href="{{ route('contratos.show', $aluguel->contrato) }}"
                   class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow transition">
                    📑 Ver Contrato
                </a>
            @endif
            
            <a href="{{ route('alugueis.index') }}"
               class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg shadow transition">
                ← Voltar
            </a>
        </div>
    </x-ui.container>
</x-app-layout>