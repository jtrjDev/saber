# saber
SABER - Sistema de Aluguel de ferramentas



# comandos a utilizar

✅ ETAPA 0 — Instalar autenticação (Login + Registro + Dashboard)
1. Instalar o Laravel Breeze
composer require laravel/breeze --dev

2. Gerar estrutura com Blade (recomendado)
php artisan breeze:install blade


Isso cria:

/resources/views/auth/login.blade.php

/resources/views/auth/register.blade.php

/resources/views/layouts

/dashboard route

Middleware de autenticação

Logout, Forgot Password, Email Verification (opcional)






# Componentes que compoem o sistema
✅ Componentes globais (Blade Components)

Botão Salvar

Botão Excluir

Botão Voltar

Botão Editar

Botão Novo

Badge (status)

Alerta de sucesso/erro

✅ Componentes de Formulário

Input padrão

Textarea

Select

Input com label embutido

Field group

✅ Componentes de Tabela

Tabela responsiva

Cabeçalho padrão

Linhas padronizadas

Ações por linha

✅ Sistema de Filtros globais

Campo de pesquisa

Select de setores

Select de status

Botão de “limpar filtro”

Como usar botão primario
<x-ui.button-primary>Salvar</x-ui.button-primary>

Botão secundário
<x-ui.button-secondary href="{{ route('setores.index') }}">Voltar</x-ui.button-secondary>

Inputs
<x-ui.input label="Nome" name="nome" required />

Selects
<x-ui.select name="setor_id" label="Setor">
    <option value="">Selecione…</option>
    @foreach($setores as $setor)
        <option value="{{ $setor->id }}">{{ $setor->nome }}</option>
    @endforeach
</x-ui.select>

Tables
<x-ui.table>
    <x-slot:head>
        <th class="px-4 py-2 text-left">Nome</th>
        <th class="px-4 py-2">Ações</th>
    </x-slot:head>

    @foreach($setores as $setor)
        <tr>
            <td class="px-4 py-2">{{ $setor->nome }}</td>
            <td class="px-4 py-2">
                <x-ui.button-secondary href="#">Editar</x-ui.button-secondary>
            </td>
        </tr>
    @endforeach
</x-ui.table>

Filters
<form method="GET">
    <x-ui.filter-box>
        <x-ui.select name="setor" label="Setor">
            <option value="">Todos</option>
            @foreach($setores as $setor)
                <option value="{{ $setor->id }}">{{ $setor->nome }}</option>
            @endforeach
        </x-ui.select>
    </x-ui.filter-box>
</form>


# Pesquisas em tinker
print_r(DB::select("SELECT name FROM sqlite_master WHERE type='table'"));

