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
        .section-title {
            color: #dc3545;
            font-size: 17px;
            margin: 25px 0 12px;
            border-bottom: 1px solid #f1b0b7;
            padding-bottom: 6px;
        }
        .cards {
            width: 100%;
            margin-bottom: 16px;
        }
        .card-cell {
            width: 25%;
            padding: 5px;
            vertical-align: top;
        }
        .indicator-card {
            border: 1px solid #ddd;
            background-color: #fff;
            padding: 10px;
            min-height: 56px;
        }
        .indicator-title {
            color: #666;
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 4px;
        }
        .indicator-value {
            color: #222;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        .indicator-detail {
            color: #777;
            font-size: 10px;
        }
        .chart-block {
            page-break-inside: avoid;
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 14px;
        }
        .chart-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        .chart-description {
            color: #666;
            font-size: 10px;
            margin-bottom: 10px;
        }
        .chart-image {
            width: 100%;
        }
        .chart-image img {
            width: 100%;
            height: auto;
            display: block;
        }
        .chart-dataset {
            margin-top: 8px;
        }
        .chart-dataset-title {
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 5px;
        }
        .chart-row {
            clear: both;
            margin-bottom: 4px;
            height: 14px;
        }
        .chart-label {
            float: left;
            width: 30%;
            font-size: 9px;
            color: #555;
            white-space: nowrap;
            overflow: hidden;
        }
        .chart-bar-wrap {
            float: left;
            width: 58%;
            height: 10px;
            background-color: #f1f3f5;
            margin-top: 1px;
        }
        .chart-bar {
            height: 10px;
        }
        .chart-value {
            float: right;
            width: 10%;
            text-align: right;
            font-size: 9px;
            color: #333;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>VitaFlow</h1>
        <p>{{ $titulo }}</p>
        <p style="font-size: 12px; margin-top: 5px;">Gerado em: {{ now()->format('d/m/Y \à\s H:i') }}</p>
    </div>

    @php
        $cards = $painelAnalitico['cards'] ?? [];
        $graficosPdf = $graficosPdf ?? [];
    @endphp

    @if (! empty($cards) || ! empty($graficosPdf))
        <h2 class="section-title">Painel analítico</h2>

        @if (! empty($cards))
            <table class="cards">
                <tbody>
                    @foreach (array_chunk($cards, 4) as $linhaCards)
                        <tr>
                            @foreach ($linhaCards as $card)
                                <td class="card-cell">
                                    <div class="indicator-card">
                                        <div class="indicator-title">{{ $card['titulo'] }}</div>
                                        <div class="indicator-value">{{ $card['valor'] }}</div>
                                        <div class="indicator-detail">{{ $card['detalhe'] }}</div>
                                    </div>
                                </td>
                            @endforeach
                            @for ($i = count($linhaCards); $i < 4; $i++)
                                <td class="card-cell"></td>
                            @endfor
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @foreach ($graficosPdf as $grafico)
            <div class="chart-block">
                <div class="chart-title">{{ $grafico['titulo'] }}</div>
                <div class="chart-description">{{ $grafico['descricao'] }}</div>

                @if (! empty($grafico['imagem']))
                    <div class="chart-image"><img src="{{ $grafico['imagem'] }}" alt="{{ $grafico['titulo'] }}"></div>
                @else
                    @foreach ($grafico['datasets'] as $dataset)
                        @php
                            $maiorValor = max(1, (int) collect($dataset['data'] ?? [])->max());
                            $cor = $dataset['color'] ?? '#dc3545';
                        @endphp
                        <div class="chart-dataset">
                            <div class="chart-dataset-title">{{ $dataset['label'] }}</div>
                            @foreach ($grafico['labels'] as $index => $label)
                                @php
                                    $valor = (int) ($dataset['data'][$index] ?? 0);
                                    $largura = min(100, (int) round(($valor / $maiorValor) * 100));
                                @endphp
                                <div class="chart-row">
                                    <div class="chart-label">{{ $label }}</div>
                                    <div class="chart-bar-wrap">
                                        <div class="chart-bar" style="width: {{ $largura }}%; background-color: {{ $cor }};"></div>
                                    </div>
                                    <div class="chart-value">{{ number_format($valor, 0, ',', '.') }}</div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                @endif
            </div>
        @endforeach
    @endif

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
