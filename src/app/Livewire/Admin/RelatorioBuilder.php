<?php

namespace App\Livewire\Admin;

use App\Models\Agendamento;
use App\Models\BolsaSangue;
use App\Models\Campanha;
use App\Models\LocalColeta;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RelatorioBuilder extends Component
{
    public array $modulosSelecionados = ['agendamentos'];
    public array $colunasSelecionadas = [];

    // Filtros
    public string $filtroDataInicio = '';
    public string $filtroDataFim = '';
    public array $filtroStatusAgendamento = [];
    public array $filtroStatusBolsa = [];
    public array $filtroStatusCampanha = [];
    public array $filtroTipoSanguineo = [];
    public array $filtroLocalColeta = [];
    
    public bool $mostrarTudo = false;

    public function mount(): void
    {
        $this->setDefaultColumns();
    }

    public function toggleMostrarTudo(): void
    {
        $this->mostrarTudo = !$this->mostrarTudo;
    }

    public function updatedModulosSelecionados(): void
    {
        $this->setDefaultColumns();
    }

    private function setDefaultColumns(): void
    {
        foreach ($this->modulosSelecionados as $modulo) {
            if (!isset($this->colunasSelecionadas[$modulo])) {
                $this->colunasSelecionadas[$modulo] = array_keys($this->getOpcoesColunas($modulo));
            }
        }
    }

    public function getModulos(): array
    {
        return [
            'agendamentos' => 'Agendamentos e Comparecimentos',
            'bolsas' => 'Bolsas de Sangue (Estoque)',
            'campanhas' => 'Campanhas de Doação',
            'doadores' => 'Doadores Cadastrados',
        ];
    }

    public function getOpcoesColunas(string $modulo): array
    {
        return match ($modulo) {
            'agendamentos' => [
                'id' => 'ID',
                'doador' => 'Doador',
                'data_hora' => 'Data / Hora',
                'campanha' => 'Campanha',
                'local' => 'Local de Coleta',
                'status' => 'Status',
            ],
            'bolsas' => [
                'id' => 'ID da Bolsa',
                'tipo_sanguineo' => 'Tipo Sanguíneo',
                'local' => 'Local Atual',
                'quantidade' => 'Qtd (ml)',
                'data_coleta' => 'Data de Coleta',
                'validade' => 'Validade',
                'status' => 'Status',
            ],
            'campanhas' => [
                'id' => 'ID',
                'titulo' => 'Título',
                'local' => 'Local',
                'status' => 'Status',
                'periodo' => 'Período',
                'meta' => 'Meta (bolsas)',
            ],
            'doadores' => [
                'id' => 'ID',
                'nome' => 'Nome',
                'email' => 'E-mail',
                'tipo_sanguineo' => 'Tipo Sanguíneo',
            ],
            default => [],
        };
    }

    public function getFiltrosDisponiveis(): array
    {
        $filtros = [];
        foreach ($this->modulosSelecionados as $modulo) {
            $f = match ($modulo) {
                'agendamentos' => ['datas', 'status_agendamento', 'local'],
                'bolsas' => ['tipo_sanguineo', 'status_bolsa', 'local'],
                'campanhas' => ['datas', 'status_campanha'],
                'doadores' => ['tipo_sanguineo'],
                default => [],
            };
            $filtros = array_merge($filtros, $f);
        }
        return array_unique($filtros);
    }

    public function getLocaisColeta(): Collection
    {
        return LocalColeta::orderBy('nome')->pluck('nome', 'id');
    }

    public function getTiposSanguineos(): array
    {
        return ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
    }

    public function getStatusAgendamento(): array
    {
        return [
            Agendamento::STATUS_AGENDADO => 'Agendado',
            Agendamento::STATUS_REALIZADO => 'Realizado',
            Agendamento::STATUS_FALTOU => 'Faltou',
            Agendamento::STATUS_CANCELADO => 'Cancelado',
        ];
    }

    public function getStatusBolsa(): array
    {
        return [
            BolsaSangue::STATUS_DISPONIVEL => 'Disponível',
            BolsaSangue::STATUS_UTILIZADA => 'Utilizada',
            BolsaSangue::STATUS_DESCARTADA => 'Descartada',
            BolsaSangue::STATUS_VENCIDA => 'Vencida',
            BolsaSangue::STATUS_TRANSFERIDA => 'Transferida',
        ];
    }

    public function getStatusCampanha(): array
    {
        return [
            'ativa' => 'Ativa',
            'encerrada' => 'Encerrada',
            'cancelada' => 'Cancelada',
        ];
    }

    private function getQuery(string $modulo)
    {
        if ($modulo === 'agendamentos') {
            return Agendamento::with(['user', 'campanha.localColeta'])
                ->when($this->filtroDataInicio !== '', fn ($q) => $q->whereDate('data_hora', '>=', $this->filtroDataInicio))
                ->when($this->filtroDataFim !== '', fn ($q) => $q->whereDate('data_hora', '<=', $this->filtroDataFim))
                ->when(!empty($this->filtroStatusAgendamento), fn ($q) => $q->whereIn('agendamentos.status', $this->filtroStatusAgendamento))
                ->when(!empty($this->filtroLocalColeta), fn ($q) => $q->whereHas('campanha', fn ($qc) => $qc->whereIn('local_coleta_id', $this->filtroLocalColeta)))
                ->orderByDesc('data_hora');
        }

        if ($modulo === 'bolsas') {
            return BolsaSangue::with(['doacao.agendamento.campanha.localColeta'])
                ->when(!empty($this->filtroTipoSanguineo), fn ($q) => $q->whereIn('tipo_sanguineo', $this->filtroTipoSanguineo))
                ->when(!empty($this->filtroStatusBolsa), fn ($q) => $q->whereIn('bolsas_sangue.status', $this->filtroStatusBolsa))
                ->orderByDesc('data_coleta');
        }

        if ($modulo === 'campanhas') {
            return Campanha::with(['localColeta'])
                ->when($this->filtroDataInicio !== '', fn ($q) => $q->whereDate('data_inicio', '>=', $this->filtroDataInicio))
                ->when($this->filtroDataFim !== '', fn ($q) => $q->whereDate('data_fim', '<=', $this->filtroDataFim))
                ->when(!empty($this->filtroStatusCampanha), fn ($q) => $q->whereIn('campanhas.status', $this->filtroStatusCampanha))
                ->orderByDesc('data_inicio');
        }

        if ($modulo === 'doadores') {
            return User::query()
                ->where('tipo', User::TIPO_DOADOR)
                ->when(!empty($this->filtroTipoSanguineo), fn ($q) => $q->whereIn('tipo_sanguineo', $this->filtroTipoSanguineo))
                ->orderBy('name');
        }

        return Agendamento::query();
    }

    public function getDados(): array
    {
        $dadosPorModulo = [];
        foreach ($this->modulosSelecionados as $modulo) {
            $dadosPorModulo[$modulo] = $this->getQuery($modulo)->get();
        }
        return $dadosPorModulo;
    }

    public function exportarCsv(): StreamedResponse
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', '300');

        $dadosPorModulo = $this->getDados();
        $nomeArquivo = 'relatorio_multiplo_' . date('Y-m-d_H-i-s') . '.csv';

        return response()->streamDownload(function () use ($dadosPorModulo) {
            $handle = fopen('php://output', 'w');
            
            // BOM to fix UTF-8 in Excel
            fputs($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            
            foreach ($this->modulosSelecionados as $modulo) {
                $colunasAtivas = array_filter(
                    $this->getOpcoesColunas($modulo),
                    fn ($chave) => in_array($chave, $this->colunasSelecionadas[$modulo] ?? []),
                    ARRAY_FILTER_USE_KEY
                );

                fputcsv($handle, [$this->getModulos()[$modulo]], ';'); // Titulo do modulo
                fputcsv($handle, array_values($colunasAtivas), ';');

                foreach ($dadosPorModulo[$modulo] as $linha) {
                    $linhaFormatada = [];
                    foreach (array_keys($colunasAtivas) as $coluna) {
                        $linhaFormatada[] = $this->formatarValor($linha, $modulo, $coluna);
                    }
                    fputcsv($handle, $linhaFormatada, ';');
                }
                
                fputcsv($handle, [], ';'); // Linha em branco separadora
            }

            fclose($handle);
        }, $nomeArquivo);
    }

    public function exportarPdf()
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', '300');

        $dadosPorModulo = $this->getDados();

        $pdf = Pdf::loadView('admin.relatorios.pdf', [
            'titulo' => 'Relatório Consolidado',
            'modulosSelecionados' => $this->modulosSelecionados,
            'dadosPorModulo' => $dadosPorModulo,
            'builder' => $this,
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'relatorio_consolidado.pdf');
    }

    public function formatarValor($modelo, string $modulo, string $coluna): string
    {
        if ($modulo === 'agendamentos') {
            return match ($coluna) {
                'id' => (string) $modelo->id,
                'doador' => $modelo->user?->name ?? '-',
                'data_hora' => $modelo->data_hora ? $modelo->data_hora->format('d/m/Y H:i') : '-',
                'campanha' => $modelo->campanha?->titulo ?? '-',
                'local' => $modelo->campanha?->localColeta?->nome ?? '-',
                'status' => ucfirst($modelo->status),
                default => '-',
            };
        }

        if ($modulo === 'bolsas') {
            return match ($coluna) {
                'id' => (string) $modelo->id,
                'tipo_sanguineo' => $modelo->tipo_sanguineo ?? '-',
                'local' => $modelo->doacao?->agendamento?->campanha?->localColeta?->nome ?? 'Desconhecido',
                'quantidade' => (string) $modelo->quantidade_ml,
                'data_coleta' => $modelo->data_coleta ? $modelo->data_coleta->format('d/m/Y') : '-',
                'validade' => $modelo->validade_em ? $modelo->validade_em->format('d/m/Y') : '-',
                'status' => ucfirst($modelo->status),
                default => '-',
            };
        }

        if ($modulo === 'campanhas') {
            return match ($coluna) {
                'id' => (string) $modelo->id,
                'titulo' => $modelo->titulo ?? '-',
                'local' => $modelo->localColeta?->nome ?? '-',
                'status' => ucfirst($modelo->status),
                'periodo' => ($modelo->data_inicio ? $modelo->data_inicio->format('d/m/Y') : '') . ' até ' . ($modelo->data_fim ? $modelo->data_fim->format('d/m/Y') : ''),
                'meta' => (string) $modelo->meta_bolsas,
                default => '-',
            };
        }

        if ($modulo === 'doadores') {
            return match ($coluna) {
                'id' => (string) $modelo->id,
                'nome' => $modelo->name ?? '-',
                'email' => $modelo->email ?? '-',
                'tipo_sanguineo' => $modelo->tipo_sanguineo ?? 'Não informado',
                default => '-',
            };
        }

        return '-';
    }

    public function render(): View
    {
        return view('livewire.admin.relatorio-builder', [
            'dadosPorModulo' => $this->getDados(),
        ]);
    }
}
