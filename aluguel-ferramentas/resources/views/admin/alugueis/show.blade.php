<x-app-layout>

    <x-ui.container>

        {{-- Cabeçalho com Breadcrumb --}}
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900 dark:text-white tracking-tight">
                        Detalhes do Aluguel
                    </h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        ID: <span class="font-semibold text-indigo-600 dark:text-indigo-400">#{{ $aluguel->id }}</span>
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="px-4 py-2 rounded-full text-sm font-semibold
                        {{ $aluguel->status === 'ativo' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' : 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' }}">
                        {{ ucfirst($aluguel->status) }}
                    </span>
                </div>
            </div>
            <div class="h-1 bg-gradient-to-r from-indigo-500 to-blue-500 rounded-full w-20"></div>
        </div>

        {{-- Seção de Informações Principais em Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-5 border-l-4 border-indigo-500">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Casa de Oração</p>
                <p class="text-lg font-bold mt-2">{{ $aluguel->casa?->nome ?? '—' }}</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-5 border-l-4 border-blue-500">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Membro / Usuário</p>
                <p class="text-lg font-bold mt-2">{{ $aluguel->usuario?->name ?? '—' }}</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-5 border-l-4 border-green-500">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Responsável</p>
                <p class="text-lg font-bold mt-2">{{ $aluguel->responsavel?->name ?? '—' }}</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-5 border-l-4 border-purple-500">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Status</p>
               @php
                    $st = $aluguel->status;

                    $cores = [
                        'emprestado' => 'bg-blue-100 text-blue-800',
                        'parcial'    => 'bg-yellow-100 text-yellow-800',
                        'devolvido'  => 'bg-green-100 text-green-800',
                        'atrasado'   => 'bg-red-100 text-red-800',
                    ];
                @endphp

                <span class="inline-flex items-center mt-2 px-3 py-1 rounded-full text-sm font-semibold {{ $cores[$st] ?? 'bg-gray-200 text-gray-700' }}">
                    {{ ucfirst($st) }}
                </span>
            </div>

        </div>

        {{-- Cronograma --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-8">
            <h2 class="text-xl font-bold mb-6">Cronograma de Datas</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- Data de Retirada --}}
                <div>
                    <p class="text-xs font-semibold uppercase">Data de Retirada</p>
                    <p class="text-lg font-bold">
                        {{ \Carbon\Carbon::parse($aluguel->data_retirada)->format('d/m/Y') }}
                    </p>
                </div>

                {{-- Data Prevista --}}
                <div>
                    <p class="text-xs font-semibold uppercase">Data Prevista</p>
                    <p class="text-lg font-bold">
                        {{ \Carbon\Carbon::parse($aluguel->data_prevista)->format('d/m/Y') }}
                    </p>
                </div>

                {{-- Data de Devolução --}}
                <div>
                    <p class="text-xs font-semibold uppercase">Data de Devolução</p>
                    <p class="text-lg font-bold">
                        {{ $aluguel->data_devolucao ? \Carbon\Carbon::parse($aluguel->data_devolucao)->format('d/m/Y') : '—' }}
                    </p>
                </div>

            </div>
        </div>

        {{-- Itens --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden mb-8">
            <div class="px-6 py-4 border-b">
                <h2 class="text-xl font-bold flex items-center gap-2">
                    Itens do Aluguel
                    <span class="ml-auto text-sm font-semibold bg-indigo-100 px-3 py-1 rounded-full">
                        {{ $aluguel->itens->count() }} item(ns)
                    </span>
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase">Ferramenta</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase">Quantidade</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase">Observação</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">

                        @forelse($aluguel->itens as $item)
                            <tr>
                                <td class="px-6 py-4">
                                    {{ $item->ferramenta?->nome ?? 'Ferramenta Apagada' }}
                                </td>
                                <td class="px-6 py-4 text-center">{{ $item->quantidade }}</td>
                                <td class="px-6 py-4">{{ $item->observacao ?? '—' }}</td>
                                <td class="px-6 py-4">
    {{ $item->ferramenta?->nome }}

    @if($item->status === 'emprestado')
        <div class="mt-2 flex gap-2">

            {{-- DEVOLVER --}}
            <form action="{{ route('alugueis.item.devolver', $item) }}"

                method="POST">
                @csrf
                <button class="px-3 py-1 text-xs bg-green-600 text-white rounded">
                    Devolver
                </button>
            </form>

            {{-- RENOVAR --}}
            <form action="{{ route('alugueis.item.renovar', $item) }}"
                method="POST">
                @csrf
                <input type="hidden" name="dias" value="3">
                <button class="px-3 py-1 text-xs bg-yellow-500 text-white rounded">
                    Renovar
                </button>
            </form>

            {{-- PERDIDO --}}
            <form action="{{ route('alugueis.item.perdido', $item) }}"
                method="POST">
                @csrf
                <button class="px-3 py-1 text-xs bg-red-600 text-white rounded">
                    Perdido
                </button>
            </form>

        </div>
    @endif
</td>

<td>
    <span class="px-2 py-1 text-xs rounded bg-gray-200">
        {{ $item->status }}
    </span>
</td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-6 text-gray-600">Nenhum item registrado</td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>
        </div>
{{-- BOTÕES DO CONTRATO --}}
<div class="mt-10 flex items-center gap-4">

    @if(!$aluguel->contrato)
        {{-- BOTÃO: Gerar Contrato --}}
        <a href="{{ route('contrato.gerar', $aluguel) }}"
           class="inline-flex items-center px-4 py-2 bg-indigo-600 
                  text-white rounded-lg shadow hover:bg-indigo-700 transition">
            📄 Gerar Contrato
        </a>
    @else
        {{-- BOTÃO: Ver Contrato --}}
        <a href="{{ route('contratos.show', $aluguel->contrato) }}"
           class="inline-flex items-center px-4 py-2 bg-green-600 
                  text-white rounded-lg shadow hover:bg-green-700 transition">
            📑 Ver Contrato
        </a>

        {{-- BOTÃO: Baixar PDF --}}
        <a href="{{ asset('storage/'.$aluguel->contrato->arquivo_pdf) }}"
           target="_blank"
           class="inline-flex items-center px-4 py-2 bg-blue-600 
                  text-white rounded-lg shadow hover:bg-blue-700 transition">
            ⬇️ Baixar PDF
        </a>
    @endif

</div>

        {{-- Botão Voltar --}}
        <div class="mt-8 flex items-center gap-4">
            <x-ui.button-secondary href="{{ route('alugueis.index') }}">
                Voltar
            </x-ui.button-secondary>
        </div>

    </x-ui.container>

</x-app-layout>
