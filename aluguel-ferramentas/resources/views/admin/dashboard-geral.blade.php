
<x-app-layout>

<x-ui.container>

            {{-- ========================================================= --}}
        {{--  TÍTULO                                                   --}}
        {{-- ========================================================= --}}
        <div class="mb-12 mt-10">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-8 text-white shadow-lg">
                <h1 class="text-5xl font-bold mb-2">
                    Dashboard Geral
                </h1>
                <p class="text-indigo-100 text-lg">
                    Visão completa do uso das ferramentas, devoluções e movimentações.
                </p>
            </div>
        </div>

        {{-- ========================================================= --}}
        {{--  FILTROS — NO TOPO (Opção A)                           --}}
        {{-- ========================================================= --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow mb-12 border border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Filtros</h2>

            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- Filtro por Casa --}}
                <div>
                    <label class="text-gray-600 dark:text-gray-300 text-sm">Casa de Oração</label>
                    <select name="casa" class="w-full mt-1 rounded-lg dark:bg-gray-700 dark:text-white">
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
                    <label class="text-gray-600 dark:text-gray-300 text-sm">Status</label>
                    <select name="status" class="w-full mt-1 rounded-lg dark:bg-gray-700 dark:text-white">
                        <option value="">Todos</option>
                        <option value="atrasado" @selected($statusFiltro == 'atrasado')>Atrasado</option>
                        <option value="normal" @selected($statusFiltro == 'normal')>No Prazo</option>
                    </select>
                </div>

                {{-- Botão --}}
                <div class="flex items-end">
                    <button class="px-5 py-3 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700 w-full">
                        Aplicar Filtros
                    </button>
                </div>

            </form>
        </div>




        {{-- ========================================================= --}}
        {{--  CARDS DE MÉTRICAS                                        --}}
        {{-- ========================================================= --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">

            {{-- Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border-l-4 border-blue-500 hover:shadow-xl transition transform hover:scale-105">
                <p class="text-gray-500 dark:text-gray-300 text-sm">Ferramentas Cadastradas</p>
                <p class="text-4xl font-bold mt-2">{{ $totalFerramentas }}</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border-l-4 border-green-500 hover:shadow-xl transition transform hover:scale-105">
                <p class="text-gray-500 dark:text-gray-300 text-sm">Disponíveis</p>
                <p class="text-4xl font-bold text-green-600 mt-2">{{ $disponiveis }}</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border-l-4 border-yellow-500 hover:shadow-xl transition transform hover:scale-105">
                <p class="text-gray-500 dark:text-gray-300 text-sm">Emprestadas</p>
                <p class="text-4xl font-bold text-yellow-600 mt-2">{{ $emprestadas }}</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border-l-4 border-orange-500 hover:shadow-xl transition transform hover:scale-105">
                <p class="text-gray-500 dark:text-gray-300 text-sm">A Devolver (48h)</p>
                <p class="text-4xl font-bold text-orange-600 mt-2">{{ $devolver48 }}</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border-l-4 border-red-500 hover:shadow-xl transition transform hover:scale-105">
                <p class="text-gray-500 dark:text-gray-300 text-sm">Atrasadas</p>
                <p class="text-4xl font-bold text-red-600 mt-2">{{ $atrasadas }}</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border-l-4 border-purple-500 hover:shadow-xl transition transform hover:scale-105">
                <p class="text-gray-500 dark:text-gray-300 text-sm">Usuários Ativos</p>
                <p class="text-4xl font-bold text-purple-600 mt-2">{{ $usuariosAtivos }}</p>
            </div>

        </div>



        {{-- ========================================================= --}}
        {{--  SEÇÃO DE GRÁFICOS                                        --}}
        {{-- ========================================================= --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">

            {{-- GRÁFICO DE PIZZA --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h2 class="text-2xl font-bold mb-6">Distribuição de Status</h2>

                @php
                    $totalStatus = $disponiveis + $emprestadas + $atrasadas;
                    $percDisp = $totalStatus ? ($disponiveis / $totalStatus) * 100 : 0;
                    $percEmp  = $totalStatus ? ($emprestadas / $totalStatus) * 100 : 0;
                    $percAtr  = $totalStatus ? ($atrasadas / $totalStatus) * 100 : 0;

                    $angulo1 = ($percDisp / 100) * 360;
                    $angulo2 = ($percEmp  / 100) * 360;
                @endphp

                <div class="flex items-center justify-center">
                    <svg viewBox="0 0 100 100" class="w-64 h-64">
                        {{-- Disponíveis --}}
                        <path d="M50 50 L50 10 A40 40 0 {{ $angulo1 > 180 ? 1 : 0 }} 1
                                {{ 50 + 40*cos(deg2rad(-90 + $angulo1)) }}
                                {{ 50 + 40*sin(deg2rad(-90 + $angulo1)) }} Z"
                              fill="#10b981" opacity="0.85" />

                        {{-- Emprestadas --}}
                        <path d="M50 50 L
                                {{ 50 + 40*cos(deg2rad(-90 + $angulo1)) }}
                                {{ 50 + 40*sin(deg2rad(-90 + $angulo1)) }}
                                A40 40 0 {{ $angulo2 > 180 ? 1 : 0 }} 1
                                {{ 50 + 40*cos(deg2rad(-90 + $angulo1 + $angulo2)) }}
                                {{ 50 + 40*sin(deg2rad(-90 + $angulo1 + $angulo2)) }} Z"
                              fill="#f59e0b" opacity="0.85" />

                        {{-- Atrasadas --}}
                        <path d="M50 50 L
                                {{ 50 + 40*cos(deg2rad(-90 + $angulo1 + $angulo2)) }}
                                {{ 50 + 40*sin(deg2rad(-90 + $angulo1 + $angulo2)) }}
                                A40 40 0 1 1 50 10 Z"
                              fill="#ef4444" opacity="0.85" />

                        <circle cx="50" cy="50" r="25" fill="white" class="dark:fill-gray-800"/>
                    </svg>
                </div>

                <div class="mt-6 space-y-3">
                    <p class="flex justify-between"><span>Disponíveis</span> <strong>{{ $disponiveis }} ({{ round($percDisp) }}%)</strong></p>
                    <p class="flex justify-between"><span>Emprestadas</span> <strong>{{ $emprestadas }} ({{ round($percEmp) }}%)</strong></p>
                    <p class="flex justify-between"><span>Atrasadas</span> <strong>{{ $atrasadas }} ({{ round($percAtr) }}%)</strong></p>
                </div>
            </div>



            {{-- GRÁFICO DE BARRAS --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h2 class="text-2xl font-bold mb-6">Comparativo de Ferramentas</h2>
                @php $max = max($disponiveis, $emprestadas, $atrasadas, $devolver48, $usuariosAtivos); @endphp

                @php
                    $barras = [
                        ['label' => 'Disponíveis', 'valor' => $disponiveis, 'cor' => 'bg-green-500'],
                        ['label' => 'Emprestadas', 'valor' => $emprestadas, 'cor' => 'bg-yellow-500'],
                        ['label' => 'A Devolver (48h)', 'valor' => $devolver48, 'cor' => 'bg-orange-500'],
                        ['label' => 'Atrasadas', 'valor' => $atrasadas, 'cor' => 'bg-red-500'],
                        ['label' => 'Usuários Ativos', 'valor' => $usuariosAtivos, 'cor' => 'bg-purple-500'],
                    ];
                @endphp

                @foreach($barras as $b)
                    <div class="mb-6">
                        <div class="flex justify-between mb-1">
                            <span>{{ $b['label'] }}</span>
                            <strong>{{ $b['valor'] }}</strong>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 h-3 rounded-full">
                            <div class="{{ $b['cor'] }} h-3 rounded-full"
                                 style="width: {{ $max ? ($b['valor'] / $max) * 100 : 0 }}%">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>



        {{-- ========================================================= --}}
        {{--  TAXAS (Disponibilidade / Ocupação / Atraso)               --}}
        {{-- ========================================================= --}}
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-gray-800 dark:to-gray-700 rounded-xl shadow-lg p-8 mb-12 border-l-4 border-green-500">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">

                <div>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-1">Taxa de Disponibilidade</p>
                    <p class="text-4xl font-bold text-green-600">
                        {{ round(($disponiveis / $totalFerramentas) * 100) }}%
                    </p>
                </div>

                <div>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-1">Taxa de Ocupação</p>
                    <p class="text-4xl font-bold text-yellow-600">
                        {{ round(($emprestadas / $totalFerramentas) * 100) }}%
                    </p>
                </div>

                <div>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-1">Taxa de Atraso</p>
                    <p class="text-4xl font-bold {{ $atrasadas > 0 ? 'text-red-600' : 'text-green-600' }}">
                        {{ round(($atrasadas / $totalFerramentas) * 100) }}%
                    </p>
                </div>

            </div>
        </div>
 {{-- ATALHOS --}}
        <div class="mb-12 flex flex-wrap gap-3">
            <a href="{{ route('ferramentas.create') }}" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white rounded-lg shadow-lg hover:shadow-xl hover:from-indigo-700 hover:to-indigo-800 transition-all duration-300 font-medium flex items-center gap-2">
                <span>+</span> Nova Ferramenta
            </a>
            <a href="{{ route('alugueis.create') }}" class="px-6 py-3 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-lg shadow-lg hover:shadow-xl hover:from-purple-700 hover:to-purple-800 transition-all duration-300 font-medium flex items-center gap-2">
                <span>+</span> Novo Aluguel
            </a>
            <a href="{{ route('ferramentas.index') }}" class="px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white rounded-lg shadow hover:shadow-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-all duration-300 font-medium">
                📋 Listar Ferramentas
            </a>
            <a href="{{ route('alugueis.index') }}" class="px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white rounded-lg shadow hover:shadow-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-all duration-300 font-medium">
                📦 Listar Aluguéis
            </a>
        </div>



        {{-- ========================================================= --}}
        {{--  PAINEL DE DEVOLUÇÕES — (7 dias + atrasados)               --}}
        {{-- ========================================================= --}}
        @include('admin.almoxarifado._cards-devolucoes', ['ferramentas' => $ferramentas])


    </x-ui.container>
</x-app-layout>
