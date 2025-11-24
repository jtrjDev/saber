<x-app-layout>
    <h1 class="text-2xl font-bold mb-6">Cadastrar Usuário</h1>

    <form action="{{ route('usuarios.store') }}" method="POST" class="bg-white shadow-lg rounded-xl p-6">
        @csrf

        <x-ui.input name="name" label="Nome" required />
        <x-ui.input name="email" type="email" label="Email" required />
        <x-ui.input name="password" type="password" label="Senha" required />

        {{-- Função --}}
        <x-ui.select name="role" label="Função" required>
            <option value="admin">Admin</option>
            <option value="gestor_obra">Gestor de Obra</option>
            <option value="responsavel_ferramentas">Responsável pelas Ferramentas</option>
            <option value="membro" selected>Membro</option>
        </x-ui.select>

        {{-- Setor (opcional) --}}
        <x-ui.select name="setor_id" label="Setor (opcional)">
            <option value="">Nenhum</option>
            @foreach($setores as $setor)
                <option value="{{ $setor->id }}">{{ $setor->nome }}</option>
            @endforeach
        </x-ui.select>

        <div class="flex gap-2 mt-4">
            <x-ui.button-primary>Salvar</x-ui.button-primary>
            <x-ui.button-secondary href="{{ route('usuarios.index') }}">Voltar</x-ui.button-secondary>
        </div>
    </form>
</x-app-layout>
