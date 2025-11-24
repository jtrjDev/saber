<x-app-layout>

    <header class="mb-6 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
            Cadastrar Ferramenta
        </h1>
    </header>

    <div class="bg-white shadow-lg rounded-xl p-6 lg:p-8 max-w-4xl mx-auto">

        <form action="{{ route('ferramentas.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Nome --}}
            <x-ui.input name="nome" label="Nome da Ferramenta" required />

            {{-- Setor --}}
            <x-ui.select name="setor_id" label="Setor" required>
                <option value="">Selecione…</option>
                @foreach($setores as $setor)
                    <option value="{{ $setor->id }}">{{ $setor->nome }}</option>
                @endforeach
            </x-ui.select>

            {{-- Estado --}}
            <x-ui.select name="estado" label="Estado" required>
                <option value="bom">Bom</option>
                <option value="ruim">Ruim</option>
                <option value="manutenção">Manutenção</option>
                <option value="quebrado">Quebrado</option>
            </x-ui.select>

            {{-- Valor de compra --}}
            <x-ui.input 
                name="valor_compra" 
                label="Valor de Compra (R$)" 
                type="number" 
                step="0.01"
                placeholder="0,00"
            />

            {{-- Foto --}}
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Foto da Ferramenta</label>
                <input 
                    type="file" 
                    name="foto"
                    accept="image/*"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                />
                @error('foto')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Descrição --}}
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Descrição</label>
                <textarea 
                    name="descricao"
                    rows="4"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                ></textarea>
                @error('descricao')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3 mt-6">
                <x-ui.button-primary>Salvar</x-ui.button-primary>
                <x-ui.button-secondary href="{{ route('ferramentas.index') }}">Voltar</x-ui.button-secondary>
            </div>
        </form>

    </div>

</x-app-layout>
