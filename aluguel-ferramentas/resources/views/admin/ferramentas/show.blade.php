{{-- resources/views/admin/ferramentas/show.blade.php --}}

<x-app-layout>
    <x-ui.container>
        <header class="mb-6 pb-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                    Detalhes da Ferramenta
                </h1>
                <div class="space-x-2">
                    <a href="{{ route('ferramentas.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        ← Voltar
                    </a>
                    <a href="{{ route('ferramentas.edit', $ferramenta) }}" 
                       class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700">
                        Editar
                    </a>
                </div>
            </div>
        </header>

        <div class="bg-white shadow-lg rounded-xl overflow-hidden">
            <!-- Status atual da ferramenta -->
            @php
                $emprestimoAtivo = $ferramenta->itens
                    ->whereIn('status', ['emprestado', 'renovado', 'parcial'])
                    ->first();
            @endphp

            @if($emprestimoAtivo)
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 m-6 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                <strong>Ferramenta emprestada!</strong><br>
                                Pegue por: {{ $emprestimoAtivo->aluguel->usuario->name ?? 'N/A' }}<br>
                                Casa: {{ $emprestimoAtivo->aluguel->casa->nome ?? 'N/A' }}<br>
                                Previsão de devolução: {{ \Carbon\Carbon::parse($emprestimoAtivo->aluguel->data_prevista)->format('d/m/Y') }}
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-green-50 border-l-4 border-green-400 p-4 m-6 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">
                                <strong>Ferramenta disponível!</strong> Pronta para empréstimo.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="p-6 lg:p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Informações básicas -->
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Informações da Ferramenta</h2>
                        
                        <dl class="space-y-3">
                            <div class="grid grid-cols-3 gap-4">
                                <dt class="text-sm font-medium text-gray-500">Nome:</dt>
                                <dd class="text-sm text-gray-900 col-span-2">{{ $ferramenta->nome }}</dd>
                            </div>
                            
                            <div class="grid grid-cols-3 gap-4">
                                <dt class="text-sm font-medium text-gray-500">Setor:</dt>
                                <dd class="text-sm text-gray-900 col-span-2">
                                    <span class="px-2 py-1 rounded text-xs bg-blue-100 text-blue-800">
                                        {{ $ferramenta->setor->nome }}
                                    </span>
                                </dd>
                            </div>
                            
                            <div class="grid grid-cols-3 gap-4">
                                <dt class="text-sm font-medium text-gray-500">Estado:</dt>
                                <dd class="text-sm text-gray-900 col-span-2">
                                    @php
                                        $cores = [
                                            'bom' => 'bg-green-100 text-green-800',
                                            'ruim' => 'bg-yellow-100 text-yellow-800',
                                            'manutenção' => 'bg-orange-100 text-orange-800',
                                            'quebrado' => 'bg-red-100 text-red-800',
                                        ];
                                    @endphp
                                    <span class="px-2 py-1 rounded text-xs font-semibold {{ $cores[$ferramenta->estado] ?? 'bg-gray-100' }}">
                                        {{ ucfirst($ferramenta->estado) }}
                                    </span>
                                </dd>
                            </div>
                            
                            @if($ferramenta->valor_compra)
                            <div class="grid grid-cols-3 gap-4">
                                <dt class="text-sm font-medium text-gray-500">Valor de Compra:</dt>
                                <dd class="text-sm text-gray-900 col-span-2">
                                    R$ {{ number_format($ferramenta->valor_compra, 2, ',', '.') }}
                                </dd>
                            </div>
                            @endif
                            
                            <div class="grid grid-cols-3 gap-4">
                                <dt class="text-sm font-medium text-gray-500">Data Cadastro:</dt>
                                <dd class="text-sm text-gray-900 col-span-2">
                                    {{ $ferramenta->created_at->format('d/m/Y H:i') }}
                                </dd>
                            </div>
                            
                            @if($ferramenta->descricao)
                            <div class="grid grid-cols-3 gap-4">
                                <dt class="text-sm font-medium text-gray-500">Descrição:</dt>
                                <dd class="text-sm text-gray-900 col-span-2">
                                    {{ $ferramenta->descricao }}
                                </dd>
                            </div>
                            @endif
                        </dl>
                    </div>

                    <!-- Foto -->
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Foto</h2>
                        <div class="bg-gray-50 rounded-lg p-4 text-center">
                            @if($ferramenta->foto)
                                <img src="{{ asset('storage/'.$ferramenta->foto) }}" 
                                     class="max-w-full h-auto rounded-lg shadow-md mx-auto"
                                     style="max-height: 300px;"
                                     alt="{{ $ferramenta->nome }}">
                            @else
                                <div class="text-gray-400 py-8">
                                    <svg class="mx-auto h-16 w-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <p class="mt-2">Nenhuma foto cadastrada</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Histórico de empréstimos -->
                <div class="mt-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Histórico de Empréstimos</h2>
                    
                    @if(isset($historico) && $historico->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuário</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Casa</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Previsão</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Devolução</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($historico as $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ $item->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ $item->aluguel->usuario->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ $item->aluguel->casa->nome ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ \Carbon\Carbon::parse($item->aluguel->data_prevista)->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            @if($item->aluguel->data_devolucao)
                                                {{ \Carbon\Carbon::parse($item->aluguel->data_devolucao)->format('d/m/Y H:i') }}
                                            @else
                                                <span class="text-yellow-600">Em andamento</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @php
                                                $statusColors = [
                                                    'emprestado' => 'bg-blue-100 text-blue-800',
                                                    'renovado' => 'bg-purple-100 text-purple-800',
                                                    'parcial' => 'bg-yellow-100 text-yellow-800',
                                                    'devolvido' => 'bg-green-100 text-green-800'
                                                ];
                                                $color = $statusColors[$item->status] ?? 'bg-gray-100 text-gray-800';
                                            @endphp
                                            <span class="px-2 py-1 rounded text-xs font-semibold {{ $color }}">
                                                {{ ucfirst($item->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $historico->links() }}
                        </div>
                    @else
                        <div class="bg-gray-50 rounded-lg p-6 text-center">
                            <p class="text-gray-500">Nenhum histórico de empréstimo encontrado.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </x-ui.container>
</x-app-layout>