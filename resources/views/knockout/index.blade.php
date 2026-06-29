<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Eliminatorias - {{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body>
    <div class="toast-stack" id="toast-stack" aria-live="polite" aria-atomic="true"></div>
    <div class="success-swal" id="prediction-success-modal" role="status" aria-live="polite" aria-modal="true" hidden>
        <div class="success-swal-box">
            <div class="success-swal-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M20 6L9 17l-5-5"></path>
                </svg>
            </div>
            <strong>PALPITES SALVOS</strong>
            <p id="prediction-success-message">Seus palpites foram registrados com sucesso.</p>
            <button type="button" id="prediction-success-close">OK</button>
        </div>
    </div>

    <div class="app-shell">
        <aside class="sidebar" aria-label="Menu principal">
            <div class="sidebar-logo">
                <img src="{{ asset('images/world-cup-2026-emblem.svg') }}" alt="">
                <div class="brand-copy">
                    <strong>Bolao</strong>
                    <span>Copa 2026</span>
                </div>
            </div>

            <button class="sidebar-toggle" id="sidebar-toggle" type="button" aria-label="Recolher menu">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                    <path d="M15 18l-6-6 6-6"></path>
                </svg>
            </button>

            <nav class="sidebar-nav">
                <a class="nav-item" href="{{ route('dashboard') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <path d="M8 9l-4 4 4 4M16 9l4 4-4 4M14 5l-4 14"></path>
                    </svg>
                    <span>Lógica</span>
                </a>
                <a class="nav-item" href="{{ route('predictions.index') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <circle cx="12" cy="12" r="8"></circle>
                        <circle cx="12" cy="12" r="3"></circle>
                        <path d="M12 4v3M12 17v3M4 12h3M17 12h3"></path>
                    </svg>
                    <span>Palpites</span>
                </a>
                <a class="nav-item active" href="{{ route('knockout.index') }}" aria-current="page">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <path d="M4 6h7"></path>
                        <path d="M4 18h7"></path>
                        <path d="M13 12h7"></path>
                        <path d="M11 6c2 0 2 6 4 6M11 18c2 0 2-6 4-6"></path>
                    </svg>
                    <span>Eliminatorias</span>
                </a>
                <a class="nav-item" href="{{ route('rules.index') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <path d="M7 3h7l4 4v14H7z"></path>
                        <path d="M14 3v5h5"></path>
                        <path d="M9.5 13h5M9.5 17h5"></path>
                    </svg>
                    <span>Regras</span>
                </a>
                <a class="nav-item" href="{{ route('ranking.index') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <path d="M8 21h8"></path>
                        <path d="M12 17v4"></path>
                        <path d="M7 4h10v5a5 5 0 0 1-10 0z"></path>
                        <path d="M7 6H4a3 3 0 0 0 3 3M17 6h3a3 3 0 0 1-3 3"></path>
                    </svg>
                    <span>Ranking</span>
                </a>
            </nav>

            <div class="sidebar-user">
                <div class="avatar">{{ strtoupper(substr(auth()->user()->nickname, 0, 1)) }}</div>
                <div class="user-copy">
                    <strong>{{ auth()->user()->nickname }}</strong>
                    <span>Participante</span>
                </div>
            </div>
        </aside>

        <main class="dashboard-main knockout-main">
            <section class="overview">
                <div>
                    <p class="eyebrow">Mata-mata</p>
                    <h1>Eliminatorias</h1>
                    <p class="lead">Fase de 32, oitavas, quartas, semifinais, terceiro lugar e final.</p>
                </div>
            </section>

            @if (! $isUnlocked)
                <section class="knockout-lock">
                    <div class="knockout-lock-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                            <rect x="5" y="11" width="14" height="10" rx="2"></rect>
                            <path d="M8 11V8a4 4 0 0 1 8 0v3"></path>
                        </svg>
                    </div>
                    <div>
                        <h2>Eliminatorias bloqueadas</h2>
                        <p>Complete os palpites das 3 rodadas da fase de grupos para liberar o mata-mata.</p>
                        <strong>{{ $completedGroupPredictions }} / {{ $requiredGroupPredictions }} palpites completos</strong>
                    </div>
                    <a href="{{ route('predictions.index') }}">Voltar para grupos</a>
                </section>
            @else
                <form id="knockout-form" method="post" action="{{ route('knockout.store') }}">
                    @csrf
                    @php
                        $phaseOrder = [
                            ['key' => 'r32', 'label' => 'FASE 32', 'short' => 'F32'],
                            ['key' => 'r16', 'label' => 'OITAVAS', 'short' => 'OIT'],
                            ['key' => 'qf', 'label' => 'QUARTAS', 'short' => 'QRT'],
                            ['key' => 'sf', 'label' => 'SEMIFINAIS', 'short' => 'SF'],
                            ['key' => 'third', 'label' => '3 LUGAR', 'short' => '3L'],
                            ['key' => 'final', 'label' => 'FINAL', 'short' => 'FIN'],
                        ];
                        $slotCode = function (?string $label) {
                            $label = (string) $label;
                            $label = str_replace(['Winner Group ', 'Runner-up Group ', '3rd Group '], ['1G', '2G', '3G'], $label);
                            $label = preg_replace('/[^A-Za-z0-9]/', '', $label) ?: 'TBD';

                            return strtoupper(substr($label, 0, 3));
                        };
                        $teamDisplay = function ($match, string $side) use ($flagCodes, $simulatedTeams, $slotCode) {
                            $team = $side === 'home' ? $match->homeTeam : $match->awayTeam;
                            $slot = $side === 'home' ? $match->home_slot : $match->away_slot;
                            $simulated = $simulatedTeams[$match->match_number][$side] ?? null;

                            if ($team) {
                                return [
                                    'name' => $team->name,
                                    'code' => $team->code,
                                    'flag' => $flagCodes[$team->code] ?? null,
                                    'tag' => null,
                                ];
                            }

                            if ($simulated) {
                                return [
                                    'name' => $simulated['name'],
                                    'code' => $simulated['code'],
                                    'flag' => $flagCodes[$simulated['code']] ?? null,
                                    'tag' => 'Simulado',
                                ];
                            }

                            $code = $slotCode($slot);

                            return [
                                'name' => $slot,
                                'code' => $code,
                                'flag' => null,
                                'tag' => 'Slot',
                            ];
                        };
                    @endphp

                    <section class="knockout-phase-shell">
                        <div class="knockout-phase-control">
                            <button class="phase-nav-button secondary" id="knockout-phase-prev" type="button" disabled>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                    <path d="M15 18l-6-6 6-6"></path>
                                </svg>
                                Voltar fase
                            </button>
                            <div class="phase-current">
                                <span id="knockout-current-phase">FASE 32</span>
                                <div class="phase-dots" aria-label="Fases eliminatorias">
                                    @foreach ($phaseOrder as $phaseIndex => $phase)
                                        <button class="phase-dot {{ $phaseIndex === 0 ? 'active' : '' }}" type="button" data-phase-jump="{{ $phaseIndex }}">{{ $phase['short'] }}</button>
                                    @endforeach
                                </div>
                            </div>
                            <button class="phase-nav-button" id="knockout-phase-next" type="button">
                                Ir para OITAVAS
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                    <path d="M9 18l6-6-6-6"></path>
                                </svg>
                            </button>
                        </div>

                        <div class="knockout-phases">
                            @foreach ($phaseOrder as $phaseIndex => $phase)
                                @php
                                    $phaseMatches = ($matchesByStage[$phase['key']] ?? collect())->values();
                                @endphp
                                <section class="knockout-phase" data-phase-index="{{ $phaseIndex }}" data-phase-label="{{ $phase['label'] }}" data-next-label="{{ $phaseOrder[$phaseIndex + 1]['label'] ?? '' }}" @if ($phaseIndex !== 0) hidden @endif>
                                    <div class="knockout-phase-header">
                                        <div>
                                            <p class="eyebrow">Eliminatorias</p>
                                            <h2>{{ $phase['label'] }}</h2>
                                        </div>
                                        <strong>{{ $phaseMatches->count() }} confrontos</strong>
                                    </div>

                                    <div class="knockout-phase-grid">
                                        @foreach ($phaseMatches as $match)
                                            @php
                                                $prediction = $match->predictions->first();
                                                $startsAt = $match->starts_at->timezone('America/Sao_Paulo');
                                                $closesAt = $startsAt->copy()->subHour();
                                                $isClosed = now('America/Sao_Paulo')->greaterThanOrEqualTo($closesAt);
                                                $home = $teamDisplay($match, 'home');
                                                $away = $teamDisplay($match, 'away');
                                            @endphp
                                            <article class="knockout-match phase-match" data-closes-at="{{ $closesAt->toIso8601String() }}">
                                                <div class="phase-match-top">
                                                    <span>Jogo {{ $match->match_number }}</span>
                                                    <em class="{{ $isClosed ? 'is-closed' : '' }}">{{ $isClosed ? 'Fechado' : 'Fecha '.$closesAt->format('d/m H:i') }}</em>
                                                </div>
                                                <div class="phase-match-date">
                                                    <strong>{{ $startsAt->format('d/m/Y H:i') }}</strong>
                                                    <span>{{ $match->venue }}</span>
                                                </div>

                                                <div class="phase-score-card">
                                                    <label class="phase-team-row">
                                                        <span class="phase-team-identity">
                                                            <span class="phase-flag">
                                                                @if ($home['flag'])
                                                                    <img src="https://flagcdn.com/w40/{{ $home['flag'] }}.png" alt="{{ $home['name'] }}" loading="lazy" onerror="this.style.display='none'">
                                                                @else
                                                                    {{ $home['code'] }}
                                                                @endif
                                                            </span>
                                                            <span>
                                                                <strong>{{ $home['code'] }}</strong>
                                                                <em>{{ $home['name'] }}</em>
                                                            </span>
                                                            @if ($home['tag'])
                                                                <b>{{ $home['tag'] }}</b>
                                                            @endif
                                                        </span>
                                                        <input name="predictions[{{ $match->id }}][home_score]" type="text" inputmode="numeric" pattern="[0-9]" maxlength="1" value="{{ $prediction?->home_score }}" aria-label="Placar {{ $home['name'] }}" @disabled($isClosed)>
                                                    </label>

                                                    <div class="phase-versus">x</div>

                                                    <label class="phase-team-row">
                                                        <span class="phase-team-identity">
                                                            <span class="phase-flag">
                                                                @if ($away['flag'])
                                                                    <img src="https://flagcdn.com/w40/{{ $away['flag'] }}.png" alt="{{ $away['name'] }}" loading="lazy" onerror="this.style.display='none'">
                                                                @else
                                                                    {{ $away['code'] }}
                                                                @endif
                                                            </span>
                                                            <span>
                                                                <strong>{{ $away['code'] }}</strong>
                                                                <em>{{ $away['name'] }}</em>
                                                            </span>
                                                            @if ($away['tag'])
                                                                <b>{{ $away['tag'] }}</b>
                                                            @endif
                                                        </span>
                                                        <input name="predictions[{{ $match->id }}][away_score]" type="text" inputmode="numeric" pattern="[0-9]" maxlength="1" value="{{ $prediction?->away_score }}" aria-label="Placar {{ $away['name'] }}" @disabled($isClosed)>
                                                    </label>
                                                </div>
                                            </article>
                                        @endforeach

                                        @if ($phaseMatches->isEmpty())
                                            <div class="knockout-empty-phase">
                                                <strong>{{ $phase['label'] }}</strong>
                                                <span>Nenhum confronto sincronizado para esta fase.</span>
                                            </div>
                                        @endif
                                    </div>
                                </section>
                            @endforeach
                        </div>
                    </section>
                    <div class="save-predictions-bar">
                        <button type="submit">SALVAR ELIMINATORIAS</button>
                    </div>
                </form>
            @endif
        </main>
    </div>

    <script>
        const toastStack = document.querySelector('#toast-stack');
        const sidebarToggle = document.querySelector('#sidebar-toggle');
        const storedSidebar = localStorage.getItem('sidebar-collapsed');
        const successModal = document.querySelector('#prediction-success-modal');
        const successModalMessage = document.querySelector('#prediction-success-message');
        const successModalClose = document.querySelector('#prediction-success-close');

        if (storedSidebar === 'true') {
            document.body.classList.add('sidebar-collapsed');
        }

        sidebarToggle?.addEventListener('click', () => {
            document.body.classList.toggle('sidebar-collapsed');
            localStorage.setItem('sidebar-collapsed', document.body.classList.contains('sidebar-collapsed'));
        });

        function showToast(message, duration = 3000) {
            if (!toastStack || !message) return;

            const toast = document.createElement('div');
            toast.className = 'app-toast';
            toast.style.setProperty('--toast-duration', `${duration}ms`);
            toast.innerHTML = '<span class="toast-icon" aria-hidden="true">!</span><p class="toast-message"></p><button class="toast-close" type="button" aria-label="Fechar aviso">&times;</button><span class="toast-progress" aria-hidden="true"></span>';
            toast.querySelector('.toast-message').textContent = message;
            toast.querySelector('.toast-close').addEventListener('click', () => toast.remove());
            toastStack.prepend(toast);
            window.setTimeout(() => toast.remove(), duration);
        }

        function closeSuccessModal() {
            successModal?.classList.add('is-leaving');
            window.setTimeout(() => {
                successModal?.setAttribute('hidden', '');
                successModal?.classList.remove('is-visible', 'is-leaving');
            }, 180);
        }

        function showSuccessModal(message) {
            if (successModalMessage && message) {
                successModalMessage.textContent = message;
            }

            successModal?.removeAttribute('hidden');
            window.requestAnimationFrame(() => successModal?.classList.add('is-visible'));
            window.setTimeout(closeSuccessModal, 2600);
        }

        successModalClose?.addEventListener('click', closeSuccessModal);

        @if (session('prediction_success'))
            showSuccessModal(@json(session('prediction_success')));
        @endif

        @if (session('prediction_error'))
            showToast(@json(session('prediction_error')));
        @endif

        const knockoutPhases = Array.from(document.querySelectorAll('.knockout-phase'));
        const phasePrev = document.querySelector('#knockout-phase-prev');
        const phaseNext = document.querySelector('#knockout-phase-next');
        const phaseTitle = document.querySelector('#knockout-current-phase');
        const phaseDots = Array.from(document.querySelectorAll('[data-phase-jump]'));
        let activePhaseIndex = 0;

        function showKnockoutPhase(index) {
            if (!knockoutPhases.length) return;

            activePhaseIndex = Math.max(0, Math.min(index, knockoutPhases.length - 1));

            knockoutPhases.forEach((phase, phaseIndex) => {
                phase.hidden = phaseIndex !== activePhaseIndex;
            });

            const activePhase = knockoutPhases[activePhaseIndex];
            const nextLabel = activePhase.dataset.nextLabel;

            if (phaseTitle) {
                phaseTitle.textContent = activePhase.dataset.phaseLabel;
            }

            if (phasePrev) {
                phasePrev.disabled = activePhaseIndex === 0;
            }

            if (phaseNext) {
                phaseNext.disabled = activePhaseIndex === knockoutPhases.length - 1;
                const phaseNextText = Array.from(phaseNext.childNodes).find((n) => n.nodeType === Node.TEXT_NODE);
                if (phaseNextText) {
                    phaseNextText.textContent = nextLabel ? `Ir para ${nextLabel} ` : 'Fim da chave ';
                }
            }

            phaseDots.forEach((dot, phaseIndex) => {
                dot.classList.toggle('active', phaseIndex === activePhaseIndex);
            });
        }

        phasePrev?.addEventListener('click', () => showKnockoutPhase(activePhaseIndex - 1));
        phaseNext?.addEventListener('click', () => showKnockoutPhase(activePhaseIndex + 1));
        phaseDots.forEach((dot) => {
            dot.addEventListener('click', () => showKnockoutPhase(Number(dot.dataset.phaseJump)));
        });

        showKnockoutPhase(0);

        document.querySelector('#knockout-form')?.addEventListener('submit', (event) => {
            const visiblePhase = document.querySelector('.knockout-phase:not([hidden])');
            const matches = Array.from((visiblePhase ?? document).querySelectorAll('.knockout-match'));
            let hasPrediction = false;

            for (const match of matches) {
                const inputs = Array.from(match.querySelectorAll('input:not(:disabled)'));

                if (inputs.length === 0) continue;

                const [homeInput, awayInput] = inputs;
                const homeValue = homeInput.value.trim();
                const awayValue = awayInput.value.trim();

                if (homeValue === '' && awayValue === '') {
                    inputs.forEach((input) => input.disabled = true);
                    continue;
                }

                if (!/^[0-9]$/.test(homeValue) || !/^[0-9]$/.test(awayValue)) {
                    event.preventDefault();
                    showToast('Preencha os dois placares do confronto com numeros de 0 a 9.');
                    (homeValue === '' ? homeInput : awayInput).focus();
                    return;
                }

                hasPrediction = true;
            }

            if (!hasPrediction) {
                event.preventDefault();
                showToast('Preencha ao menos um confronto antes de salvar.');
                return;
            }

            document.querySelectorAll('.knockout-phase[hidden] input').forEach((input) => {
                input.disabled = true;
            });
        });
    </script>
</body>
</html>
