<x-app-layout>
    <x-ui.container>

        <h1 class="text-2xl font-bold text-gray-900 mb-6">Novo Setor</h1>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">

            <form method="POST" action="{{ route('setores.store') }}">
                @csrf

                <x-ui.input name="nome" label="Nome do Setor" required />

                <div class="flex gap-3 mt-6">
                    <x-ui.button-primary>Salvar</x-ui.button-primary>
                    <x-ui.button-secondary href="{{ route('setores.index') }}">Voltar</x-ui.button-secondary>
                </div>

            </form>

        </div>

    </x-ui.container>
</x-app-layout>
