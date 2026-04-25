<x-app-layout>
    <x-ui.container>
        <header class="mb-6 pb-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                        Novo Aluguel de Ferramentas
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">Preencha os dados para registrar um novo empréstimo</p>
                </div>
                <a href="{{ route('alugueis.index') }}" class="text-gray-500 hover:text-gray-700">
                    ← Voltar
                </a>
            </div>
        </header>

        @if(auth()->user()->role !== 'admin')
            <div class="mb-4 p-3 bg-blue-100 text-blue-800 rounded-lg">
                <i class="fas fa-building"></i>
                Cadastrando aluguel no setor: <strong>{{ auth()->user()->setor->nome ?? 'N/A' }}</strong>
            </div>
        @endif

        <div class="bg-white shadow-lg rounded-xl p-6 lg:p-8 max-w-5xl mx-auto">

            <form action="{{ route('alugueis.store') }}" method="POST" id="formAluguel">
                @csrf

                {{-- Seleção Geral --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

                    {{-- Casa --}}
                    <x-ui.select name="casa_id" label="Casa de Oração" required>
                        <option value="">Selecione…</option>
                        @foreach($casas as $casa)
                            <option value="{{ $casa->id }}" {{ old('casa_id') == $casa->id ? 'selected' : '' }}>
                                {{ $casa->nome }}
                            </option>
                        @endforeach
                    </x-ui.select>
                    @error('casa_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror

                    {{-- Usuário --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Quem está alugando? <span class="text-red-500">*</span>
                        </label>
                        <select id="user_id" name="user_id"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Selecione…</option>
                            @foreach($usuarios as $u)
                                <option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }}
                                </option>
                            @endforeach
                            <option value="outro">➕ Outro (digitar nome)</option>
                        </select>
                        <input type="text" 
                            id="novo_usuario"
                            name="novo_usuario"
                            value="{{ old('novo_usuario') }}"
                            placeholder="Digite o nome do novo usuário"
                            class="mt-2 w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 hidden"
                        />
                        @error('user_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        @error('novo_usuario')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Responsável --}}
                    <x-ui.select name="responsavel_id" label="Responsável pela Entrega" required>
                        <option value="">Selecione…</option>
                        @foreach($responsaveis as $r)
                            <option value="{{ $r->id }}" {{ old('responsavel_id') == $r->id ? 'selected' : '' }}>
                                {{ $r->name }}
                            </option>
                        @endforeach
                    </x-ui.select>
                    @error('responsavel_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror

                    {{-- Data da retirada --}}
                    <x-ui.input 
                        name="data_retirada"
                        label="Data da Retirada"
                        type="date"
                        value="{{ old('data_retirada', date('Y-m-d')) }}"
                        required
                    />
                    @error('data_retirada')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror

                    {{-- Período --}}
                    <x-ui.select name="alugar_por" label="Período do Empréstimo" required>
                        <option value="">Selecione…</option>
                        <option value="1 dia" {{ old('alugar_por') == '1 dia' ? 'selected' : '' }}>1 dia</option>
                        <option value="2 dias" {{ old('alugar_por') == '2 dias' ? 'selected' : '' }}>2 dias</option>
                        <option value="3 dias" {{ old('alugar_por') == '3 dias' ? 'selected' : '' }}>3 dias</option>
                        <option value="4 dias" {{ old('alugar_por') == '4 dias' ? 'selected' : '' }}>4 dias</option>
                        <option value="5 dias" {{ old('alugar_por') == '5 dias' ? 'selected' : '' }}>5 dias</option>
                        <option value="1 semana" {{ old('alugar_por') == '1 semana' ? 'selected' : '' }}>1 semana</option>
                        <option value="2 semanas" {{ old('alugar_por') == '2 semanas' ? 'selected' : '' }}>2 semanas</option>
                        <option value="3 semanas" {{ old('alugar_por') == '3 semanas' ? 'selected' : '' }}>3 semanas</option>
                        <option value="1 mes" {{ old('alugar_por') == '1 mes' ? 'selected' : '' }}>1 mês</option>
                        <option value="2 meses" {{ old('alugar_por') == '2 meses' ? 'selected' : '' }}>2 meses</option>
                        <option value="3 meses" {{ old('alugar_por') == '3 meses' ? 'selected' : '' }}>3 meses</option>
                        <option value="4 meses" {{ old('alugar_por') == '4 meses' ? 'selected' : '' }}>4 meses</option>
                        <option value="6 meses" {{ old('alugar_por') == '6 meses' ? 'selected' : '' }}>6 meses</option>
                        <option value="1 ano" {{ old('alugar_por') == '1 ano' ? 'selected' : '' }}>1 ano</option>
                    </x-ui.select>
                    @error('alugar_por')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror

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
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Ferramenta</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Observação</th>
                                <th class="px-4 py-3 w-24 text-center text-sm font-medium text-gray-700">Ação</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="itens-container">
                            <!-- Linhas adicionadas via JS -->
                        </tbody>
                    </table>
                </div>

                {{-- Botão de adicionar linha --}}
                <button type="button" id="addItem"
                    class="mb-6 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Adicionar Ferramenta
                </button>

                @error('ferramentas')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror

                {{-- Botões --}}
                <div class="flex gap-3 pt-4 border-t">
                    <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow transition">
                        Salvar Aluguel
                    </button>
                    <a href="{{ route('alugueis.index') }}" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg shadow transition">
                        Cancelar
                    </a>
                </div>

            </form>

        </div>
    </x-ui.container>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Mostrar campo "Novo usuário"
            const userSelect = document.getElementById("user_id");
            const novoUsuario = document.getElementById("novo_usuario");

            function toggleNovoUsuario() {
                if (userSelect.value === "outro") {
                    novoUsuario.classList.remove("hidden");
                    novoUsuario.setAttribute("required", true);
                } else {
                    novoUsuario.classList.add("hidden");
                    novoUsuario.removeAttribute("required");
                }
            }

            userSelect.addEventListener("change", toggleNovoUsuario);
            toggleNovoUsuario();

            // Adicionar itens
            let index = 0;
            const addItemBtn = document.getElementById("addItem");
            const container = document.getElementById("itens-container");

            // Carregar ferramentas do PHP para JS
            const ferramentas = @json($ferramentas);

            addItemBtn.addEventListener("click", function() {
                const row = document.createElement("tr");
                
                let options = '<option value="">Selecione…</option>';
                ferramentas.forEach(f => {
                    options += `<option value="${f.id}">${f.nome}</option>`;
                });

                row.innerHTML = `
                    <td class="px-4 py-2">
                        <select name="ferramentas[${index}][id]" required
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            ${options}
                        </select>
                    </td>
                    <td class="px-4 py-2">
                        <input type="text"
                            name="ferramentas[${index}][observacao]" 
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Opcional"/>
                    </td>
                    <td class="px-4 py-2 text-center">
                        <button type="button" class="text-red-600 hover:text-red-800 transition" 
                            onclick="this.closest('tr').remove()">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </td>
                `;
                
                container.appendChild(row);
                index++;
            });

            // Se tiver erros, manter itens
            @if(old('ferramentas'))
                @foreach(old('ferramentas') as $key => $item)
                    (function() {
                        const row = document.createElement("tr");
                        let options = '<option value="">Selecione…</option>';
                        ferramentas.forEach(f => {
                            const selected = f.id == {{ $item['id'] ?? 0 }} ? 'selected' : '';
                            options += `<option value="${f.id}" ${selected}>${f.nome}</option>`;
                        });
                        row.innerHTML = `
                            <td class="px-4 py-2">
                                <select name="ferramentas[${index}][id]" required class="w-full border-gray-300 rounded-lg">
                                    ${options}
                                </select>
                            </td>
                            <td class="px-4 py-2">
                                <input type="text" name="ferramentas[${index}][observacao]" value="{{ $item['observacao'] ?? '' }}"
                                    class="w-full border-gray-300 rounded-lg" placeholder="Opcional"/>
                            </td>
                            <td class="px-4 py-2 text-center">
                                <button type="button" class="text-red-600" onclick="this.closest('tr').remove()">Remover</button>
                            </td>
                        `;
                        container.appendChild(row);
                        index++;
                    })();
                @endforeach
            @endif
        });
    </script>
</x-app-layout>