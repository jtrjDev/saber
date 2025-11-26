<x-app-layout>

    <header class="mb-6 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
            Editar Ferramenta
        </h1>
    </header>


    <div class="bg-white shadow-lg rounded-xl p-6 lg:p-8 max-w-4xl mx-auto">
@if($ferramenta->estado === 'manutenção')
    <div class="mb-6 p-4 bg-orange-100 border-l-4 border-orange-500 rounded">
        <h2 class="font-bold text-orange-700">⚠ Ferramenta em Manutenção</h2>
        <p class="text-orange-600 text-sm">
            Esta ferramenta está marcada como <strong>em manutenção</strong>.
            Ela não poderá ser alugada até que o estado seja alterado.
        </p>
    </div>
@endif
        <form action="{{ route('ferramentas.update', $ferramenta) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Nome --}}
            <x-ui.input 
                name="nome" 
                label="Nome da Ferramenta" 
                value="{{ $ferramenta->nome }}" 
                required 
            />

            {{-- Setor --}}
            <x-ui.select name="setor_id" label="Setor" required>
                @foreach($setores as $setor)
                    <option 
                        value="{{ $setor->id }}"
                        @selected($ferramenta->setor_id == $setor->id)
                    >
                        {{ $setor->nome }}
                    </option>
                @endforeach
            </x-ui.select>

            {{-- Estado --}}
            <x-ui.select name="estado" label="Estado" required>
                <option value="bom" @selected($ferramenta->estado === 'bom')>Bom</option>
                <option value="ruim" @selected($ferramenta->estado === 'ruim')>Ruim</option>
                <option value="manutenção" @selected($ferramenta->estado === 'manutenção')>Manutenção</option>
                <option value="quebrado" @selected($ferramenta->estado === 'quebrado')>Quebrado</option>
            </x-ui.select>


            {{-- Valor de compra --}}
            <x-ui.input 
                name="valor_compra" 
                label="Valor de Compra (R$)" 
                type="number" 
                step="0.01"
                value="{{ $ferramenta->valor_compra }}"
            />

            {{-- Foto --}}
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Foto da Ferramenta</label>

                {{-- Preview --}}
                @if($ferramenta->foto)
                    <div class="mb-3">
                        <img src="{{ asset('storage/'.$ferramenta->foto) }}" class="h-24 rounded shadow">
                    </div>
                @endif

                <input 
                    type="file" 
                    name="foto"
                    accept="image/*"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                />
            </div>

            {{-- Descrição --}}
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Descrição</label>
                <textarea 
                    name="descricao"
                    rows="4"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                >{{ $ferramenta->descricao }}</textarea>
            </div>

            <div class="flex gap-3 mt-6">
                <x-ui.button-primary>Salvar</x-ui.button-primary>
                <x-ui.button-secondary href="{{ route('ferramentas.index') }}">Voltar</x-ui.button-secondary>
            </div>
        </form>

    </div>

</x-app-layout>
