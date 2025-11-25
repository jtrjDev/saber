<x-app-layout>

    <x-ui.container>

        {{-- Cabeçalho --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    Contrato {{ $contrato->numero }}
                </h1>
                <p class="text-gray-600">
                    Gerado para o aluguel 
                    <span class="font-semibold text-indigo-600">#{{ $contrato->aluguel_id }}</span>
                </p>
            </div>

            {{-- Status --}}
            <span class="px-4 py-2 rounded-full bg-blue-100 text-blue-800 font-semibold text-sm">
                Contrato Gerado
            </span>
        </div>

        {{-- Card principal --}}
        <div class="bg-white rounded-xl shadow p-6 mb-10">

            {{-- Usuário / Casa / Data --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <div>
                    <p class="text-sm text-gray-500">Usuário</p>
                    <p class="text-lg font-semibold">{{ $contrato->aluguel->usuario->name }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Casa de Oração</p>
                    <p class="text-lg font-semibold">{{ $contrato->aluguel->casa->nome }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Assinado em</p>
                    <p class="text-lg font-semibold">
                        {{ $contrato->data_assinatura ? \Carbon\Carbon::parse($contrato->data_assinatura)->format('d/m/Y') : '—' }}
                    </p>
                </div>

            </div>

            <hr class="my-6">

            {{-- Itens --}}
            <h2 class="text-xl font-bold mb-4">Itens do Contrato</h2>

            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-sm font-medium">Ferramenta</th>
                        <th class="px-4 py-2 text-center text-sm font-medium">Quantidade</th>
                        <th class="px-4 py-2 text-left text-sm font-medium">Observação</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($contrato->itens as $item)
                        <tr>
                            <td class="px-4 py-2">{{ $item->nome_ferramenta }}</td>
                            <td class="px-4 py-2 text-center">{{ $item->quantidade }}</td>
                            <td class="px-4 py-2">{{ $item->observacao ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Link para download --}}
            <div class="mt-8">
                <a href="{{ asset('storage/'.$contrato->arquivo_pdf) }}"
                   target="_blank"
                   class="inline-flex items-center px-5 py-2 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700 transition">
                    📄 Baixar PDF
                </a>
            </div>

        </div>


        {{-- Histórico --}}
        <div class="bg-white rounded-xl shadow p-6">

            <h2 class="text-xl font-bold mb-4">Histórico do Contrato</h2>

            @forelse($contrato->historicos as $h)
                <div class="mb-4 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <p class="font-semibold">{{ $h->acao }}</p>
                    <p class="text-sm text-gray-600">{{ $h->detalhes }}</p>
                    <p class="text-xs text-gray-400 mt-1">
                        {{ \Carbon\Carbon::parse($h->created_at)->format('d/m/Y H:i') }}
                    </p>
                </div>
            @empty
                <p class="text-gray-500 text-sm">Nenhuma ação registrada.</p>
            @endforelse

        </div>

        <div class="mt-8">
            <x-ui.button-secondary href="{{ route('alugueis.show', $contrato->aluguel_id) }}">
                Voltar ao Aluguel
            </x-ui.button-secondary>
        </div>

    </x-ui.container>

</x-app-layout>
