<x-app-layout>

    <x-ui.container>

        {{-- TÍTULO --}}
        <div class="mb-10">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                Devolução de Ferramentas
            </h1>
            <p class="text-gray-600 dark:text-gray-300 mt-1">
                Finalize o aluguel e registre o estado das ferramentas devolvidas.
            </p>
        </div>

        {{-- INFORMAÇÕES DO ALUGUEL --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 mb-10 border-l-4 border-indigo-500">
            <h2 class="text-xl font-bold mb-4">Informações do Aluguel</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <div>
                    <p class="text-xs text-gray-500 uppercase">Casa de Oração</p>
                    <p class="text-lg font-semibold">{{ $aluguel->casa->nome }}</p>
                </div>

                <div>
                    <p class="text-xs text-gray-500 uppercase">Usuário / Membro</p>
                    <p class="text-lg font-semibold">{{ $aluguel->usuario->name }}</p>
                </div>

                <div>
                    <p class="text-xs text-gray-500 uppercase">Responsável pela Entrega</p>
                    <p class="text-lg font-semibold">{{ $aluguel->responsavel->name }}</p>
                </div>

            </div>
        </div>

        {{-- FORM DE DEVOLUÇÃO --}}
        <form action="{{ route('alugueis.devolver.post', $aluguel->id) }}" method="POST">
            @csrf

            {{-- LISTA DE ITENS --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 mb-12">
                <h2 class="text-xl font-bold mb-6">Itens Devolvidos</h2>

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
                                <tr>
                                    <td class="px-6 py-4">
                                        {{ $item->ferramenta->nome }}
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        {{ $item->quantidade }}
                                    </td>

                                    {{-- Estado na Devolução --}}
                                    <td class="px-6 py-4">
                                        <select name="itens[{{ $i }}][estado]"
                                            class="w-full border-gray-300 rounded-lg dark:bg-gray-700 dark:text-white">

                                            <option value="bom">Bom</option>
                                            <option value="manutencao">Precisa de Manutenção</option>
                                            <option value="quebrado">Quebrado</option>
                                        </select>
                                    </td>

                                    {{-- Observação --}}
                                    <td class="px-6 py-4">
                                        <input type="text"
                                               name="itens[{{ $i }}][observacao]"
                                               class="w-full border-gray-300 rounded-lg dark:bg-gray-700 dark:text-white"
                                               placeholder="Opcional">
                                    </td>

                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>

            {{-- CAMPOS ADICIONAIS DE MANUTENÇÃO --}}
            <div id="area-manutencao" class="hidden bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-300 dark:border-yellow-700 rounded-xl p-6 mb-10">

                <h2 class="text-xl font-bold text-yellow-800 dark:text-yellow-300 mb-4">
                    Registro de Manutenção Necessária
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div>
                        <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">
                            Responsável pela manutenção
                        </label>
                        <input type="text" name="manutencao_responsavel"
                               class="w-full border-gray-300 rounded-lg dark:bg-gray-700 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">
                            Previsão de retorno
                        </label>
                        <input type="date" name="manutencao_previsao"
                               class="w-full border-gray-300 rounded-lg dark:bg-gray-700 dark:text-white">
                    </div>

                </div>

                <div class="mt-4">
                    <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">
                        Observações gerais
                    </label>
                    <textarea name="manutencao_observacao"
                              rows="3"
                              class="w-full border-gray-300 rounded-lg dark:bg-gray-700 dark:text-white"></textarea>
                </div>

            </div>

            {{-- BOTÕES --}}
            <div class="flex justify-end gap-3">

                <x-ui.button-secondary href="{{ route('alugueis.show', $aluguel->id) }}">
                    Cancelar
                </x-ui.button-secondary>

                <x-ui.button-primary>
                    Finalizar Devolução
                </x-ui.button-primary>

            </div>

        </form>

    </x-ui.container>

    {{-- SCRIPT: mostrar área de manutenção quando necessário --}}
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const selects = document.querySelectorAll("select[name^='itens']");
            const area = document.getElementById("area-manutencao");

            function verificarManutencao() {
                let precisa = false;

                selects.forEach(sel => {
                    if (sel.value === "manutencao" || sel.value === "quebrado") {
                        precisa = true;
                    }
                });

                if (precisa) {
                    area.classList.remove("hidden");
                } else {
                    area.classList.add("hidden");
                }
            }

            selects.forEach(sel => {
                sel.addEventListener("change", verificarManutencao);
            });
        });
    </script>

</x-app-layout>
