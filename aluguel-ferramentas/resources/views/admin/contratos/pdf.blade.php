<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Contrato {{ $contrato->numero }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        h1 { text-align:center; }
        table { width:100%; border-collapse: collapse; margin-top:20px; }
        th, td { border:1px solid #333; padding:8px; }
    </style>
</head>
<body>
    <h1>Contrato de Empréstimo Nº {{ $contrato->numero }}</h1>

    <p><strong>Nome:</strong> {{ $contrato->aluguel->usuario->name }}</p>
    <p><strong>Casa:</strong> {{ $contrato->aluguel->casa->nome }}</p>
    <p><strong>Data da Retirada:</strong> {{ $contrato->aluguel->data_retirada }}</p>
    <p><strong>Data Prevista:</strong> {{ $contrato->aluguel->data_prevista }}</p>

    <h3>Itens</h3>
    <table>
        <thead>
            <tr>
                <th>Ferramenta</th>
                <th>Quantidade</th>
                <th>Obs</th>
            </tr>
        </thead>
        <tbody>
            @foreach($contrato->itens as $item)
            <tr>
                <td>{{ $item->nome_ferramenta }}</td>
                <td>{{ $item->quantidade }}</td>
                <td>{{ $item->observacao }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
