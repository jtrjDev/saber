<x-app-layout>

    <x-ui.container>

        {{-- ========================================================= --}}
        {{--  TÍTULO E HEADER                                          --}}
        {{-- ========================================================= --}}
        <div class="mb-12 mt-10">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-8 text-white shadow-xl">
                <h1 class="text-5xl font-extrabold mb-2 tracking-tight">
                    Dashboard Geral
                </h1>
                <p class="text-indigo-100 text-lg font-light">
                    Visão completa do uso das ferramentas, devoluções e movimentações.
                </p>
            </div>
        </div>

        {{-- ========================================================= --}}
        {{--  FILTROS — COM RESET E MAIS COMPACTO                       --}}
        {{-- ========================================================= --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg mb-12 border border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center justify-between">
                Filtros
                <a href="{{ route(Route::currentRouteName()) }}" class="text-sm text-gray-500 hover:text-red-600 transition duration-150">
                    Limpar Filtros
                </a>
            </h2>

            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">

                {{-- Filtro por Casa --}}
                <div>
                    <label for="casa" class="text-gray-600 dark:text-gray-300 text-sm font-medium">Casa de Oração</label>
                    <select name="casa" id="casa" class="w-full mt-1 rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Todas</option>
                        @foreach($totaisPorCasa as $c)
                            <option value="{{ $c->id }}" @selected($casaFiltro == $c->id)>
                                {{ $c->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filtro por Status --}}
                <div>
                    <label for="status" class="text-gray-600 dark:text-gray-300 text-sm font-medium">Status</label>
                    <select name="status" id="status" class="w-full mt-1 rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Todos</option>
                        <option value="atrasado" @selected($statusFiltro == 'atrasado')>Atrasado</option>
                        <option value="normal" @selected($statusFiltro == 'normal')>No Prazo</option>
                    </select>
                </div>

                {{-- Botão Aplicar --}}
                <div class="col-span-1">
                    <button type="submit" class="w-full px-4 py-2.5 bg-indigo-600 text-white rounded-lg shadow-md hover:bg-indigo-700 transition duration-150 font-semibold">
                        Aplicar Filtros
                    </button>
                </div>

            </form>
        </div>


        {{-- ========================================================= --}}
        {{--  CARDS DE MÉTRICAS                                        --}}
        {{-- ========================================================= --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-6 mb-12">

            {{-- Card 1: Total Ferramentas --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-5 border-l-4 border-blue-500 hover:shadow-xl transition transform hover:scale-[1.02] duration-300">
                <p class="text-gray-500 dark:text-gray-300 text-xs uppercase font-semibold">Total Cadastrado</p>
                <p class="text-3xl font-bold mt-1.5">{{ $totalFerramentas }}</p>
            </div>

            {{-- Card 2: Disponíveis --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-5 border-l-4 border-green-500 hover:shadow-xl transition transform hover:scale-[1.02] duration-300">
                <p class="text-gray-500 dark:text-gray-300 text-xs uppercase font-semibold">Disponíveis</p>
                <p class="text-3xl font-bold text-green-600 mt-1.5">{{ $disponiveis }}</p>
            </div>

            {{-- Card 3: Emprestadas --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-5 border-l-4 border-yellow-500 hover:shadow-xl transition transform hover:scale-[1.02] duration-300">
                <p class="text-gray-500 dark:text-gray-300 text-xs uppercase font-semibold">Emprestadas</p>
                <p class="text-3xl font-bold text-yellow-600 mt-1.5">{{ $emprestadas }}</p>
            </div>

            {{-- Card 4: A Devolver (48h) --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-5 border-l-4 border-orange-500 hover:shadow-xl transition transform hover:scale-[1.02] duration-300">
                <p class="text-gray-500 dark:text-gray-300 text-xs uppercase font-semibold">A Devolver (48h)</p>
                <p class="text-3xl font-bold text-orange-600 mt-1.5">{{ $devolver48 }}</p>
            </div>

            {{-- Card 5: Atrasadas --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-5 border-l-4 border-red-500 hover:shadow-xl transition transform hover:scale-[1.02] duration-300">
                <p class="text-gray-500 dark:text-gray-300 text-xs uppercase font-semibold">Atrasadas</p>
                <p class="text-3xl font-bold text-red-600 mt-1.5">{{ $atrasadas }}</p>
            </div>

            {{-- Card 6: Usuários Ativos --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-5 border-l-4 border-purple-500 hover:shadow-xl transition transform hover:scale-[1.02] duration-300">
                <p class="text-gray-500 dark:text-gray-300 text-xs uppercase font-semibold">Usuários Ativos</p>
                <p class="text-3xl font-bold text-purple-600 mt-1.5">{{ $usuariosAtivos }}</p>
            </div>

        </div>


        {{-- ========================================================= --}}
        {{--  SEÇÃO DE GRÁFICOS E TAXAS                                --}}
        {{-- ========================================================= --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">

            {{-- GRÁFICO DE PIZZA (Status) --}}
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Distribuição de Status das Ferramentas</h2>

                @php
                    $totalStatus = $disponiveis + $emprestadas + $atrasadas;
                    $percDisp = $totalStatus ? ($disponiveis / $totalStatus) * 100 : 0;
                    $percEmp  = $totalStatus ? ($emprestadas / $totalStatus) * 100 : 0;
                    $percAtr  = $totalStatus ? ($atrasadas / $totalStatus) * 100 : 0;

                    // Cálculo do SVG (mantido o original, mas melhorado o visual)
                    $angulo1 = ($percDisp / 100) * 360;
                    $angulo2 = ($percEmp  / 100) * 360;
                @endphp

                <div class="flex flex-col md:flex-row items-center justify-around">
                    <div class="relative w-48 h-48 mb-6 md:mb-0">
                        <svg viewBox="0 0 100 100" class="w-full h-full">
                            {{-- Disponíveis --}}
                            <path d="M50 50 L50 10 A40 40 0 {{ $angulo1 > 180 ? 1 : 0 }} 1
                                    {{ 50 + 40*cos(deg2rad(-90 + $angulo1)) }}
                                    {{ 50 + 40*sin(deg2rad(-90 + $angulo1)) }} Z"
                                  fill="#10b981" class="shadow-md" />

                            {{-- Emprestadas --}}
                            <path d="M50 50 L
                                    {{ 50 + 40*cos(deg2rad(-90 + $angulo1)) }}
                                    {{ 50 + 40*sin(deg2rad(-90 + $angulo1)) }}
                                    A40 40 0 {{ $angulo2 > 180 ? 1 : 0 }} 1
                                    {{ 50 + 40*cos(deg2rad(-90 + $angulo1 + $angulo2)) }}
                                    {{ 50 + 40*sin(deg2rad(-90 + $angulo1 + $angulo2)) }} Z"
                                  fill="#f59e0b" class="shadow-md" />

                            {{-- Atrasadas --}}
                            <path d="M50 50 L
                                    {{ 50 + 40*cos(deg2rad(-90 + $angulo1 + $angulo2)) }}
                                    {{ 50 + 40*sin(deg2rad(-90 + $angulo1 + $angulo2)) }}
                                    A40 40 0 1 1 50 10 Z"
                                  fill="#ef4444" class="shadow-md" />

                            <circle cx="50" cy="50" r="25" fill="white" class="dark:fill-gray-800"/>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-xl font-bold text-gray-800 dark:text-gray-200">{{ $totalStatus }}</span>
                        </div>
                    </div>

                    <div class="space-y-3 w-full md:w-auto md:ml-10">
                        <div class="flex justify-between items-center">
                            <span class="flex items-center text-gray-700 dark:text-gray-300"><span class="w-3 h-3 rounded-full bg-green-500 mr-2"></span> Disponíveis</span>
                            <strong class="font-semibold text-gray-900 dark:text-white">{{ $disponiveis }} ({{ round($percDisp) }}%)</strong>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="flex items-center text-gray-700 dark:text-gray-300"><span class="w-3 h-3 rounded-full bg-yellow-500 mr-2"></span> Emprestadas</span>
                            <strong class="font-semibold text-gray-900 dark:text-white">{{ $emprestadas }} ({{ round($percEmp) }}%)</strong>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="flex items-center text-gray-700 dark:text-gray-300"><span class="w-3 h-3 rounded-full bg-red-500 mr-2"></span> Atrasadas</span>
                            <strong class="font-semibold text-gray-900 dark:text-white">{{ $atrasadas }} ({{ round($percAtr) }}%)</strong>
                        </div>
                    </div>
                </div>
            </div>


            {{-- TAXAS (Disponibilidade / Ocupação / Atraso) --}}
            <div class="lg:col-span-1 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 flex flex-col justify-between">
                <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Taxas de Uso</h2>

                <div class="space-y-6">
                    {{-- Taxa de Disponibilidade --}}
                    <div class="p-4 bg-green-50 dark:bg-gray-700 rounded-lg border-l-4 border-green-500">
                        <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">Disponibilidade</p>
                        <p class="text-4xl font-extrabold text-green-600 mt-1">
                            {{ $totalFerramentas > 0 ? round(($disponiveis / $totalFerramentas) * 100) : 0 }}%
                        </p>
                    </div>

                    {{-- Taxa de Ocupação --}}
                    <div class="p-4 bg-yellow-50 dark:bg-gray-700 rounded-lg border-l-4 border-yellow-500">
                        <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">Ocupação</p>
                        <p class="text-4xl font-extrabold text-yellow-600 mt-1">
                            {{ $totalFerramentas > 0 ? round(($emprestadas / $totalFerramentas) * 100) : 0 }}%
                        </p>
                    </div>

                    {{-- Taxa de Atraso --}}
                    <div class="p-4 bg-red-50 dark:bg-gray-700 rounded-lg border-l-4 border-red-500">
                        <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">Atraso</p>
                        <p class="text-4xl font-extrabold {{ $atrasadas > 0 ? 'text-red-600' : 'text-green-600' }} mt-1">
                            {{ $totalFerramentas > 0 ? round(($atrasadas / $totalFerramentas) * 100) : 0 }}%
                        </p>
                    </div>
                </div>
            </div>
        </div>


        {{-- ========================================================= --}}
        {{--  GRÁFICO DE BARRAS (Comparativo)                          --}}
        {{-- ========================================================= --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-12">
            <h2 class="text-2xl font-bold mb-8 text-gray-900 dark:text-white">Comparativo de Ferramentas por Categoria</h2>
            @php $max = max($disponiveis, $emprestadas, $atrasadas, $devolver48, $usuariosAtivos); @endphp

            @php
                $barras = [
                    ['label' => 'Disponíveis', 'valor' => $disponiveis, 'cor' => 'bg-green-500', 'text_cor' => 'text-green-600'],
                    ['label' => 'Emprestadas', 'valor' => $emprestadas, 'cor' => 'bg-yellow-500', 'text_cor' => 'text-yellow-600'],
                    ['label' => 'A Devolver (48h)', 'valor' => $devolver48, 'cor' => 'bg-orange-500', 'text_cor' => 'text-orange-600'],
                    ['label' => 'Atrasadas', 'valor' => $atrasadas, 'cor' => 'bg-red-500', 'text_cor' => 'text-red-600'],
                    ['label' => 'Usuários Ativos', 'valor' => $usuariosAtivos, 'cor' => 'bg-purple-500', 'text_cor' => 'text-purple-600'],
                ];
            @endphp

            <div class="space-y-6">
                @foreach($barras as $b)
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-gray-700 dark:text-gray-300 font-medium">{{ $b['label'] }}</span>
                            <strong class="{{ $b['text_cor'] }} text-lg">{{ $b['valor'] }}</strong>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 h-3 rounded-full overflow-hidden">
                            <div class="{{ $b['cor'] }} h-3 rounded-full transition-all duration-500 ease-out"
                                 style="width: {{ $max > 0 ? ($b['valor'] / $max) * 100 : 0 }}%">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>


        {{-- ========================================================= --}}
        {{-- ATALHOS --}}
        {{-- ========================================================= --}}
        <div class="mb-12 flex flex-wrap gap-4">
            <a href="{{ route('ferramentas.create') }}" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white rounded-xl shadow-lg hover:shadow-xl hover:from-indigo-700 hover:to-indigo-800 transition-all duration-300 font-semibold flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" /></svg>
                Nova Ferramenta
            </a>
            <a href="{{ route('alugueis.create') }}" class="px-6 py-3 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-xl shadow-lg hover:shadow-xl hover:from-purple-700 hover:to-purple-800 transition-all duration-300 font-semibold flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" /></svg>
                Novo Aluguel
            </a>
            <a href="{{ route('ferramentas.index') }}" class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-white rounded-xl shadow hover:shadow-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-300 font-medium flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 5a2 2 0 012-2h8a2 2 0 012 2v10a2 2 0 01-2 2H6a2 2 0 01-2-2V5z" /></svg>
                Listar Ferramentas
            </a>
            <a href="{{ route('alugueis.index') }}" class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-white rounded-xl shadow hover:shadow-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-300 font-medium flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" /><path fill-rule="evenodd" d="M4 5a2 2 0 012-2h8a2 2 0 012 2v10a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 3a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h.01a1 1 0 100-2H10zm3 0a1 1 0 000 2h.01a1 1 0 100-2H13zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H10z" clip-rule="evenodd" /></svg>
                Listar Aluguéis
            </a>
        </div>


        {{-- ========================================================= --}}
        {{--  PAINEL DE DEVOLUÇÕES — (7 dias + atrasados)               --}}
        {{-- ========================================================= --}}
        {{-- Mantido o include original, assumindo que o componente _cards-devolucoes está otimizado --}}
        @include('admin.almoxarifado._cards-devolucoes', ['ferramentas' => $ferramentas])


    </x-ui.container>
</x-app-layout>