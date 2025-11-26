@php
    use Carbon\Carbon;

    // AGRUPAR EM CATEGORIAS
    $grupos = [
        'Atrasadas' => [],
        'Hoje'      => [],
        'Amanhã'    => [],
        'Esta Semana' => [],
        'Outras Semanas' => [],
    ];

    $hoje     = Carbon::today()->startOfDay();
    $amanha   = Carbon::tomorrow()->startOfDay();
    $semanaFim = Carbon::now()->endOfWeek(Carbon::SATURDAY)->startOfDay();

    foreach ($ferramentas as $f) {

        $uso = $f->ultimo_aluguel;
        if (!$uso) continue;

        $prevista = Carbon::parse($uso->aluguel->data_prevista)->startOfDay();

        if ($prevista->lt($hoje)) {
            $grupos['Atrasadas'][] = $f;
        } elseif ($prevista->equalTo($hoje)) {
            $grupos['Hoje'][] = $f;
        } elseif ($prevista->equalTo($amanha)) {
            $grupos['Amanhã'][] = $f;
        } elseif ($prevista->between($hoje->copy()->addDays(2), $semanaFim)) {
            $grupos['Esta Semana'][] = $f;
        } else {
            $grupos['Outras Semanas'][] = $f;
        }
    }
@endphp


{{-- LISTAGEM DINÂMICA --}}
@foreach ($grupos as $titulo => $lista)

    @if(count($lista) > 0)

        <div class="mb-10">

            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4 border-b pb-2">
                {{ $titulo }} ({{ count($lista) }})
            </h2>

            <div class="flex flex-wrap gap-4">
                @foreach ($lista as $f)
                    @php
                        $uso = $f->ultimo_aluguel;
                        $atrasado = now()->gt($uso->aluguel->data_prevista);
                    @endphp

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 border-l-4 
                        {{ $atrasado ? 'border-red-500' : 'border-yellow-500' }}
                        w-full sm:w-64 flex-shrink-0">

                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ $f->nome }}
                        </h3>

                        <p class="mt-1 text-sm">
                            Status:
                            @if($atrasado)
                                <span class="text-red-600 font-semibold">Atrasado</span>
                            @else
                                <span class="text-yellow-600 font-semibold">Emprestado</span>
                            @endif
                        </p>

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
                                {{ Carbon::parse($uso->aluguel->data_prevista)->format('d/m/Y') }}
                            </p>

                        </div>

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
