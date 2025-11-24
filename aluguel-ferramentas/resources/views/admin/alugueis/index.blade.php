<x-app-layout>

    <header class="mb-6 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
            Aluguéis de Ferramentas
        </h1>
    </header>

    <div class="bg-white shadow-lg rounded-xl p-6 lg:p-8">

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
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Responsável</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Retirada</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Previsto</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Situação</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase">Ações</th>
            </x-slot:head>

            @foreach($alugueis as $a)
                <tr class="hover:bg-gray-50">

                    {{-- Usuário --}}
                    <td class="px-6 py-4">{{ $a->usuario->name }}</td>

                    {{-- Casa --}}
                    <td class="px-6 py-4">{{ $a->casa->nome }}</td>

                    {{-- Responsável --}}
                    <td class="px-6 py-4">{{ $a->responsavel->name }}</td>

                    {{-- Retirada --}}
                    <td class="px-6 py-4">
                        {{ \Carbon\Carbon::parse($a->data_retirada)->format('d/m/Y') }}
                    </td>

                    {{-- Devolução Prevista --}}
                    <td class="px-6 py-4">
                        {{ \Carbon\Carbon::parse($a->data_devolucao_prevista)->format('d/m/Y') }}
                    </td>

                    {{-- Situação --}}
                    <td class="px-6 py-4">
                        @php
                            $cores = [
                                'ativo' => 'bg-blue-200 text-blue-900',
                                'devolvido' => 'bg-green-200 text-green-900',
                                'atrasado' => 'bg-red-200 text-red-900',
                            ];
                        @endphp

                        <span class="px-2 py-1 text-xs font-semibold rounded {{ $cores[$a->status] }}">
                            {{ ucfirst($a->status) }}
                        </span>
                    </td>

                    {{-- Ações --}}
                    <td class="px-6 py-4 text-center">

                        <div class="inline-flex space-x-2">

                            {{-- Ver Detalhes --}}
                            <x-ui.button-secondary 
                                href="{{ route('alugueis.show', $a) }}">
                                Ver
                            </x-ui.button-secondary>

                            {{-- Devolver --}}
                            @if($a->status === 'ativo')
                                <form action="{{ route('alugueis.devolver', $a) }}" method="POST">
                                    @csrf
                                    <x-ui.button-danger>
                                        Devolver
                                    </x-ui.button-danger>
                                </form>
                            @endif

                        </div>

                    </td>

                </tr>
            @endforeach

        </x-ui.table>

        <div class="mt-6">
            {{ $alugueis->links() }}
        </div>

    </div>

</x-app-layout>
