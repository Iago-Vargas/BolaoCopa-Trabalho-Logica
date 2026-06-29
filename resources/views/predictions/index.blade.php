<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Palpites - {{ config('app.name') }}</title>
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
                    <strong>Bolão</strong>
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
                <a class="nav-item active" href="{{ route('predictions.index') }}" aria-current="page">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <circle cx="12" cy="12" r="8"></circle>
                        <circle cx="12" cy="12" r="3"></circle>
                        <path d="M12 4v3M12 17v3M4 12h3M17 12h3"></path>
                    </svg>
                    <span>Palpites</span>
                </a>
                <a class="nav-item" href="{{ route('knockout.index') }}">
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

        <main class="dashboard-main predictions-main">
            <section class="overview">
                <div>
                    <h1>GRUPOS DA COPA</h1>
                    <p class="lead">Preencha seus placares ao lado de cada jogo.</p>
                </div>
            </section>

            <form id="predictions-form" method="post" action="{{ route('predictions.store') }}">
                @csrf
                <div class="round-control" aria-label="Controle de rodada">
                    <div>
                        <span>Rodada atual</span>
                        <strong id="current-round-label">1ª rodada</strong>
                        <em id="current-round-lock-label"></em>
                    </div>
                    <div class="round-control-actions">
                        <button id="global-round-prev" class="round-control-button secondary" type="button">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                <path d="M15 18l-6-6 6-6"></path>
                            </svg>
                            Voltar rodada
                        </button>
                        <button id="global-round-next" class="round-control-button" type="button">
                            Ir para próxima rodada
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                <path d="M9 18l6-6-6-6"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <section class="world-groups">
                    @foreach ($matchesByGroup as $group => $matches)
                        <article class="group-board">
                            <h2>Grupo {{ $group }}</h2>

                            <div class="group-table">
                                <div class="standings">
                                    <div class="standings-title">Classificação</div>
                                    @foreach ($teamsByGroup[$group] as $index => $team)
                                        @php
                                            $flag = $flagCodes[$team->code] ?? null;
                                            $teamName = $teamNames[$team->code] ?? $team->name;
                                        @endphp
                                        <div class="standing-row" data-team-id="{{ $team->id }}" data-team-name="{{ $teamName }}">
                                            <span class="position position-{{ $index + 1 }}">{{ $index + 1 }}</span>
                                            <span class="team-flag">
                                                @if ($flag)
                                                    <img src="https://flagcdn.com/w40/{{ $flag }}.png" alt="{{ $teamName }}" loading="lazy" onerror="this.style.display='none'">
                                                @endif
                                            </span>
                                            <span class="team-name">{{ $teamName }}</span>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="stats-table" aria-label="Estatísticas do grupo {{ $group }}">
                                    <div class="stats-head">
                                        <span>P</span>
                                        <span>J</span>
                                        <span>V</span>
                                        <span>E</span>
                                        <span>D</span>
                                        <span>GP</span>
                                        <span>GC</span>
                                        <span>SG</span>
                                        <span>%</span>
                                    </div>
                                    @foreach ($teamsByGroup[$group] as $team)
                                        <div class="stats-row" data-team-id="{{ $team->id }}">
                                            <strong data-stat="points">0</strong>
                                            <span data-stat="played">0</span>
                                            <span data-stat="wins">0</span>
                                            <span data-stat="draws">0</span>
                                            <span data-stat="losses">0</span>
                                            <span data-stat="goals_for">0</span>
                                            <span data-stat="goals_against">0</span>
                                            <span data-stat="goal_difference">0</span>
                                            <span data-stat="percentage">0</span>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="group-fixtures">
                                    @foreach ($matches->chunk(2) as $roundIndex => $roundMatches)
                                        @php
                                            $closesAt = $roundLocks[$roundIndex]['closes_at'];
                                            $isClosed = now('America/Sao_Paulo')->greaterThanOrEqualTo($closesAt);
                                        @endphp
                                        <div class="round-block" data-round="{{ $roundIndex }}" data-locked="{{ $isClosed ? '1' : '0' }}" data-closes-at="{{ $closesAt->toIso8601String() }}" @if ($roundIndex !== 0) hidden @endif>
                                            <div class="round-header">
                                                <button class="round-nav round-prev" type="button" aria-label="Rodada anterior">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                                        <path d="M15 18l-6-6 6-6"></path>
                                                    </svg>
                                                </button>
                                                <div class="round-title">
                                                    <strong>{{ $roundIndex + 1 }}ª rodada</strong>
                                                    <em class="{{ $isClosed ? 'is-closed' : '' }}">
                                                        {{ $isClosed ? 'Rodada fechada' : 'Fecha em '.$closesAt->format('d/m/Y H:i') }}
                                                    </em>
                                                </div>
                                                <button class="round-nav round-next" type="button" aria-label="Próxima rodada">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                                        <path d="M9 18l6-6-6-6"></path>
                                                    </svg>
                                                </button>
                                            </div>

                                            @foreach ($roundMatches as $match)
                                                @php
                                                    $prediction = $match->predictions->first();
                                                    $homeFlag = $flagCodes[$match->homeTeam->code] ?? null;
                                                    $awayFlag = $flagCodes[$match->awayTeam->code] ?? null;
                                                    $startsAt = $match->starts_at->timezone('America/Sao_Paulo');
                                                    $weekdays = ['DOM', 'SEG', 'TER', 'QUA', 'QUI', 'SEX', 'SÁB'];
                                                    $hasScore = $match->home_score !== null && $match->away_score !== null;
                                                    $hasResult = $match->is_finished && $hasScore;
                                                    $hasLiveScore = ! $match->is_finished && $hasScore;
                                                    $hasPrediction = $prediction !== null;
                                                    $homeScoreClass = '';
                                                    $awayScoreClass = '';

                                                    if ($hasResult && $hasPrediction) {
                                                        $homeScoreClass = $prediction->home_score === $match->home_score ? 'score-hit' : 'score-miss';
                                                        $awayScoreClass = $prediction->away_score === $match->away_score ? 'score-hit' : 'score-miss';
                                                    }
                                                @endphp
                                                <div
                                                    class="fixture-row"
                                                    data-match-id="{{ $match->id }}"
                                                    data-home-id="{{ $match->homeTeam->id }}"
                                                    data-away-id="{{ $match->awayTeam->id }}"
                                                    data-home-score="{{ $match->home_score }}"
                                                    data-away-score="{{ $match->away_score }}"
                                                    data-is-finished="{{ $match->is_finished ? '1' : '0' }}"
                                                >
                                                    <div class="fixture-meta">
                                                        <span>{{ $weekdays[$startsAt->dayOfWeek] }}</span>
                                                        <strong>{{ $startsAt->format('d/m/Y') }}</strong>
                                                        <em>{{ $match->venue }}</em>
                                                        <b>{{ $startsAt->format('H:i') }}</b>
                                                        <small class="match-score-label {{ $hasLiveScore ? 'live-score' : '' }}" @if (! $hasResult && ! $hasLiveScore) hidden @endif>
                                                            @if ($hasResult)
                                                                Resultado: {{ $match->home_score }} x {{ $match->away_score }}
                                                            @elseif ($hasLiveScore)
                                                                Ao vivo: {{ $match->home_score }} x {{ $match->away_score }}
                                                            @endif
                                                        </small>
                                                    </div>

                                                    <div class="fixture-prediction">
                                                        <span class="team-code">{{ $match->homeTeam->code }}</span>
                                                        <span class="fixture-flag">
                                                            @if ($homeFlag)
                                                                <img src="https://flagcdn.com/w40/{{ $homeFlag }}.png" alt="{{ $teamNames[$match->homeTeam->code] ?? $match->homeTeam->name }}" loading="lazy" onerror="this.style.display='none'">
                                                            @endif
                                                        </span>
                                                        <input class="home-score {{ $homeScoreClass }}" name="predictions[{{ $match->id }}][home_score]" type="text" inputmode="numeric" pattern="[0-9]" maxlength="1" autocomplete="off" value="{{ $prediction?->home_score }}" data-initial-value="{{ $prediction?->home_score }}" @disabled($isClosed)>
                                                        <span class="fixture-x">x</span>
                                                        <input class="away-score {{ $awayScoreClass }}" name="predictions[{{ $match->id }}][away_score]" type="text" inputmode="numeric" pattern="[0-9]" maxlength="1" autocomplete="off" value="{{ $prediction?->away_score }}" data-initial-value="{{ $prediction?->away_score }}" @disabled($isClosed)>
                                                        <span class="fixture-flag">
                                                            @if ($awayFlag)
                                                                <img src="https://flagcdn.com/w40/{{ $awayFlag }}.png" alt="{{ $teamNames[$match->awayTeam->code] ?? $match->awayTeam->name }}" loading="lazy" onerror="this.style.display='none'">
                                                            @endif
                                                        </span>
                                                        <span class="team-code">{{ $match->awayTeam->code }}</span>
                                                        @if ($hasResult && $hasPrediction)
                                                            <span class="prediction-points points-{{ $prediction->points }}">{{ $prediction->points }} pts</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </article>
                    @endforeach
                </section>
                <div class="save-predictions-bar">
                    <button type="submit">SALVAR PALPITES</button>
                </div>
            </form>
        </main>
    </div>

    <script>
        const toastStack = document.querySelector('#toast-stack');
        const sidebarToggle = document.querySelector('#sidebar-toggle');
        const storedSidebar = localStorage.getItem('sidebar-collapsed');

        if (storedSidebar === 'true') {
            document.body.classList.add('sidebar-collapsed');
        }

        sidebarToggle?.addEventListener('click', () => {
            document.body.classList.toggle('sidebar-collapsed');
            localStorage.setItem('sidebar-collapsed', document.body.classList.contains('sidebar-collapsed'));
        });

        function closeToast(toast) {
            toast.classList.add('is-leaving');
            window.setTimeout(() => toast.remove(), 180);
        }

        function showToast(message, duration = 3000) {
            if (!toastStack || !message) {
                return;
            }

            const toast = document.createElement('div');
            toast.className = 'app-toast';
            toast.style.setProperty('--toast-duration', `${duration}ms`);
            toast.innerHTML = `
                <span class="toast-icon" aria-hidden="true">!</span>
                <p class="toast-message"></p>
                <button class="toast-close" type="button" aria-label="Fechar aviso">&times;</button>
                <span class="toast-progress" aria-hidden="true"></span>
            `;

            toast.querySelector('.toast-message').textContent = message;
            toast.querySelector('.toast-close').addEventListener('click', () => closeToast(toast));
            toastStack.prepend(toast);
            window.setTimeout(() => closeToast(toast), duration);
        }

        const successModal = document.querySelector('#prediction-success-modal');
        const successModalMessage = document.querySelector('#prediction-success-message');
        const successModalClose = document.querySelector('#prediction-success-close');

        function closeSuccessModal() {
            successModal?.classList.add('is-leaving');
            window.setTimeout(() => {
                successModal?.setAttribute('hidden', '');
                successModal?.classList.remove('is-visible', 'is-leaving');
            }, 180);
        }

        function showSuccessModal(message) {
            if (!successModal) {
                return;
            }

            if (successModalMessage && message) {
                successModalMessage.textContent = message;
            }

            successModal.removeAttribute('hidden');
            window.requestAnimationFrame(() => successModal.classList.add('is-visible'));
            window.setTimeout(closeSuccessModal, 2600);
        }

        successModalClose?.addEventListener('click', closeSuccessModal);
        successModal?.addEventListener('click', (event) => {
            if (event.target === successModal) {
                closeSuccessModal();
            }
        });

        @if (session('prediction_success'))
            showSuccessModal(@json(session('prediction_success')));
        @endif

        @if (session('prediction_error'))
            showToast(@json(session('prediction_error')));
        @endif

        const numberOnlyMessage = 'Digite somente n\u00fameros.';
        const kingsLeagueMessage = 'Isso \u00e9 Copa do Mundo, n\u00e3o Kings League. Use placares de 0 a 9.';
        let lastScoreToast = 0;

        function showScoreToast(message) {
            const now = Date.now();

            if (now - lastScoreToast < 900) {
                return;
            }

            lastScoreToast = now;
            showToast(message);
        }

        function nextInputValue(input, insertedValue) {
            const start = input.selectionStart ?? input.value.length;
            const end = input.selectionEnd ?? input.value.length;

            return `${input.value.slice(0, start)}${insertedValue}${input.value.slice(end)}`;
        }

        function guardScoreBeforeInput(event) {
            if (!event.inputType?.startsWith('insert') || !event.data) {
                return;
            }

            if (!/^\d+$/.test(event.data)) {
                event.preventDefault();
                showScoreToast(numberOnlyMessage);
                return;
            }

            if (!/^[0-9]?$/.test(nextInputValue(event.currentTarget, event.data))) {
                event.preventDefault();
                showScoreToast(kingsLeagueMessage);
            }
        }

        function sanitizeScoreInput(input) {
            const originalValue = input.value;
            const onlyDigits = originalValue.replace(/\D/g, '');

            if (onlyDigits !== originalValue) {
                showScoreToast(numberOnlyMessage);
            }

            if (onlyDigits.length > 1) {
                showScoreToast(kingsLeagueMessage);
            }

            input.value = onlyDigits.slice(0, 1);
            input.classList.toggle('is-dirty', input.value !== input.dataset.initialValue);
        }

        function isValidScore(input) {
            return /^[0-9]$/.test(input.value.trim());
        }

        function setupRoundNavigation(group) {
            const rounds = Array.from(group.querySelectorAll('.round-block'));
            let currentRound = rounds.findIndex((round) => !round.hidden);

            if (currentRound < 0) {
                currentRound = 0;
            }

            function firstBlankScore(round) {
                return Array.from(round.querySelectorAll('.fixture-prediction input:not(:disabled)')).find((input) => input.value.trim() === '');
            }

            function showRound(index) {
                currentRound = Math.max(0, Math.min(index, rounds.length - 1));

                rounds.forEach((round, roundIndex) => {
                    round.hidden = roundIndex !== currentRound;
                    round.querySelector('.round-prev').disabled = currentRound === 0;
                    round.querySelector('.round-next').disabled = currentRound === rounds.length - 1;
                });
            }

            rounds.forEach((round, roundIndex) => {
                round.querySelector('.round-prev')?.addEventListener('click', () => showRound(roundIndex - 1));
                round.querySelector('.round-next')?.addEventListener('click', () => {
                    const blankScore = firstBlankScore(round);

                    if (round.dataset.locked !== '1' && blankScore) {
                        showToast('Existem valores não definidos nesta rodada. Digite todos os placares para avançar.');
                        blankScore.focus();
                        return;
                    }

                    showRound(roundIndex + 1);
                });
            });

            showRound(currentRound);
        }

        const groupBoards = Array.from(document.querySelectorAll('.group-board'));
        const globalRoundPrev = document.querySelector('#global-round-prev');
        const globalRoundNext = document.querySelector('#global-round-next');
        const currentRoundLabel = document.querySelector('#current-round-label');
        const currentRoundLockLabel = document.querySelector('#current-round-lock-label');
        const roundCount = Math.max(...groupBoards.map((group) => group.querySelectorAll('.round-block').length));
        let currentRoundIndex = 0;

        function ordinalRound(index) {
            return `${index + 1}ª rodada`;
        }

        function roundBlocks(index = currentRoundIndex) {
            return Array.from(document.querySelectorAll(`.round-block[data-round="${index}"]`));
        }

        function roundIsClosed(round) {
            if (!round) {
                return false;
            }

            const closesAt = Date.parse(round.dataset.closesAt);

            if (Number.isFinite(closesAt) && Date.now() >= closesAt) {
                return true;
            }

            return round.dataset.locked === '1';
        }

        function refreshRoundLocks() {
            document.querySelectorAll('.round-block').forEach((round) => {
                if (!roundIsClosed(round)) {
                    return;
                }

                round.dataset.locked = '1';
                round.querySelectorAll('.fixture-prediction input').forEach((input) => {
                    input.disabled = true;
                });

                const status = round.querySelector('.round-title em');

                if (status) {
                    status.textContent = 'Rodada fechada';
                    status.classList.add('is-closed');
                }
            });
        }

        function firstBlankScoreInRound(index = currentRoundIndex) {
            return roundBlocks(index)
                .flatMap((round) => Array.from(round.querySelectorAll('.fixture-prediction input:not(:disabled)')))
                .find((input) => input.value.trim() === '');
        }

        function canLeaveCurrentRound() {
            refreshRoundLocks();

            const activeRounds = roundBlocks();
            const hasOpenRound = activeRounds.some((round) => !roundIsClosed(round));
            const blankScore = firstBlankScoreInRound();

            if (hasOpenRound && blankScore) {
                showToast('Esta rodada ainda esta aberta. Preencha todos os palpites antes de ir para a proxima rodada.');
                blankScore.focus();
                return false;
            }

            return true;
        }

        function updateGlobalRoundControls() {
            const activeRound = roundBlocks()[0];
            const closed = roundIsClosed(activeRound);
            const closesAt = activeRound?.dataset.closesAt ? new Date(activeRound.dataset.closesAt) : null;

            currentRoundLabel.textContent = ordinalRound(currentRoundIndex);
            globalRoundPrev.disabled = currentRoundIndex === 0;
            globalRoundNext.disabled = currentRoundIndex === roundCount - 1;
            globalRoundNext.innerHTML = currentRoundIndex === roundCount - 1
                ? 'Ultima rodada'
                : `Ir para ${ordinalRound(currentRoundIndex + 1)} <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M9 18l6-6-6-6"></path></svg>`;

            if (closed) {
                currentRoundLockLabel.textContent = 'Rodada fechada';
                currentRoundLockLabel.classList.add('is-closed');
                return;
            }

            currentRoundLockLabel.classList.remove('is-closed');
            currentRoundLockLabel.textContent = closesAt
                ? `Fecha em ${closesAt.toLocaleString('pt-BR', { dateStyle: 'short', timeStyle: 'short' })}`
                : '';
        }

        function showRound(index, options = {}) {
            const nextIndex = Math.max(0, Math.min(index, roundCount - 1));

            if (options.validate && nextIndex > currentRoundIndex && !canLeaveCurrentRound()) {
                return;
            }

            currentRoundIndex = nextIndex;
            refreshRoundLocks();

            groupBoards.forEach((group) => {
                const rounds = Array.from(group.querySelectorAll('.round-block'));

                rounds.forEach((round, roundIndex) => {
                    round.hidden = roundIndex !== currentRoundIndex;
                    round.querySelector('.round-prev').disabled = currentRoundIndex === 0;
                    round.querySelector('.round-next').disabled = currentRoundIndex === roundCount - 1;
                });
            });

            updateGlobalRoundControls();
        }

        function setupRoundNavigation(group) {
            group.querySelectorAll('.round-block').forEach((round, roundIndex) => {
                round.querySelector('.round-prev')?.addEventListener('click', () => showRound(roundIndex - 1));
                round.querySelector('.round-next')?.addEventListener('click', () => showRound(roundIndex + 1));
            });
        }

        function setupLiveStandings(group) {
            const standingRows = Array.from(group.querySelectorAll('.standing-row'));
            const statsRows = Array.from(group.querySelectorAll('.stats-row'));
            const fixtures = Array.from(group.querySelectorAll('.fixture-row'));
            const standingsContainer = group.querySelector('.standings');
            const statsContainer = group.querySelector('.stats-table');

            function baseTable() {
                return Object.fromEntries(standingRows.map((row) => [
                    row.dataset.teamId,
                    {
                        id: row.dataset.teamId,
                        name: row.dataset.teamName,
                        points: 0,
                        played: 0,
                        wins: 0,
                        draws: 0,
                        losses: 0,
                        goals_for: 0,
                        goals_against: 0,
                        goal_difference: 0,
                        percentage: 0,
                    },
                ]));
            }

            function updateStats(stats, goalsFor, goalsAgainst) {
                stats.played += 1;
                stats.goals_for += goalsFor;
                stats.goals_against += goalsAgainst;
                stats.goal_difference = stats.goals_for - stats.goals_against;

                if (goalsFor > goalsAgainst) {
                    stats.wins += 1;
                    stats.points += 3;
                    return;
                }

                if (goalsFor === goalsAgainst) {
                    stats.draws += 1;
                    stats.points += 1;
                    return;
                }

                stats.losses += 1;
            }

            function renderTable() {
                const table = baseTable();

                fixtures.forEach((fixture) => {
                    const homeInput = fixture.querySelector('.home-score');
                    const awayInput = fixture.querySelector('.away-score');
                    const homeValue = fixture.dataset.homeScore || homeInput?.value;
                    const awayValue = fixture.dataset.awayScore || awayInput?.value;

                    if (homeValue === '' || awayValue === '') {
                        return;
                    }

                    const homeScore = Number(homeValue);
                    const awayScore = Number(awayValue);

                    if (!Number.isFinite(homeScore) || !Number.isFinite(awayScore)) {
                        return;
                    }

                    updateStats(table[fixture.dataset.homeId], homeScore, awayScore);
                    updateStats(table[fixture.dataset.awayId], awayScore, homeScore);
                });

                Object.values(table).forEach((stats) => {
                    stats.goal_difference = stats.goals_for - stats.goals_against;
                    stats.percentage = stats.played > 0 ? Math.round((stats.points / (stats.played * 3)) * 100) : 0;
                });

                const sorted = Object.values(table).sort((a, b) => {
                    return b.points - a.points
                        || b.wins - a.wins
                        || b.goal_difference - a.goal_difference
                        || b.goals_for - a.goals_for
                        || a.name.localeCompare(b.name);
                });

                sorted.forEach((stats, index) => {
                    const standingRow = group.querySelector(`.standing-row[data-team-id="${stats.id}"]`);
                    const statsRow = group.querySelector(`.stats-row[data-team-id="${stats.id}"]`);
                    const position = standingRow?.querySelector('.position');

                    if (position) {
                        position.textContent = index + 1;
                        position.className = `position position-${index + 1}`;
                    }

                    Object.entries(stats).forEach(([key, value]) => {
                        const statCell = statsRow?.querySelector(`[data-stat="${key}"]`);

                        if (statCell) {
                            statCell.textContent = value;
                        }
                    });

                    if (standingRow) {
                        standingsContainer.appendChild(standingRow);
                    }

                    if (statsRow) {
                        statsContainer.appendChild(statsRow);
                    }
                });
            }

            group.refreshStandings = renderTable;

            fixtures.forEach((fixture) => {
                fixture.querySelectorAll('input').forEach((input) => {
                    if (input.disabled) {
                        return;
                    }

                    input.addEventListener('input', () => {
                        sanitizeScoreInput(input);
                        renderTable();
                    });
                    input.addEventListener('beforeinput', guardScoreBeforeInput);
                    input.addEventListener('paste', () => window.setTimeout(() => {
                        sanitizeScoreInput(input);
                        renderTable();
                    }));
                });
            });

            renderTable();
        }

        const scoresUrl = @json(route('predictions.scores'));

        function updateFixtureScore(match) {
            const fixture = document.querySelector(`.fixture-row[data-match-id="${match.id}"]`);

            if (!fixture) {
                return;
            }

            const homeScore = match.home_score ?? '';
            const awayScore = match.away_score ?? '';
            const hasScore = homeScore !== '' && awayScore !== '';
            const isFinished = Boolean(match.is_finished);
            const label = fixture.querySelector('.match-score-label');

            fixture.dataset.homeScore = homeScore;
            fixture.dataset.awayScore = awayScore;
            fixture.dataset.isFinished = isFinished ? '1' : '0';

            if (!label) {
                return;
            }

            if (!hasScore) {
                label.hidden = true;
                label.textContent = '';
                label.classList.remove('live-score');
                return;
            }

            label.hidden = false;
            label.textContent = `${isFinished ? 'Resultado' : 'Ao vivo'}: ${homeScore} x ${awayScore}`;
            label.classList.toggle('live-score', !isFinished);
        }

        async function refreshScores() {
            try {
                const response = await fetch(scoresUrl, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (!response.ok) {
                    return;
                }

                const payload = await response.json();

                for (const match of payload.matches ?? []) {
                    updateFixtureScore(match);
                }

                document.querySelectorAll('.group-board').forEach((group) => {
                    group.refreshStandings?.();
                });
            } catch (error) {
                // The queue will try again; keep the page usable if a poll fails.
            }
        }

        document.querySelectorAll('.group-board').forEach((group) => {
            setupRoundNavigation(group);
            setupLiveStandings(group);
        });

        globalRoundPrev?.addEventListener('click', () => showRound(currentRoundIndex - 1));
        globalRoundNext?.addEventListener('click', () => showRound(currentRoundIndex + 1));
        window.setInterval(() => {
            refreshRoundLocks();
            updateGlobalRoundControls();
        }, 30000);
        window.setInterval(refreshScores, 20000);
        refreshScores();
        showRound(0);

        document.querySelector('#predictions-form')?.addEventListener('submit', (event) => {
            refreshRoundLocks();

            const openInputs = Array.from(document.querySelectorAll('.round-block:not([hidden]) .fixture-prediction input:not(:disabled)'));
            const invalidInput = openInputs.find((input) => input.value.trim() !== '' && !isValidScore(input));
            const blankInput = openInputs.find((input) => input.value.trim() === '');

            if (openInputs.length === 0) {
                event.preventDefault();
                showToast('Esta rodada esta fechada. Nao existem palpites abertos para salvar.');
                return;
            }

            if (invalidInput) {
                event.preventDefault();
                invalidInput.focus();
                showToast(kingsLeagueMessage);
                return;
            }

            if (blankInput) {
                event.preventDefault();
                blankInput.focus();
                showToast('Existem valores não definidos ainda. Preencha todos os placares para salvar.');
            }
        });

        document.querySelector('#predictions-form')?.addEventListener('submit', (event) => {
            if (event.defaultPrevented) {
                return;
            }

            document.querySelectorAll('.round-block[hidden] .fixture-prediction input').forEach((input) => {
                input.disabled = true;
            });
        });
    </script>
</body>
</html>
