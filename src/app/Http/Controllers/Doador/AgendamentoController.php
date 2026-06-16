<?php

namespace App\Http\Controllers\Doador;

use App\Http\Controllers\Controller;
use App\Models\Agendamento;
use App\Models\Campanha;
use App\Models\User;
use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

/**
 * Gerencia o agendamento de doacao feito pelo doador.
 */
class AgendamentoController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        assert($user !== null);

        $agendamentosAtivos = $user->agendamentos()
            ->with(['campanha.localColeta', 'doacao'])
            ->where('status', 'agendado')
            ->where('data_hora', '>=', now())
            ->orderBy('data_hora')
            ->get();

        $agendamentosHistorico = $user->agendamentos()
            ->with(['campanha.localColeta', 'doacao'])
            ->where(function ($query): void {
                $query
                    ->where('status', '<>', 'agendado')
                    ->orWhere('data_hora', '<', now());
            })
            ->latest('data_hora')
            ->paginate(10)
            ->withQueryString();

        return view('usuario.agendamentos.index', [
            'agendamentosAtivos' => $agendamentosAtivos,
            'agendamentosHistorico' => $agendamentosHistorico,
        ]);
    }

    public function show(Request $request, Agendamento $agendamento): View
    {
        $agendamento = $this->agendamentoDoUsuario($request, $agendamento);
        $agendamento->loadMissing(['campanha.localColeta', 'doacao.bolsaSangue']);

        return view('usuario.agendamentos.show', [
            'agendamento' => $agendamento,
        ]);
    }

    public function create(Request $request, Campanha $campanha): View|RedirectResponse
    {
        $user = $request->user();
        assert($user !== null);

        $carteira = $user->carteiraDoacao;

        if (! $user->podeAgendarDoacao() || $carteira === null) {
            return redirect()
                ->route('usuario.carteirinha')
                ->withErrors([
                    'agendamento' => 'Emita uma carteirinha ativa antes de agendar uma doacao.',
                ]);
        }

        if ($user->intervaloMinimoDoacaoDias() === null) {
            return redirect()
                ->route('usuario.carteirinha')
                ->withErrors([
                    'sexo' => 'Informe o sexo biologico para calcular o intervalo minimo entre doacoes.',
                ]);
        }

        if (! $this->campanhaDisponivel($campanha)) {
            return redirect()
                ->route('home')
                ->withErrors([
                    'agendamento' => 'Esta campanha nao esta disponivel para agendamento.',
                ]);
        }

        if ($user->agendamentos()->where('campanha_id', $campanha->id)->exists()) {
            return redirect()
                ->route('usuario.dashboard')
                ->withErrors([
                    'agendamento' => 'Voce ja possui um agendamento para esta campanha.',
                ]);
        }

        $dataMinima = $this->dataMinima($campanha);
        $dataMaxima = $this->fimCampanha($campanha);

        if ($dataMinima->gt($dataMaxima)) {
            return redirect()
                ->route('home')
                ->withErrors([
                    'agendamento' => 'Esta campanha nao possui horarios disponiveis para agendamento.',
                ]);
        }

        return view('usuario.agendamentos.create', [
            'campanha' => $campanha->loadMissing('localColeta'),
            'horarios' => $this->horariosAgendamento($campanha, $dataMinima, $dataMaxima, $user),
            'totalAgendamentos' => $campanha->agendamentos()->where('status', 'agendado')->count(),
        ]);
    }

    public function edit(Request $request, Agendamento $agendamento): View|RedirectResponse
    {
        $user = $request->user();
        assert($user !== null);

        $agendamento = $this->agendamentoDoUsuario($request, $agendamento);
        $agendamento->loadMissing('campanha.localColeta');
        $campanha = $agendamento->campanha;

        if (! $agendamento->podeSerGerenciadoPeloDoador()) {
            return redirect()
                ->route('usuario.agendamentos.show', $agendamento)
                ->withErrors([
                    'agendamento' => 'Este agendamento nao pode mais ser reagendado.',
                ]);
        }

        if ($campanha === null || ! $this->campanhaDisponivel($campanha)) {
            return redirect()
                ->route('usuario.agendamentos.show', $agendamento)
                ->withErrors([
                    'agendamento' => 'A campanha deste agendamento nao esta disponivel para reagendamento.',
                ]);
        }

        if ($user->intervaloMinimoDoacaoDias() === null) {
            return redirect()
                ->route('usuario.carteirinha')
                ->withErrors([
                    'sexo' => 'Informe o sexo biologico para calcular o intervalo minimo entre doacoes.',
                ]);
        }

        $dataMinima = $this->dataMinima($campanha);
        $dataMaxima = $this->fimCampanha($campanha);

        if ($dataMinima->gt($dataMaxima)) {
            return redirect()
                ->route('usuario.agendamentos.show', $agendamento)
                ->withErrors([
                    'agendamento' => 'Esta campanha nao possui horarios disponiveis para reagendamento.',
                ]);
        }

        return view('usuario.agendamentos.edit', [
            'agendamento' => $agendamento,
            'campanha' => $campanha,
            'horarios' => $this->horariosAgendamento($campanha, $dataMinima, $dataMaxima, $user, $agendamento),
            'totalAgendamentos' => $campanha->agendamentos()->where('status', 'agendado')->count(),
        ]);
    }

    public function store(Request $request, Campanha $campanha): RedirectResponse
    {
        $user = $request->user();
        assert($user !== null);

        $carteira = $user->carteiraDoacao;

        if (! $user->podeAgendarDoacao() || $carteira === null) {
            return redirect()
                ->route('usuario.carteirinha')
                ->withErrors([
                    'agendamento' => 'Emita uma carteirinha ativa antes de agendar uma doacao.',
                ]);
        }

        if ($user->intervaloMinimoDoacaoDias() === null) {
            return redirect()
                ->route('usuario.carteirinha')
                ->withErrors([
                    'sexo' => 'Informe o sexo biologico para calcular o intervalo minimo entre doacoes.',
                ]);
        }

        if (! $this->campanhaDisponivel($campanha)) {
            return redirect()
                ->route('home')
                ->withErrors([
                    'agendamento' => 'Esta campanha nao esta disponivel para agendamento.',
                ]);
        }

        if ($user->agendamentos()->where('campanha_id', $campanha->id)->exists()) {
            return redirect()
                ->route('usuario.dashboard')
                ->withErrors([
                    'agendamento' => 'Voce ja possui um agendamento para esta campanha.',
                ]);
        }

        $data = $request->validate([
            'data_hora' => [
                'required',
                'date',
                'after_or_equal:now',
                $this->validarPeriodoCampanha($campanha),
                $this->validarHorarioAtendimento($campanha),
                $this->validarIntervaloHorario(),
                $this->validarIntervaloMinimoDoacao($user),
            ],
        ]);

        $dataHora = CarbonImmutable::parse((string) $data['data_hora'])->second(0);

        $lotado = DB::transaction(function () use ($campanha, $dataHora, $user): bool {
            $agendamentosNoHorario = $campanha->agendamentos()
                ->where('data_hora', $dataHora)
                ->where('status', 'agendado')
                ->lockForUpdate()
                ->pluck('id')
                ->count();

            if ($agendamentosNoHorario >= $campanha->agendamentos_por_horario) {
                return true;
            }

            $user->agendamentos()->create([
                'campanha_id' => $campanha->id,
                'data_hora' => $dataHora,
                'status' => 'agendado',
            ]);

            return false;
        });

        if ($lotado) {
            return back()
                ->withInput()
                ->withErrors([
                    'data_hora' => 'Este horario ja atingiu o limite de agendamentos.',
                ]);
        }

        return redirect()
            ->route('usuario.dashboard')
            ->with('success', 'Agendamento realizado com sucesso.');
    }

    public function update(Request $request, Agendamento $agendamento): RedirectResponse
    {
        $user = $request->user();
        assert($user !== null);

        $agendamento = $this->agendamentoDoUsuario($request, $agendamento);
        $agendamento->loadMissing('campanha');
        $campanha = $agendamento->campanha;

        if (! $agendamento->podeSerGerenciadoPeloDoador()) {
            return redirect()
                ->route('usuario.agendamentos.show', $agendamento)
                ->withErrors([
                    'agendamento' => 'Este agendamento nao pode mais ser reagendado.',
                ]);
        }

        if ($campanha === null || ! $this->campanhaDisponivel($campanha)) {
            return redirect()
                ->route('usuario.agendamentos.show', $agendamento)
                ->withErrors([
                    'agendamento' => 'A campanha deste agendamento nao esta disponivel para reagendamento.',
                ]);
        }

        $data = $request->validate([
            'data_hora' => [
                'required',
                'date',
                'after_or_equal:now',
                $this->validarPeriodoCampanha($campanha),
                $this->validarHorarioAtendimento($campanha),
                $this->validarIntervaloHorario(),
                $this->validarIntervaloMinimoDoacao($user, $agendamento),
            ],
        ]);

        $dataHora = CarbonImmutable::parse((string) $data['data_hora'])->second(0);

        $resultado = DB::transaction(function () use ($campanha, $dataHora, $agendamento): string {
            $agendamentoAtual = Agendamento::lockForUpdate()->findOrFail($agendamento->id);

            if (! $agendamentoAtual->podeSerGerenciadoPeloDoador()) {
                return 'indisponivel';
            }

            $agendamentosNoHorario = $campanha->agendamentos()
                ->where('id', '<>', $agendamentoAtual->id)
                ->where('data_hora', $dataHora)
                ->where('status', 'agendado')
                ->lockForUpdate()
                ->pluck('id')
                ->count();

            if ($agendamentosNoHorario >= $campanha->agendamentos_por_horario) {
                return 'lotado';
            }

            $agendamentoAtual->update([
                'data_hora' => $dataHora,
                'status' => 'agendado',
            ]);

            return 'ok';
        });

        if ($resultado === 'indisponivel') {
            return redirect()
                ->route('usuario.agendamentos.show', $agendamento)
                ->withErrors([
                    'agendamento' => 'Este agendamento nao pode mais ser reagendado.',
                ]);
        }

        if ($resultado === 'lotado') {
            return back()
                ->withInput()
                ->withErrors([
                    'data_hora' => 'Este horario ja atingiu o limite de agendamentos.',
                ]);
        }

        return redirect()
            ->route('usuario.agendamentos.show', $agendamento)
            ->with('success', 'Agendamento reagendado com sucesso.');
    }

    public function cancel(Request $request, Agendamento $agendamento): RedirectResponse
    {
        $agendamento = $this->agendamentoDoUsuario($request, $agendamento);

        $cancelado = DB::transaction(function () use ($agendamento): bool {
            $agendamentoAtual = Agendamento::lockForUpdate()->findOrFail($agendamento->id);

            if (! $agendamentoAtual->podeSerGerenciadoPeloDoador()) {
                return false;
            }

            $agendamentoAtual->update(['status' => 'cancelado']);

            return true;
        });

        if (! $cancelado) {
            return back()->withErrors([
                'agendamento' => 'Este agendamento nao pode mais ser cancelado.',
            ]);
        }

        return redirect()
            ->route('usuario.agendamentos.index')
            ->with('success', 'Agendamento cancelado com sucesso.');
    }

    private function agendamentoDoUsuario(Request $request, Agendamento $agendamento): Agendamento
    {
        $user = $request->user();
        assert($user !== null);

        abort_unless($agendamento->user_id === $user->id, 404);

        return $agendamento;
    }

    private function campanhaDisponivel(Campanha $campanha): bool
    {
        return $campanha->status === 'ativa'
            && $this->inicioCampanha($campanha)->lte(now())
            && $this->fimCampanha($campanha)->gte(now());
    }

    private function dataMinima(Campanha $campanha): CarbonImmutable
    {
        $agora = CarbonImmutable::now();
        $inicioCampanha = $this->dataComHorario($campanha->data_inicio, $this->horarioInicio($campanha));
        $base = $inicioCampanha->gt($agora) ? $inicioCampanha : $agora;

        return $this->proximoHorarioValido($base);
    }

    private function validarPeriodoCampanha(Campanha $campanha): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail) use ($campanha): void {
            try {
                $dataHora = CarbonImmutable::parse((string) $value);
            } catch (Throwable) {
                return;
            }

            if ($dataHora->lt($this->inicioCampanha($campanha)) || $dataHora->gt($this->fimCampanha($campanha))) {
                $fail('A data e horario devem estar dentro do periodo da campanha.');
            }
        };
    }

    private function validarIntervaloHorario(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            try {
                $dataHora = CarbonImmutable::parse((string) $value);
            } catch (Throwable) {
                return;
            }

            if (! in_array($dataHora->minute, [0, 30], true)) {
                $fail('Escolha um horario de 30 em 30 minutos.');
            }
        };
    }

    private function validarIntervaloMinimoDoacao(User $user, ?Agendamento $agendamentoIgnorado = null): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail) use ($user, $agendamentoIgnorado): void {
            try {
                $dataHora = CarbonImmutable::parse((string) $value);
            } catch (Throwable) {
                return;
            }

            $intervaloDias = $user->intervaloMinimoDoacaoDias();
            $dataReferencia = $this->conflitoIntervaloDoacao(
                $this->datasReferenciaIntervaloDoacao($user, $agendamentoIgnorado),
                $intervaloDias,
                $dataHora,
            );

            if ($dataReferencia === null || $intervaloDias === null) {
                return;
            }

            $fail("Respeite o intervalo minimo de {$intervaloDias} dias entre doacoes. Ha uma doacao ou agendamento em {$dataReferencia->format('d/m/Y')}.");
        };
    }

    private function inicioCampanha(Campanha $campanha): CarbonImmutable
    {
        return CarbonImmutable::instance($campanha->data_inicio)->startOfDay();
    }

    private function fimCampanha(Campanha $campanha): CarbonImmutable
    {
        return $this->dataComHorario($campanha->data_fim, $this->horarioFim($campanha));
    }

    private function proximoHorarioValido(CarbonImmutable $dataHora): CarbonImmutable
    {
        $normalizada = $dataHora->second(0)->microsecond(0);

        if ($dataHora->second === 0 && $dataHora->minute === 0) {
            return $normalizada;
        }

        if ($dataHora->minute < 30) {
            return $normalizada->minute(30);
        }

        if ($dataHora->second === 0 && $dataHora->minute === 30) {
            return $normalizada;
        }

        return $normalizada->addHour()->minute(0);
    }

    private function validarHorarioAtendimento(Campanha $campanha): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail) use ($campanha): void {
            try {
                $dataHora = CarbonImmutable::parse((string) $value);
            } catch (Throwable) {
                return;
            }

            $inicio = $this->dataComHorario($dataHora, $this->horarioInicio($campanha));
            $fim = $this->dataComHorario($dataHora, $this->horarioFim($campanha));

            if ($dataHora->lt($inicio) || $dataHora->gt($fim)) {
                $fail('Escolha um horario dentro do periodo de atendimento da campanha.');
            }
        };
    }

    /**
     * @return array<int, array{grupo: string, valor: string, rotulo: string, vagas: int, lotado: bool, bloqueado: bool, motivo: string|null}>
     */
    private function horariosAgendamento(
        Campanha $campanha,
        CarbonImmutable $inicio,
        CarbonImmutable $fim,
        User $user,
        ?Agendamento $agendamentoIgnorado = null,
    ): array
    {
        $ocupacaoQuery = $campanha->agendamentos()
            ->where('status', 'agendado')
            ->whereBetween('data_hora', [$inicio, $fim]);

        if ($agendamentoIgnorado !== null) {
            $ocupacaoQuery->where('id', '<>', $agendamentoIgnorado->id);
        }

        $ocupacao = $ocupacaoQuery
            ->selectRaw('data_hora, count(*) as total')
            ->groupBy('data_hora')
            ->pluck('total', 'data_hora')
            ->map(fn (mixed $total) => (int) $total);

        $datasReferencia = $this->datasReferenciaIntervaloDoacao($user, $agendamentoIgnorado);
        $intervaloDias = $user->intervaloMinimoDoacaoDias();
        $horarios = [];

        for ($dia = $inicio->startOfDay(); $dia->lte($fim->startOfDay()); $dia = $dia->addDay()) {
            $inicioDia = $this->dataComHorario($dia, $this->horarioInicio($campanha));
            $fimDia = $this->dataComHorario($dia, $this->horarioFim($campanha));

            if ($inicioDia->lt($inicio)) {
                $inicioDia = $this->proximoHorarioValido($inicio);
            }

            if ($fimDia->gt($fim)) {
                $fimDia = $fim;
            }

            for ($dataHora = $inicioDia; $dataHora->lte($fimDia); $dataHora = $dataHora->addMinutes(30)) {
                $chave = $dataHora->format('Y-m-d H:i:s');
                $ocupados = (int) ($ocupacao[$chave] ?? 0);
                $vagas = max(0, $campanha->agendamentos_por_horario - $ocupados);
                $bloqueadoPorIntervalo = $this->conflitoIntervaloDoacao($datasReferencia, $intervaloDias, $dataHora) !== null;

                $horarios[] = [
                    'grupo' => $dataHora->format('d/m/Y'),
                    'valor' => $dataHora->format('Y-m-d\TH:i'),
                    'rotulo' => $dataHora->format('H:i'),
                    'vagas' => $vagas,
                    'lotado' => $vagas === 0,
                    'bloqueado' => $bloqueadoPorIntervalo,
                    'motivo' => $bloqueadoPorIntervalo ? 'Intervalo minimo' : null,
                ];
            }
        }

        return $horarios;
    }

    private function dataComHorario(mixed $data, string $horario): CarbonImmutable
    {
        $data = CarbonImmutable::instance($data);

        return CarbonImmutable::parse("{$data->format('Y-m-d')} {$horario}");
    }

    /**
     * @return array<int, CarbonImmutable>
     */
    private function datasReferenciaIntervaloDoacao(User $user, ?Agendamento $agendamentoIgnorado = null): array
    {
        $doacoesConfirmadas = DB::table('doacoes')
            ->join('agendamentos', 'agendamentos.id', '=', 'doacoes.agendamento_id')
            ->where('agendamentos.user_id', $user->id)
            ->where('doacoes.status', 'confirmada')
            ->pluck('doacoes.data_coleta');

        $agendamentosAtivosQuery = DB::table('agendamentos')
            ->where('user_id', $user->id)
            ->where('status', 'agendado');

        if ($agendamentoIgnorado !== null) {
            $agendamentosAtivosQuery->where('id', '<>', $agendamentoIgnorado->id);
        }

        $agendamentosAtivos = $agendamentosAtivosQuery->pluck('data_hora');

        return $doacoesConfirmadas
            ->merge($agendamentosAtivos)
            ->map(fn (mixed $data): CarbonImmutable => CarbonImmutable::parse((string) $data)->startOfDay())
            ->unique(fn (CarbonImmutable $data): string => $data->toDateString())
            ->sortBy(fn (CarbonImmutable $data): int => $data->timestamp)
            ->values()
            ->all();
    }

    /**
     * @param array<int, CarbonImmutable> $datasReferencia
     */
    private function conflitoIntervaloDoacao(array $datasReferencia, ?int $intervaloDias, CarbonImmutable $dataHora): ?CarbonImmutable
    {
        if ($intervaloDias === null) {
            return null;
        }

        $dataSelecionada = $dataHora->startOfDay();

        foreach ($datasReferencia as $dataReferencia) {
            if ($dataSelecionada->gt($dataReferencia->subDays($intervaloDias))
                && $dataSelecionada->lt($dataReferencia->addDays($intervaloDias))) {
                return $dataReferencia;
            }
        }

        return null;
    }

    private function horarioInicio(Campanha $campanha): string
    {
        return substr((string) ($campanha->horario_inicio ?? '08:00'), 0, 5);
    }

    private function horarioFim(Campanha $campanha): string
    {
        return substr((string) ($campanha->horario_fim ?? '17:00'), 0, 5);
    }
}
