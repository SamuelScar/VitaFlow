<?php

namespace App\Livewire\Admin;

use App\Models\Campanha;
use Carbon\CarbonImmutable;
use Illuminate\View\View;
use Livewire\Component;

class CampanhaJanelas extends Component
{
    public Campanha $campanha;
    public string $dataSelecionada = '';

    public function mount(Campanha $campanha): void
    {
        $this->campanha = $campanha;

        $agora = CarbonImmutable::now()->startOfDay();
        $inicio = CarbonImmutable::instance($campanha->data_inicio)->startOfDay();
        $fim = CarbonImmutable::instance($campanha->data_fim)->startOfDay();

        if ($agora->between($inicio, $fim)) {
            $this->dataSelecionada = $agora->format('Y-m-d');
        } else {
            $this->dataSelecionada = $inicio->format('Y-m-d');
        }
    }

    public function setData(string $data): void
    {
        $this->dataSelecionada = $data;
    }

    /**
     * @return array<int, array{horario: string, total_vagas: int, vagas_ocupadas: int, vagas_livres: int, percentual_ocupacao: float, status: string}>
     */
    public function getJanelasProperty(): array
    {
        $inicio = CarbonImmutable::instance($this->campanha->data_inicio)->startOfDay();
        $fim = CarbonImmutable::instance($this->campanha->data_fim)->startOfDay();
        
        $ocupacao = $this->campanha->agendamentos()
            ->where('status', 'agendado')
            ->whereBetween('data_hora', [$inicio, $fim->copy()->endOfDay()])
            ->selectRaw('data_hora, count(*) as total')
            ->groupBy('data_hora')
            ->pluck('total', 'data_hora')
            ->map(fn (mixed $total) => (int) $total);

        $janelas = [];
        $vagasTotais = $this->campanha->agendamentos_por_horario;

        for ($dia = $inicio; $dia->lte($fim); $dia = $dia->addDay()) {
            $inicioDia = $this->dataComHorario($dia, $this->horarioInicio($this->campanha));
            $fimDia = $this->dataComHorario($dia, $this->horarioFim($this->campanha));

            for ($dataHora = $inicioDia; $dataHora->lte($fimDia); $dataHora = $dataHora->addMinutes(30)) {
                $chave = $dataHora->format('Y-m-d H:i:s');
                $ocupados = (int) ($ocupacao[$chave] ?? 0);
                $livres = max(0, $vagasTotais - $ocupados);
                
                $percentual = $vagasTotais > 0 ? ($ocupados / $vagasTotais) * 100 : 0;
                
                $status = 'verde';
                if ($percentual >= 100) {
                    $status = 'vermelho';
                } elseif ($percentual >= 75) {
                    $status = 'amarelo';
                }

                $janelas[] = [
                    'valor' => $dataHora->format('Y-m-d\TH:i'),
                    'horario' => $dataHora->format('H:i'),
                    'total_vagas' => $vagasTotais,
                    'vagas_ocupadas' => $ocupados,
                    'vagas_livres' => $livres,
                    'status' => $status,
                ];
            }
        }

        return $janelas;
    }

    /**
     * @return array<string, string>
     */
    public function getDiasDisponiveisProperty(): array
    {
        $inicio = CarbonImmutable::instance($this->campanha->data_inicio)->startOfDay();
        $fim = CarbonImmutable::instance($this->campanha->data_fim)->startOfDay();
        
        $dias = [];
        for ($dia = $inicio; $dia->lte($fim); $dia = $dia->addDay()) {
            $dias[$dia->format('Y-m-d')] = $dia->format('d/m/Y');
        }
        
        return $dias;
    }

    private function dataComHorario(CarbonImmutable $data, string $horario): CarbonImmutable
    {
        return CarbonImmutable::parse("{$data->format('Y-m-d')} {$horario}");
    }

    private function horarioInicio(Campanha $campanha): string
    {
        return substr((string) ($campanha->horario_inicio ?? '08:00'), 0, 5);
    }

    private function horarioFim(Campanha $campanha): string
    {
        return substr((string) ($campanha->horario_fim ?? '17:00'), 0, 5);
    }

    public function render(): View
    {
        return view('livewire.admin.campanha-janelas');
    }
}
