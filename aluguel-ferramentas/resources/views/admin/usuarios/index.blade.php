<x-app-layout>

    <header class="mb-6 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-gray-900">
            Gestão de Usuários
        </h1>
    </header>

    {{-- Filtros --}}
    <form method="GET" class="mb-6">
        <x-ui.filter-box>

            {{-- Filtro por Role --}}
            <x-ui.select name="role" label="Função">
                <option value="">Todos</option>
                <option value="admin" @selected(request('role')=='admin')>Admin</option>
                <option value="gestor_obra" @selected(request('role')=='gestor_obra')>Gestor de Obra</option>
                <option value="responsavel_ferramentas" @selected(request('role')=='responsavel_ferramentas')>Responsável Ferramentas</option>
                <option value="membro" @selected(request('role')=='membro')>Membro</option>
            </x-ui.select>

            {{-- Filtro por Setor --}}
            <x-ui.select name="setor_id" label="Setor">
                <option value="">Todos</option>
                @foreach($setores as $setor)
                    <option value="{{ $setor->id }}" @selected(request('setor_id')==$setor->id)>
                        {{ $setor->nome }}
                    </option>
                @endforeach
            </x-ui.select>

        </x-ui.filter-box>
    </form>

    {{-- Botão Novo Usuário --}}
    <div class="mb-6 flex justify-end">
        <x-ui.button-primary href="{{ route('usuarios.create') }}">
            Novo Usuário
        </x-ui.button-primary>
    </div>

    {{-- Tabela --}}
    <x-ui.table>
        <x-slot:head>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Função</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Setor</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Ações</th>
        </x-slot:head>

        @foreach($users as $usuario)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">{{ $usuario->name }}</td>
                <td class="px-6 py-4">{{ $usuario->email }}</td>
                <td class="px-6 py-4 capitalize">{{ str_replace('_', ' ', $usuario->role) }}</td>
                <td class="px-6 py-4">{{ $usuario->setor->nome ?? '-' }}</td>

                <td class="px-6 py-4 text-center space-x-2">

                    <x-ui.button-secondary href="{{ route('usuarios.edit', $usuario) }}">
                        Editar
                    </x-ui.button-secondary>

                    <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST"
                          class="inline" onsubmit="return confirm('Deseja excluir este usuário?');">
                        @csrf
                        @method('DELETE')
                        <x-ui.button-danger>Excluir</x-ui.button-danger>
                    </form>

                </td>
            </tr>
        @endforeach
    </x-ui.table>

    <div class="mt-6">
        {{ $users->links() }}
    </div>

</x-app-layout>
