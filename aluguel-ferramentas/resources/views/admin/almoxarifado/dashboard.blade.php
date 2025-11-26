<x-app-layout>

    <x-ui.container>

        @php
            // ================================
            // AGRUPAMENTO POR DATA DE DEVOLUÇÃO
            // ================================
            $ferramentasAgrupadas = [
                'atrasados'     => [],
                'hoje'          => [],
                'amanha'        => [],
                'esta_semana'   => [],
                'outras'        => [],
            ];

            foreach ($ferramentas as $ferramenta) {

                $uso = $ferramenta->ultimo_aluguel;
                if (!$uso) continue; // só mostra alugadas

                // Datas
                $dataPrevista   = \Carbon\Carbon::parse($uso->aluguel->data_prevista)->startOfDay();
                $hoje           = \Carbon\Carbon::today('America/Sao_Paulo');
                $amanha         = $hoje->copy()->addDay();
                $depoisDeAmanha = $hoje->copy()->addDays(2);
                $fimDaSemana    = $hoje->copy()->endOfWeek(\Carbon\Carbon::SATURDAY)->startOfDay();

                // 🟥 ATRASADOS
                if ($dataPrevista->lt($hoje)) {
                    $ferramentasAgrupadas['atrasados'][] = $ferramenta;
                    continue;
                }

                // 🟨 HOJE
                if ($dataPrevista->equalTo($hoje)) {
                    $ferramentasAgrupadas['hoje'][] = $ferramenta;
                    continue;
                }

                // 🟧 AMANHÃ
                if ($dataPrevista->equalTo($amanha)) {
                    $ferramentasAgrupadas['amanha'][] = $ferramenta;
                    continue;
                }

                // 🔵 ESTA SEMANA (De 2 dias à frente até sábado)
                if ($dataPrevista->between($depoisDeAmanha, $fimDaSemana)) {
                    $ferramentasAgrupadas['esta_semana'][] = $ferramenta;
                    continue;
                }

                // ⚪ OUTRAS SEMANAS
                $ferramentasAgrupadas['outras'][] = $ferramenta;
            }
        @endphp


        {{-- TÍTULO PRINCIPAL --}}
        <div class="mb-10">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white">
                Painel do Almoxarifado
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">
                Ferramentas emprestadas organizadas por data de devolução.
            </p>
        </div>


        {{-- LISTAGEM POR CATEGORIAS --}}
        @foreach($ferramentasAgrupadas as $categoria => $lista)

            @if(count($lista) > 0)

                {{-- TÍTULO DE CADA CATEGORIA --}}
                @php
                    switch ($categoria) {
                        case 'atrasados':
                            $titulo = '⚠️ Ferramentas ATRASADAS';
                            $corTitulo = 'text-red-600';
                            break;

                        case 'hoje':
                            $titulo = '✔️ Ferramentas para devolver HOJE';
                            $corTitulo = 'text-indigo-600';
                            break;

                        case 'amanha':
                            $titulo = '⏳ Ferramentas para devolver AMANHÃ';
                            $corTitulo = 'text-blue-600';
                            break;

                        case 'esta_semana':
                            $titulo = '📅 Devoluções desta SEMANA';
                            $corTitulo = 'text-yellow-600';
                            break;

                        case 'outras':
                            $titulo = '🔮 Devoluções FUTURAS';
                            $corTitulo = 'text-gray-700';
                            break;
                    }
                @endphp


                <div class="mb-10">
                    <h2 class="text-2xl font-bold mb-4 border-b pb-2 {{ $corTitulo }}">
                        {{ $titulo }} <span class="text-gray-500">({{ count($lista) }})</span>
                    </h2>


                    <div class="flex flex-wrap gap-4">

                        @foreach($lista as $f)
                            @php
                                $uso = $f->ultimo_aluguel;
                                $atrasado = now()->gt($uso->aluguel->data_prevista);
                                $cardColor = $atrasado ? 'border-red-500' : 'border-yellow-500';
                            @endphp

                            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 border-l-4 {{ $cardColor }} w-full sm:w-64 flex-shrink-0">

                                {{-- NOME --}}
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                                    {{ $f->nome }}
                                </h3>

                                {{-- STATUS --}}
                                <p class="mt-1 text-sm">
                                    Status:
                                    @if($atrasado)
                                        <span class="text-red-600 font-semibold">Atrasado</span>
                                    @else
                                        <span class="text-yellow-600 font-semibold">Emprestado</span>
                                    @endif
                                </p>


                                {{-- DETALHES DO ALUGUEL --}}
                                <div class="mt-4 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50">

                                    <p class="text-sm text-gray-700 dark:text-gray-300">
                                        <strong>Com:</strong> {{ $uso->aluguel->usuario->name }}
                                    </p>

                                    <p class="text-sm text-gray-700 dark:text-gray-300">
                                        <strong>Casa:</strong> {{ $uso->aluguel->casa->nome }}
                                    </p>

                                    <p class="text-sm text-gray-700 dark:text-gray-300">
                                        <strong>Previsto para:</strong>
                                        {{ \Carbon\Carbon::parse($uso->aluguel->data_prevista)->format('d/m/Y') }}
                                    </p>

                                </div>


                                {{-- BOTÃO --}}
                                <a 
                                    href="{{ route('alugueis.show', $uso->aluguel) }}"
                                    class="mt-4 inline-block px-4 py-2 bg-indigo-600 text-white rounded shadow hover:bg-indigo-700 transition"
                                >
                                    Ver Detalhes
                                </a>

                            </div>
                        @endforeach

                    </div>
                </div>

            @endif

        @endforeach

    </x-ui.container>

</x-app-layout>
