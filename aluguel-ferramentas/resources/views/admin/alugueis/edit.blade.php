<x-app-layout>

    <header class="mb-6 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
            Editar Aluguel
        </h1>
    </header>

    <div class="bg-white shadow-lg rounded-xl p-6 lg:p-8 max-w-5xl mx-auto">

        <form action="{{ route('alugueis.update', $aluguel) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Seleção Geral --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

                {{-- Casa --}}
                <x-ui.select name="casa_id" label="Casa de Oração" required>
                    @foreach($casas as $c)
                        <option value="{{ $c->id }}" @selected($c->id == $aluguel->casa_id)>
                            {{ $c->nome }}
                        </option>
                    @endforeach
                </x-ui.select>

                {{-- Usuário --}}
                <x-ui.select name="user_id" label="Usuário" required>
                    @foreach($usuarios as $u)
                        <option value="{{ $u->id }}" @selected($u->id == $aluguel->user_id)>
                            {{ $u->name }}
                        </option>
                    @endforeach
                </x-ui.select>

                {{-- Responsável --}}
                <x-ui.select name="responsavel_id" label="Responsável pela Entrega" required>
                    @foreach($responsaveis as $r)
                        <option value="{{ $r->id }}" @selected($r->id == $aluguel->responsavel_id)>
                            {{ $r->name }}
                        </option>
                    @endforeach
                </x-ui.select>

                {{-- Data retirada --}}
                <x-ui.input 
                    type="date"
                    name="data_retirada"
                    label="Data da Retirada"
                    value="{{ $aluguel->data_retirada }}"
                    required
                />

                {{-- Período --}}
                <x-ui.select name="alugar_por" label="Período do Empréstimo" required>
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

            {{-- Divisor --}}
            <div class="my-8 border-b pb-3">
                <h2 class="text-xl font-bold text-gray-800">Ferramentas</h2>
                <p class="text-sm text-gray-500">Edite, adicione ou remova ferramentas.</p>
            </div>

            {{-- Tabela --}}
            <div class="overflow-x-auto mb-6">
                <table class="min-w-full divide-y divide-gray-300" id="tabela-itens">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 text-left">Ferramenta</th>
                            <th class="px-4 py-2 text-left">Obs</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($aluguel->itens as $i => $item)
                            <tr>
                                <td class="px-4 py-2">
                                    <select name="ferramentas[{{ $i }}][id]" 
                                            class="w-full border-gray-300 rounded-lg"
                                            required>
                                        @foreach($ferramentas as $f)
                                            <option value="{{ $f->id }}" @selected($f->id == $item->ferramenta_id)>
                                                {{ $f->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>

                                <td class="px-4 py-2">
                                    <input type="number" 
                                           min="1"
                                           class="w-20 border-gray-300 rounded-lg"
                                           name="ferramentas[{{ $i }}][quantidade]"
                                           value="{{ $item->quantidade }}"
                                           required>
                                </td>

                                <td class="px-4 py-2">
                                    <input type="text"
                                           name="ferramentas[{{ $i }}][observacao]"
                                           value="{{ $item->observacao }}"
                                           class="w-full border-gray-300 rounded-lg"
                                           placeholder="Opcional">
                                </td>

                                <td class="px-4 py-2">
                                    <button type="button" class="text-red-600" onclick="this.closest('tr').remove()">
                                        Remover
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>

            {{-- Botão adicionar --}}
            <button type="button" id="addItem" 
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow mb-8">
                + Adicionar Ferramenta
            </button>

            {{-- Botões --}}
            <div class="flex gap-3">
                <x-ui.button-primary>Salvar Alterações</x-ui.button-primary>
                <x-ui.button-secondary href="{{ route('alugueis.index') }}">Voltar</x-ui.button-secondary>
            </div>

        </form>

    </div>

    {{-- Script --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            let index = {{ $aluguel->itens->count() }};

            document.getElementById("addItem").addEventListener("click", function() {
                const tbody = document.querySelector("#tabela-itens tbody");

                let tr = document.createElement("tr");

                tr.innerHTML = `
                    <td class="px-4 py-2">
                        <select name="ferramentas[${index}][id]" required
                            class="w-full border-gray-300 rounded-lg">
                            <option value="">Selecione…</option>
                            @foreach($ferramentas as $f)
                                <option value="{{ $f->id }}">{{ $f->nome }}</option>
                            @endforeach
                        </select>
                    </td>

                    <td class="px-4 py-2">
                        <input type="text" name="ferramentas[${index}][observacao]"
                            class="w-full border-gray-300 rounded-lg" placeholder="Opcional" />
                    </td>

                    <td class="px-4 py-2">
                        <button type="button" class="text-red-600" onclick="this.closest('tr').remove()">
                            Remover
                        </button>
                    </td>
                `;

                tbody.appendChild(tr);
                index++;
            });

        });
    </script>

</x-app-layout>
