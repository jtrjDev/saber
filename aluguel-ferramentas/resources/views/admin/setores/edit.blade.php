<x-app-layout>
    <h1 class="text-2xl font-bold mb-4">Editar Setor</h1>

    <form method="POST" action="{{ route('setores.update', $setor) }}">
        @csrf
        @method('PUT')

        <x-ui.input name="nome" label="Nome do Setor" value="{{ $setor->nome }}" required />

        <div class="flex gap-2">
            <x-ui.button-primary>Salvar</x-ui.button-primary>
            <x-ui.button-secondary href="{{ route('setores.index') }}">Voltar</x-ui.button-secondary>
        </div>
    </form>
</x-app-layout>
