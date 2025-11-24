<x-app-layout>
    <x-ui.container>

        <h1 class="text-2xl font-bold text-gray-900 mb-6">
            Editar Casa de Oração
        </h1>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 lg:p-8">

            <form action="{{ route('casas.update', $casa) }}" method="POST">
                @csrf
                @method('PUT')

                <x-ui.input 
                    name="nome" 
                    label="Nome da Casa de Oração" 
                    value="{{ $casa->nome }}" 
                    required
                />

                <x-ui.select name="setor_id" label="Setor" required>
                    @foreach($setores as $setor)
                        <option 
                            value="{{ $setor->id }}"
                            @selected($casa->setor_id == $setor->id)
                        >
                            {{ $setor->nome }}
                        </option>
                    @endforeach
                </x-ui.select>

                <div class="flex gap-3 mt-6">
                    <x-ui.button-primary>Salvar</x-ui.button-primary>
                    <x-ui.button-secondary href="{{ route('casas.index') }}">
                        Voltar
                    </x-ui.button-secondary>
                </div>

            </form>

        </div>

    </x-ui.container>
</x-app-layout>
