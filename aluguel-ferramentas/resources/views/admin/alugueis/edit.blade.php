<x-app-layout>
    <x-ui.container>
        <header class="mb-6 pb-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                        Editar Aluguel #{{ $aluguel->id }}
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">Altere os dados do empréstimo</p>
                </div>
                <a href="{{ route('alugueis.show', $aluguel) }}" class="text-gray-500 hover:text-gray-700">
                    ← Voltar para detalhes
                </a>
            </div>
        </header>

        <div class="bg-white shadow-lg rounded-xl p-6 lg:p-8 max-w-5xl mx-auto">

            <form action="{{ route('alugueis.update', $aluguel) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

                    <x-ui.select name="casa_id" label="Casa de Oração" required>
                        @foreach($casas as $c)
                            <option value="{{ $c->id }}" @selected($c->id == $aluguel->casa_id)>
                                {{ $c->nome }}
                            </option>
                        @endforeach
                    </x-ui.select>

                    <x-ui.select name="user_id" label="Usuário" required>
                        @foreach($usuarios as $u)
                            <option value="{{ $u->id }}" @selected($u->id == $aluguel->user_id)>
                                {{ $u->name }}
                            </option>
                        @endforeach
                    </x-ui.select>

                    <x-ui.select name="responsavel_id" label="Responsável pela Entrega" required>
                        @foreach($responsaveis as $r)
                            <option value="{{ $r->id }}" @selected($r->id == $aluguel->responsavel_id)>
                                {{ $r->name }}
                            </option>
                        @endforeach
                    </x-ui.select>

                    <x-ui.input type="date" name="data_retirada" label="Data da Retirada" 
                        value="{{ $aluguel->data_retirada }}" required />

                    <div>
                        <x-ui.select name="alugar_por" label="Período do Empréstimo" required>
                            <option value="1 dia" @selected($periodoAtual == '1 dia')>1 dia</option>
                            <option value="2 dias" @selected($periodoAtual == '2 dias')>2 dias</option>
                            <option value="3 dias" @selected($periodoAtual == '3 dias')>3 dias</option>
                            <option value="4 dias" @selected($periodoAtual == '4 dias')>4 dias</option>
                            <option value="5 dias" @selected($periodoAtual == '5 dias')>5 dias</option>
                            <option value="1 semana" @selected($periodoAtual == '1 semana')>1 semana</option>
                            <option value="2 semanas" @selected($periodoAtual == '2 semanas')>2 semanas</option>
                            <option value="3 semanas" @selected($periodoAtual == '3 semanas')>3 semanas</option>
                            <option value="1 mes" @selected($periodoAtual == '1 mes')>1 mês</option>
                            <option value="2 meses" @selected($periodoAtual == '2 meses')>2 meses</option>
                            <option value="3 meses" @selected($periodoAtual == '3 meses')>3 meses</option>
                            <option value="4 meses" @selected($periodoAtual == '4 meses')>4 meses</option>
                            <option value="6 meses" @selected($periodoAtual == '6 meses')>6 meses</option>
                            <option value="1 ano" @selected($periodoAtual == '1 ano')>1 ano</option>
                        </x-ui.select>
                        
                        @if($periodoAtual == 'personalizado')
                            <p class="text-xs text-yellow-600 mt-1">
                                ⚠️ Período atual: {{ $diasTotais }} dias (de {{ \Carbon\Carbon::parse($aluguel->data_retirada)->format('d/m/Y') }} até {{ \Carbon\Carbon::parse($aluguel->data_prevista)->format('d/m/Y') }})
                            </p>
                        @endif
                    </div>

                </div>

                <div class="my-8 border-b pb-3">
                    <h2 class="text-xl font-bold text-gray-800">Ferramentas</h2>
                    <p class="text-sm text-gray-500">Edite as ferramentas do aluguel</p>
                </div>

                <div class="overflow-x-auto mb-6">
                    <table class="min-w-full divide-y divide-gray-300" id="tabela-itens">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left">Ferramenta</th>
                                <th class="px-4 py-3 text-left">Observação</th>
                                <th class="px-4 py-3 text-center w-24">Ação</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="itens-container">
                            @foreach($aluguel->itens as $i => $item)
                            <tr>
                                <td class="px-4 py-2">
                                    <select name="ferramentas[{{ $i }}][id]" required
                                        class="w-full border-gray-300 rounded-lg">
                                        @foreach($ferramentas as $f)
                                            <option value="{{ $f->id }}" @selected($f->id == $item->ferramenta_id)>
                                                {{ $f->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-4 py-2">
                                    <input type="text" name="ferramentas[{{ $i }}][observacao]"
                                        value="{{ $item->observacao }}"
                                        class="w-full border-gray-300 rounded-lg" placeholder="Opcional"/>
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <button type="button" class="text-red-600 hover:text-red-800" 
                                        onclick="this.closest('tr').remove()">
                                        Remover
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <button type="button" id="addItem"
                    class="mb-6 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow">
                    + Adicionar Ferramenta
                </button>

                <div class="flex gap-3">
                    <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg">
                        Salvar Alterações
                    </button>
                    <a href="{{ route('alugueis.show', $aluguel) }}" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg">
                        Cancelar
                    </a>
                </div>

            </form>

        </div>
    </x-ui.container>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let index = {{ $aluguel->itens->count() }};
            const container = document.getElementById("itens-container");
            const ferramentas = @json($ferramentas);

            document.getElementById("addItem").addEventListener("click", function() {
                const row = document.createElement("tr");
                let options = '<option value="">Selecione…</option>';
                ferramentas.forEach(f => {
                    options += `<option value="${f.id}">${f.nome}</option>`;
                });
                row.innerHTML = `
                    <td class="px-4 py-2">
                        <select name="ferramentas[${index}][id]" required class="w-full border-gray-300 rounded-lg">
                            ${options}
                        </select>
                    </td>
                    <td class="px-4 py-2">
                        <input type="text" name="ferramentas[${index}][observacao]"
                            class="w-full border-gray-300 rounded-lg" placeholder="Opcional"/>
                    </td>
                    <td class="px-4 py-2 text-center">
                        <button type="button" class="text-red-600" onclick="this.closest('tr').remove()">Remover</button>
                    </td>
                `;
                container.appendChild(row);
                index++;
            });
        });
    </script>
</x-app-layout>