<x-app-layout>
    <h1 class="text-2xl font-bold mb-6">Editar Usuário</h1>

    <form action="{{ route('usuarios.update', $usuario) }}" method="POST" class="bg-white shadow-lg rounded-xl p-6">
        @csrf
        @method('PUT')

        <x-ui.input name="name" label="Nome" value="{{ $usuario->name }}" required />
        <x-ui.input name="email" type="email" label="Email" value="{{ $usuario->email }}" required />

        {{-- Função --}}
        <x-ui.select name="role" label="Função" required>
            <option value="admin" @selected($usuario->role == 'admin')>Admin</option>
            <option value="gestor_obra" @selected($usuario->role == 'gestor_obra')>Gestor de Obra</option>
            <option value="responsavel_ferramentas" @selected($usuario->role == 'responsavel_ferramentas')>Responsável pelas Ferramentas</option>
            <option value="membro" @selected($usuario->role == 'membro')>Membro</option>
        </x-ui.select>

        {{-- Setor --}}
        <x-ui.select name="setor_id" label="Setor">
            <option value="">Nenhum</option>
            @foreach($setores as $setor)
                <option value="{{ $setor->id }}" @selected($usuario->setor_id == $setor->id)>
                    {{ $setor->nome }}
                </option>
            @endforeach
        </x-ui.select>

        <div class="flex gap-2 mt-4">
            <x-ui.button-primary>Salvar</x-ui.button-primary>
            <x-ui.button-secondary href="{{ route('usuarios.index') }}">Voltar</x-ui.button-secondary>
        </div>
    </form>
</x-app-layout>
