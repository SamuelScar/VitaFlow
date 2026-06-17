<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>{{ $titulo }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #dc3545;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #dc3545;
            margin: 0 0 5px 0;
            font-size: 24px;
        }
        .header p {
            margin: 0;
            color: #666;
            font-size: 14px;
        }
        .modulo-title {
            color: #0d6efd;
            font-size: 16px;
            margin-top: 30px;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #495057;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #999;
        }
        .no-data {
            text-align: center;
            font-style: italic;
            color: #777;
            padding: 15px;
            border: 1px dashed #ccc;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>VitaFlow</h1>
        <p>{{ $titulo }}</p>
        <p style="font-size: 12px; margin-top: 5px;">Gerado em: {{ now()->format('d/m/Y \à\s H:i') }}</p>
    </div>

    @forelse ($modulosSelecionados as $modulo)
        @php
            $colunas = $builder->colunasSelecionadas[$modulo] ?? [];
            $nomeModulo = $builder->getModulos()[$modulo];
        @endphp

        <h2 class="modulo-title">{{ $nomeModulo }}</h2>

        @if (empty($colunas))
            <div class="no-data">Nenhuma coluna selecionada para {{ $nomeModulo }}.</div>
        @else
            <table>
                <thead>
                    <tr>
                        @foreach ($builder->getOpcoesColunas($modulo) as $chave => $label)
                            @if (in_array($chave, $colunas))
                                <th>{{ $label }}</th>
                            @endif
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dadosPorModulo[$modulo] as $linha)
                        <tr>
                            @foreach ($builder->getOpcoesColunas($modulo) as $chave => $label)
                                @if (in_array($chave, $colunas))
                                    <td>{{ $builder->formatarValor($linha, $modulo, $chave) }}</td>
                                @endif
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($colunas) }}" style="text-align: center;">Nenhum registro encontrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @endif
    @empty
        <div class="no-data">Nenhum módulo selecionado para o relatório.</div>
    @endforelse

    <div class="footer">
        Página gerada pelo sistema VitaFlow.
    </div>

</body>
</html>
