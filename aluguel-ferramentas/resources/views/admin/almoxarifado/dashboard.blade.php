<x-app-layout>

    <x-ui.container>

        {{-- TÍTULO --}}
        <div class="mb-10">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white">
                Painel do Almoxarifado
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">
                Visão geral de todas as ferramentas, status e empréstimos.
            </p>
        </div>

        {{-- CARDS DE ESTATÍSTICAS --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-10">

            <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow border-l-4 border-green-600">
                <p class="text-sm text-gray-500">Disponíveis</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">
                    {{ $stats['disponiveis'] }}
                </p>
            </div>

            <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow border-l-4 border-blue-600">
                <p class="text-sm text-gray-500">Emprestadas</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">
                    {{ $stats['emprestadas'] }}
                </p>
            </div>

            <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow border-l-4 border-red-600">
                <p class="text-sm text-gray-500">Atrasadas</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">
                    {{ $stats['atrasadas'] }}
                </p>
            </div>

        </div>

        {{-- LISTA DE FERRAMENTAS --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            @foreach($ferramentas as $f)

                @php
                    $uso = $f->ultimo_aluguel;
                    $cardColor = $uso
                        ? (now()->gt($uso->aluguel->data_prevista) ? 'border-red-500' : 'border-yellow-500')
                        : 'border-green-500';
                @endphp

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 border-l-4 {{ $cardColor }}">

                    {{-- NOME --}}
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ $f->nome }}
                    </h3>

                    {{-- STATUS --}}
                    <p class="mt-1 text-sm">
                        Status:
                        @if(!$uso)
                            <span class="text-green-600 font-semibold">Disponível</span>
                        @elseif(now()->gt($uso->aluguel->data_prevista))
                            <span class="text-red-600 font-semibold">Atrasado</span>
                        @else
                            <span class="text-yellow-600 font-semibold">Emprestado</span>
                        @endif
                    </p>

                    {{-- SE ESTIVER EM USO --}}
                    @if($uso)
                        <div class="mt-4 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50">

                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                <strong>Com:</strong>
                                {{ $uso->aluguel->usuario->name }}
                            </p>

                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                <strong>Casa:</strong>
                                {{ $uso->aluguel->casa->nome }}
                            </p>

                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                <strong>Previsto para:</strong>
                                {{ \Carbon\Carbon::parse($uso->aluguel->data_prevista)->format('d/m/Y') }}
                            </p>

                        </div>

                        <a 
                            href="{{ route('alugueis.show', $uso->aluguel) }}"
                            class="mt-4 inline-block px-4 py-2 bg-indigo-600 text-white rounded shadow hover:bg-indigo-700 transition"
                        >
                            Ver Detalhes
                        </a>
                    @endif

                </div>

            @endforeach

        </div>

    </x-ui.container>

</x-app-layout>
