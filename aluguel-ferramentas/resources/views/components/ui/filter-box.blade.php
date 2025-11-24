<div class="p-4 bg-gray-50 border border-gray-200 rounded-xl">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">

        {{-- Pesquisa --}}
        <div class="md:col-span-5">
            <x-ui.input name="search" label="Pesquisar" placeholder="Buscar..." />
        </div>

        {{-- Filtros adicionais --}}
        <div class="md:col-span-4">
            {{ $slot }}
        </div>

        {{-- Ações --}}
        <div class="md:col-span-3 flex gap-2 items-end">
            <x-ui.button-primary type="submit" class="w-full">
                Filtrar
            </x-ui.button-primary>

            <x-ui.button-secondary href="{{ url()->current() }}" class="w-full">
                Limpar
            </x-ui.button-secondary>
        </div>

    </div>
</div>
