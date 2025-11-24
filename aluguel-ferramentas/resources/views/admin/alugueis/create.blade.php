<x-app-layout>

    <header class="mb-6 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
            Novo Aluguel de Ferramentas
        </h1>
    </header>

    <div class="bg-white shadow-lg rounded-xl p-6 lg:p-8 max-w-5xl mx-auto">

        <form action="{{ route('alugueis.store') }}" method="POST">
            @csrf

            {{-- Seleção Geral --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

                {{-- Casa --}}
                <x-ui.select name="casa_id" label="Casa de Oração" required>
                    <option value="">Selecione…</option>
                    @foreach($casas as $casa)
                        <option value="{{ $casa->id }}">{{ $casa->nome }}</option>
                    @endforeach
                </x-ui.select>

                {{-- Usuário --}}
                <div class="col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Quem está alugando?
                    </label>

                    <select id="user_id" name="user_id"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Selecione…</option>

                        @foreach($usuarios as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach

                        <option value="outro">➕ Outro (digitar nome)</option>
                    </select>

                    {{-- Campo aparece só quando selecionar "Outro" --}}
                    <input 
                        type="text" 
                        id="novo_usuario"
                        name="novo_usuario"
                        placeholder="Digite o nome do novo usuário"
                        class="mt-2 w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 hidden"
                    />
                </div>

                {{-- Responsável --}}
                <x-ui.select name="responsavel_id" label="Responsável pela Entrega" required>
                    <option value="">Selecione…</option>
                    @foreach($responsaveis as $r)
                        <option value="{{ $r->id }}">{{ $r->name }}</option>
                    @endforeach
                </x-ui.select>

                {{-- Data da retirada --}}
                <x-ui.input 
                    name="data_retirada"
                    label="Data da Retirada"
                    type="date"
                    required
                />

                {{-- Período --}}
                <x-ui.select name="alugar_por" label="Período do Empréstimo" required>
                    <option value="">Selecione…</option>
                    <option value="1 dia">1 dia</option>
                    <option value="2 dias">2 dias</option>
                    <option value="3 dias">3 dias</option>
                    <option value="4 dias">4 dias</option>
                    <option value="5 dias">5 dias</option>
                    <option value="1 semana">1 semana</option>
                    <option value="2 semanas">2 semanas</option>
                    <option value="3 semanas">3 semanas</option>
                    <option value="1 mes">1 mês</option>
                    <option value="2 meses">2 meses</option>
                    <option value="3 meses">3 meses</option>
                    <option value="4 meses">4 meses</option>
                    <option value="6 meses">6 meses</option>
                    <option value="1 ano">1 ano</option>
                </x-ui.select>

            </div>

            {{-- LINHA DIVISÓRIA --}}
            <div class="my-8 border-b pb-3">
                <h2 class="text-xl font-bold text-gray-800">Ferramentas</h2>
                <p class="text-sm text-gray-500">Adicione uma ou mais ferramentas ao aluguel.</p>
            </div>

            {{-- Tabela de itens --}}
            <div class="overflow-x-auto mb-6">
                <table class="min-w-full divide-y divide-gray-300" id="tabela-itens">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-medium">Ferramenta</th>
                            <th class="px-4 py-2 text-left text-sm font-medium">Qtd</th>
                            <th class="px-4 py-2 text-left text-sm font-medium">Observação</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <!-- Linhas adicionadas via JS -->
                    </tbody>
                </table>
            </div>

            {{-- Botão de adicionar linha --}}
            <div class="flex justify-start mb-8">
                <button 
                    type="button"
                    id="addItem"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow"
                >
                    + Adicionar Ferramenta
                </button>
            </div>

            {{-- Botões --}}
            <div class="flex gap-3">
                <x-ui.button-primary>Salvar Aluguel</x-ui.button-primary>

                <x-ui.button-secondary href="{{ route('alugueis.index') }}">
                    Voltar
                </x-ui.button-secondary>
            </div>

        </form>

    </div>

    {{-- SCRIPT --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            /* Mostrar campo "Novo usuário" */
            const userSelect = document.getElementById("user_id");
            const novoUsuario = document.getElementById("novo_usuario");

            userSelect.addEventListener("change", function () {
                if (this.value === "outro") {
                    novoUsuario.classList.remove("hidden");
                    novoUsuario.setAttribute("required", true);
                } else {
                    novoUsuario.classList.add("hidden");
                    novoUsuario.removeAttribute("required");
                }
            });

            /* Adicionar itens */
            const addItemBtn = document.getElementById("addItem");
            const tabelaItens = document.querySelector("#tabela-itens tbody");

            let index = 0;

            addItemBtn.addEventListener("click", function() {
                const linha = document.createElement("tr");

                linha.innerHTML = `
                    <td class="px-4 py-2">
                        <select name="ferramentas[${index}][id]" required
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Selecione…</option>
                            @foreach($ferramentas as $f)
                                <option value="{{ $f->id }}">{{ $f->nome }} — ({{ $f->estado }})</option>
                            @endforeach
                        </select>
                    </td>

                    <td class="px-4 py-2">
                        <input 
                            type="number" 
                            name="ferramentas[${index}][quantidade]" 
                            min="1"
                            value="1"
                            required
                            class="w-20 border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                        />
                    </td>

                    <td class="px-4 py-2">
                        <input 
                            type="text"
                            name="ferramentas[${index}][observacao]" 
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Opcional"
                        />
                    </td>

                    <td class="px-4 py-2 text-center">
                        <button type="button" class="text-red-600 hover:text-red-800" 
                            onclick="this.closest('tr').remove()">
                            Remover
                        </button>
                    </td>
                `;

                tabelaItens.appendChild(linha);
                index++;
            });

        });
    </script>

</x-app-layout>
