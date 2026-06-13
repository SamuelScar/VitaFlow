<?php

namespace Database\Seeders;

use App\Models\Agendamento;
use App\Models\Campanha;
use App\Models\CarteiraDoacao;
use App\Models\Doacao;
use App\Models\EstoqueSangue;
use App\Models\LocalColeta;
use App\Models\User;
use App\Support\TipoSanguineo;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    private const TOTAL_DOADORES = 150;
    private const TOTAL_LOCAIS = 30;
    private const CAMPANHAS_POR_LOCAL = 2;
    private const AGENDAMENTOS_POR_DOADOR_ATIVO = 12;

    public function run(): void
    {
        DB::transaction(function (): void {
            $this->call(AdminUserSeeder::class);

            Model::unguarded(function (): void {
                $hoje = CarbonImmutable::today();
                $admin = User::where('email', 'admin@vitaflow.local')->firstOrFail();
                $carteiras = $this->criarDoadores($hoje);
                $locais = $this->criarLocaisColeta();

                $this->criarEstoques($locais);

                $campanhas = $this->criarCampanhas($admin, $locais, $hoje);

                $this->criarAgendamentosEDoacoes($carteiras, $campanhas, $hoje);
            });
        });
    }

    /**
     * @return array<int, CarteiraDoacao>
     */
    private function criarDoadores(CarbonImmutable $hoje): array
    {
        $nomes = [
            'Ana', 'Bruno', 'Camila', 'Daniel', 'Eduarda',
            'Felipe', 'Gabriela', 'Henrique', 'Isabela', 'Joao',
            'Larissa', 'Marcos', 'Natalia', 'Pedro', 'Rafaela',
        ];
        $sobrenomes = [
            'Almeida', 'Barbosa', 'Costa', 'Dias', 'Ferreira',
            'Gomes', 'Lima', 'Melo', 'Oliveira', 'Silva',
        ];
        $cidades = $this->cidades();
        $tiposSanguineos = TipoSanguineo::values();
        $senha = Hash::make('Doador@123');
        $carteiras = [];

        for ($indice = 0; $indice < self::TOTAL_DOADORES; $indice++) {
            $numero = $indice + 1;
            $usuario = User::updateOrCreate(
                ['email' => sprintf('doador%03d@vitaflow.local', $numero)],
                [
                    'name' => $nomes[$indice % count($nomes)].' '.$sobrenomes[intdiv($indice, count($nomes))],
                    'tipo' => User::TIPO_DOADOR,
                    'cpf' => sprintf('9%010d', $numero),
                    'telefone' => sprintf('(81) 9%04d-%04d', intdiv($numero, 10000), $numero % 10000),
                    'data_nascimento' => $hoje->subYears(20 + ($indice % 36))->subDays($indice % 300),
                    'tipo_sanguineo' => $tiposSanguineos[$indice % count($tiposSanguineos)],
                    'peso' => 55.5 + (($indice * 7) % 36),
                    'cidade' => $cidades[$indice % count($cidades)]['nome'],
                    'email_verified_at' => $hoje,
                    'password' => $senha,
                ],
            );

            $carteiras[] = CarteiraDoacao::updateOrCreate(
                ['user_id' => $usuario->id],
                [
                    'status' => match ($numero % 25) {
                        0 => 'bloqueada',
                        1 => 'inativa',
                        default => 'ativa',
                    },
                    'emitida_em' => $hoje->subDays(30 + ($indice % 330)),
                ],
            );
        }

        return $carteiras;
    }

    /**
     * @return array<int, LocalColeta>
     */
    private function criarLocaisColeta(): array
    {
        $cidades = $this->cidades();
        $locais = [];

        for ($indice = 0; $indice < self::TOTAL_LOCAIS; $indice++) {
            $cidade = $cidades[$indice % count($cidades)];
            $unidade = intdiv($indice, count($cidades)) + 1;

            $locais[] = LocalColeta::updateOrCreate(
                ['nome' => "Unidade VitaFlow {$cidade['nome']} {$unidade}"],
                [
                    'cep' => sprintf('%05d-%03d', 50000 + (($indice % count($cidades)) * 100), $unidade),
                    'logradouro' => $cidade['logradouro'],
                    'numero' => (string) (100 + ($unidade * 50)),
                    'bairro' => $cidade['bairro'],
                    'cidade' => $cidade['nome'],
                    'uf' => 'PE',
                    'complemento' => "Bloco {$unidade}",
                    'capacidade_diaria' => 40 + (($indice % 6) * 20),
                ],
            );
        }

        return $locais;
    }

    /**
     * @param array<int, LocalColeta> $locais
     */
    private function criarEstoques(array $locais): void
    {
        $tiposSanguineos = TipoSanguineo::values();

        foreach ($locais as $indiceLocal => $local) {
            foreach ($tiposSanguineos as $indiceTipo => $tipoSanguineo) {
                $bolsas = 2 + (($indiceLocal + ($indiceTipo * 3)) % 19);

                EstoqueSangue::updateOrCreate(
                    [
                        'local_coleta_id' => $local->id,
                        'tipo_sanguineo' => $tipoSanguineo,
                    ],
                    [
                        'quantidade_ml' => $bolsas * 450,
                        'bolsas_disponiveis' => $bolsas,
                        'estoque_minimo_ml' => 4500 + (($indiceTipo % 3) * 900),
                    ],
                );
            }
        }
    }

    /**
     * @param array<int, LocalColeta> $locais
     * @return array<int, Campanha>
     */
    private function criarCampanhas(User $admin, array $locais, CarbonImmutable $hoje): array
    {
        $tiposSanguineos = TipoSanguineo::values();
        $campanhas = [];

        foreach ($locais as $indiceLocal => $local) {
            for ($numeroLocal = 1; $numeroLocal <= self::CAMPANHAS_POR_LOCAL; $numeroLocal++) {
                $indice = ($indiceLocal * self::CAMPANHAS_POR_LOCAL) + $numeroLocal - 1;
                $cenario = $this->cenarioCampanha($indice, $hoje);
                $titulo = sprintf('Campanha VitaFlow %03d - %s', $indice + 1, $local->cidade);

                $campanhas[] = Campanha::updateOrCreate(
                    ['titulo' => $titulo],
                    [
                        'criada_por_id' => $admin->id,
                        'local_coleta_id' => $local->id,
                        'descricao' => "Mobilizacao para ampliar as doacoes de sangue em {$local->cidade} e manter os estoques preparados para atendimentos.",
                        'tipos_sanguineos_alvo' => $indice % 4 === 0
                            ? null
                            : [
                                $tiposSanguineos[$indice % count($tiposSanguineos)],
                                $tiposSanguineos[($indice + 3) % count($tiposSanguineos)],
                            ],
                        'meta_bolsas' => 40 + (($indice % 9) * 15),
                        'data_inicio' => $cenario['data_inicio'],
                        'data_fim' => $cenario['data_fim'],
                        'status' => $cenario['status'],
                    ],
                );
            }
        }

        return $campanhas;
    }

    /**
     * @param array<int, CarteiraDoacao> $carteiras
     * @param array<int, Campanha> $campanhas
     */
    private function criarAgendamentosEDoacoes(
        array $carteiras,
        array $campanhas,
        CarbonImmutable $hoje,
    ): void {
        $carteirasAtivas = array_values(array_filter(
            $carteiras,
            fn (CarteiraDoacao $carteira): bool => $carteira->status === 'ativa',
        ));

        foreach ($carteirasAtivas as $indiceCarteira => $carteira) {
            for ($numero = 0; $numero < self::AGENDAMENTOS_POR_DOADOR_ATIVO; $numero++) {
                $indiceCampanha = (($indiceCarteira * self::AGENDAMENTOS_POR_DOADOR_ATIVO) + $numero) % count($campanhas);
                $campanha = $campanhas[$indiceCampanha];
                $status = $this->statusAgendamento($campanha, $indiceCarteira + $numero, $hoje);
                $dataHora = $this->dataHoraAgendamento($campanha, $numero, $hoje);

                $agendamento = Agendamento::updateOrCreate(
                    [
                        'user_id' => $carteira->user_id,
                        'campanha_id' => $campanha->id,
                    ],
                    [
                        'data_hora' => $dataHora,
                        'status' => $status,
                    ],
                );

                if ($status !== 'realizado') {
                    continue;
                }

                $recusada = ($indiceCarteira + $numero) % 5 === 0;

                Doacao::updateOrCreate(
                    ['agendamento_id' => $agendamento->id],
                    [
                        'data_coleta' => $dataHora->addMinutes(20),
                        'quantidade_ml' => $recusada ? null : 450,
                        'status' => $recusada ? 'recusada' : 'confirmada',
                        'motivo_recusa' => $recusada
                            ? 'Doador temporariamente inapto apos a triagem.'
                            : null,
                    ],
                );
            }
        }
    }

    /**
     * @return array{data_inicio: CarbonImmutable, data_fim: CarbonImmutable, status: string}
     */
    private function cenarioCampanha(int $indice, CarbonImmutable $hoje): array
    {
        return match ($indice % 6) {
            0 => [
                'data_inicio' => $hoje->subDays(14),
                'data_fim' => $hoje->addDays(45),
                'status' => 'ativa',
            ],
            1 => [
                'data_inicio' => $hoje->subDays(7),
                'data_fim' => $hoje->addDays(30),
                'status' => 'ativa',
            ],
            2 => [
                'data_inicio' => $hoje->addDays(7),
                'data_fim' => $hoje->addDays(40),
                'status' => 'ativa',
            ],
            3 => [
                'data_inicio' => $hoje->subDays(75),
                'data_fim' => $hoje->subDays(45),
                'status' => 'encerrada',
            ],
            4 => [
                'data_inicio' => $hoje->subDays(10),
                'data_fim' => $hoje->addDays(20),
                'status' => 'cancelada',
            ],
            default => [
                'data_inicio' => $hoje->subDays(2),
                'data_fim' => $hoje->addDays(60),
                'status' => 'ativa',
            ],
        };
    }

    private function statusAgendamento(Campanha $campanha, int $indice, CarbonImmutable $hoje): string
    {
        if ($campanha->status === 'encerrada') {
            return $indice % 4 === 0 ? 'faltou' : 'realizado';
        }

        if ($campanha->status === 'cancelada') {
            return 'cancelado';
        }

        if ($campanha->data_inicio->isAfter($hoje)) {
            return 'agendado';
        }

        return $indice % 8 === 0 ? 'cancelado' : 'agendado';
    }

    private function dataHoraAgendamento(
        Campanha $campanha,
        int $numero,
        CarbonImmutable $hoje,
    ): CarbonImmutable {
        if ($campanha->status === 'encerrada') {
            return CarbonImmutable::parse($campanha->data_inicio)
                ->addDays(5 + $numero)
                ->setTime(8 + $numero, 0);
        }

        if ($campanha->data_inicio->isAfter($hoje)) {
            return CarbonImmutable::parse($campanha->data_inicio)
                ->addDays(1 + $numero)
                ->setTime(8 + $numero, 0);
        }

        return $hoje->addDays(1 + $numero)->setTime(8 + $numero, 0);
    }

    /**
     * @return array<int, array{nome: string, logradouro: string, bairro: string}>
     */
    private function cidades(): array
    {
        return [
            ['nome' => 'Recife', 'logradouro' => 'Avenida da Saude', 'bairro' => 'Boa Vista'],
            ['nome' => 'Olinda', 'logradouro' => 'Avenida das Flores', 'bairro' => 'Bairro Novo'],
            ['nome' => 'Jaboatao dos Guararapes', 'logradouro' => 'Rua da Esperanca', 'bairro' => 'Piedade'],
            ['nome' => 'Paulista', 'logradouro' => 'Avenida Central', 'bairro' => 'Janga'],
            ['nome' => 'Camaragibe', 'logradouro' => 'Rua da Solidariedade', 'bairro' => 'Centro'],
            ['nome' => 'Caruaru', 'logradouro' => 'Avenida Agamenon Magalhaes', 'bairro' => 'Mauricio de Nassau'],
            ['nome' => 'Petrolina', 'logradouro' => 'Avenida do Sao Francisco', 'bairro' => 'Centro'],
            ['nome' => 'Garanhuns', 'logradouro' => 'Rua das Acacias', 'bairro' => 'Heliopolis'],
            ['nome' => 'Vitoria de Santo Antao', 'logradouro' => 'Avenida Mariana Amalia', 'bairro' => 'Matriz'],
            ['nome' => 'Goiana', 'logradouro' => 'Rua Direita', 'bairro' => 'Centro'],
        ];
    }
}
