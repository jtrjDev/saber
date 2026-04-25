<x-app-layout>
    <x-ui.container>
        <header class="mb-6 pb-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                        Devolução de Ferramentas
                    </h1>
                    <p class="text-gray-600 mt-1">Finalize o aluguel #{{ $aluguel->id }}</p>
                </div>
                <a href="{{ route('alugueis.show', $aluguel) }}" class="text-gray-500 hover:text-gray-700">
                    ← Voltar para detalhes
                </a>
            </div>
        </header>

        <div class="bg-white shadow-lg rounded-xl p-6 lg:p-8 mb-6">
            <h2 class="text-xl font-bold mb-4">Informações do Aluguel</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Casa de Oração</p>
                    <p class="text-lg font-semibold">{{ $aluguel->casa->nome }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Usuário</p>
                    <p class="text-lg font-semibold">{{ $aluguel->usuario->name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Responsável</p>
                    <p class="text-lg font-semibold">{{ $aluguel->responsavel->name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Data Retirada</p>
                    <p class="text-lg font-semibold">{{ Carbon\Carbon::parse($aluguel->data_retirada)->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Previsão</p>
                    <p class="text-lg font-semibold {{ now()->gt($aluguel->data_prevista) ? 'text-red-600' : '' }}">
                        {{ Carbon\Carbon::parse($aluguel->data_prevista)->format('d/m/Y') }}
                        @if(now()->gt($aluguel->data_prevista))
                            <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded ml-2">ATRASADO</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <form action="{{ route('alugueis.devolver.post', $aluguel->id) }}" method="POST">
            @csrf

            <div class="bg-white shadow-lg rounded-xl overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b">
                    <h2 class="text-xl font-bold">Itens a Devolver</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase">Ferramenta</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold uppercase">Quantidade</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase">Estado na Devolução</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase">Observação</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($aluguel->itens as $i => $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 font-medium">
                                        {{ $item->ferramenta->nome }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        {{ $item->quantidade }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <select name="itens[{{ $i }}][estado]" 
                                            class="w-48 border-gray-300 rounded-lg estado-select"
                                            data-ferramenta="{{ $item->ferramenta->nome }}">
                                            <option value="bom">✅ Bom</option>
                                            <option value="manutencao">🔧 Precisa de Manutenção</option>
                                            <option value="quebrado">💔 Quebrado</option>
                                        </select>
                                    </td>
                                    <td class="px-6 py-4">
                                        <input type="text" name="itens[{{ $i }}][observacao]"
                                            class="w-full border-gray-300 rounded-lg"
                                            placeholder="Opcional">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Área de manutenção (aparece dinamicamente) --}}
            <div id="area-manutencao" class="hidden mt-6 bg-yellow-50 border border-yellow-300 rounded-xl p-6">
                <h2 class="text-xl font-bold text-yellow-800 mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    Registro de Manutenção Necessária
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Responsável pela manutenção</label>
                        <input type="text" name="manutencao_responsavel"
                            class="w-full border-gray-300 rounded-lg" placeholder="Nome do responsável">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Previsão de retorno</label>
                        <input type="date" name="manutencao_previsao"
                            class="w-full border-gray-300 rounded-lg">
                    </div>
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Observações gerais</label>
                    <textarea name="manutencao_observacao" rows="3"
                        class="w-full border-gray-300 rounded-lg" placeholder="Detalhes sobre a manutenção..."></textarea>
                </div>
            </div>

            {{-- Botões --}}
            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('alugueis.show', $aluguel->id) }}" 
                   class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
                    Cancelar
                </a>
                <button type="submit" 
                        onclick="return confirm('Confirmar devolução de todas as ferramentas?')"
                        class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                    Finalizar Devolução
                </button>
            </div>
        </form>
    </x-ui.container>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const selects = document.querySelectorAll(".estado-select");
            const areaManutencao = document.getElementById("area-manutencao");

            function verificarManutencao() {
                let precisaManutencao = false;
                let ferramentasManutencao = [];

                selects.forEach(sel => {
                    if (sel.value === "manutencao" || sel.value === "quebrado") {
                        precisaManutencao = true;
                        ferramentasManutencao.push(sel.dataset.ferramenta);
                    }
                });

                if (precisaManutencao) {
                    areaManutencao.classList.remove("hidden");
                    // Adiciona informações das ferramentas
                    let infoDiv = areaManutencao.querySelector('.ferramentas-info');
                    if (!infoDiv) {
                        infoDiv = document.createElement('div');
                        infoDiv.className = 'mt-4 p-3 bg-yellow-100 rounded-lg text-sm text-yellow-800 ferramentas-info';
                        areaManutencao.insertBefore(infoDiv, areaManutencao.querySelector('div:last-child'));
                    }
                    infoDiv.innerHTML = `<strong>Ferramentas que precisam de atenção:</strong> ${ferramentasManutencao.join(', ')}`;
                } else {
                    areaManutencao.classList.add("hidden");
                }
            }

            selects.forEach(sel => {
                sel.addEventListener("change", verificarManutencao);
            });
            
            verificarManutencao();
        });
    </script>
</x-app-layout>