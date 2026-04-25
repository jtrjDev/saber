{{-- resources/views/admin/contratos/pdf.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Contrato {{ $contrato->numero }}</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #4F46E5;
        }
        .numero {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        .content {
            margin: 30px 0;
        }
        .info-section {
            margin: 20px 0;
            padding: 15px;
            background: #f9fafb;
            border-radius: 8px;
        }
        .info-row {
            margin-bottom: 10px;
        }
        .label {
            font-weight: bold;
            width: 150px;
            display: inline-block;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f3f4f6;
            font-weight: bold;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            text-align: center;
            color: #666;
        }
        .signature {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .sign-box {
            width: 200px;
            text-align: center;
        }
        .sign-line {
            margin-top: 40px;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
        h2 {
            color: #4F46E5;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>CONTRATO DE EMPRÉSTIMO DE FERRAMENTAS</h1>
        <div class="numero">Nº {{ $contrato->numero }}</div>
    </div>

    <div class="content">
        <div class="info-section">
            <div class="info-row">
                <span class="label">CONTRATANTE:</span>
                <span>{{ $contrato->aluguel->usuario->name }}</span>
            </div>
            <div class="info-row">
                <span class="label">CASA DE ORAÇÃO:</span>
                <span>{{ $contrato->aluguel->casa->nome }}</span>
            </div>
            <div class="info-row">
                <span class="label">SETOR:</span>
                <span>{{ $contrato->aluguel->casa->setor->nome ?? 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="label">DATA DE RETIRADA:</span>
                <span>{{ \Carbon\Carbon::parse($contrato->aluguel->data_retirada)->format('d/m/Y') }}</span>
            </div>
            <div class="info-row">
                <span class="label">PREVISÃO DE DEVOLUÇÃO:</span>
                <span>{{ \Carbon\Carbon::parse($contrato->aluguel->data_prevista)->format('d/m/Y') }}</span>
            </div>
        </div>

        <h2>ITENS DO CONTRATO</h2>
        
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Ferramenta</th>
                    <th>Quantidade</th>
                    <th>Observações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($contrato->itens as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->nome_ferramenta }}</td>
                    <td style="text-align: center">{{ $item->quantidade }}</td>
                    <td>{{ $item->observacao ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="info-section">
            <h3>CLÁUSULAS E CONDIÇÕES</h3>
            <p><strong>1. Responsabilidade:</strong> O contratante se responsabiliza pelo uso adequado das ferramentas, comprometendo-se a utilizá-las apenas para os fins a que se destinam.</p>
            <p><strong>2. Devolução:</strong> As ferramentas deverão ser devolvidas na data prevista, em boas condições de uso, sujeito a multas em caso de atraso.</p>
            <p><strong>3. Danos e Perdas:</strong> Em caso de dano ou perda de qualquer ferramenta, o contratante arcará com os custos de reparo ou reposição.</p>
            <p><strong>4. Renovação:</strong> A renovação do contrato está sujeita à disponibilidade das ferramentas e aprovação da administração.</p>
            <p><strong>5. Foro:</strong> Fica eleito o foro da comarca de Ibiporã para dirimir quaisquer questões oriundas deste contrato.</p>
        </div>

        <div class="signature">
            <div class="sign-box">
                <div class="sign-line"></div>
                <p>{{ $contrato->aluguel->usuario->name }}</p>
                <p>CONTRATANTE</p>
            </div>
            <div class="sign-box">
                <div class="sign-line"></div>
                <p>{{ $contrato->aluguel->responsavel->name }}</p>
                <p>RESPONSÁVEL</p>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Documento gerado eletronicamente em {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Contrato válido de acordo com os termos estabelecidos acima.</p>
    </div>
</body>
</html>