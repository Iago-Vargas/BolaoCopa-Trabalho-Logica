<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ranking - {{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body>
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
                <a class="nav-item active" href="{{ route('ranking.index') }}" aria-current="page">
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

        <main class="dashboard-main ranking-main">
            <section class="overview">
                <div>
                    <p class="eyebrow">Classificacao</p>
                    <h1>Ranking</h1>
                    <p class="lead">Pontuacao geral dos participantes, atualizada conforme os resultados oficiais entram.</p>
                </div>
            </section>

            <section class="ranking-summary" aria-label="Resumo do ranking">
                <article>
                    <span>Lider atual</span>
                    <strong>{{ $leader?->nickname ?? '-' }}</strong>
                    <em>{{ (int) ($leader?->points ?? 0) }} pts</em>
                </article>
                <article>
                    <span>Participantes</span>
                    <strong>{{ $totalUsers }}</strong>
                    <em>No bolao</em>
                </article>
                <article>
                    <span>Placar exato do lider</span>
                    <strong>{{ (int) ($leader?->exact_scores ?? 0) }}</strong>
                    <em>Acertos de 10 pts</em>
                </article>
            </section>

            <section class="ranking-table-panel">
                <div class="ranking-table-head">
                    <span>Pos</span>
                    <span>Participante</span>
                    <span>Pontos</span>
                    <span>Placares corretos</span>
                    <span>Palpites</span>
                </div>

                <div class="ranking-table-body">
                    @foreach ($ranking as $row)
                        <article class="ranking-table-row {{ $row->id === auth()->id() ? 'is-current-user' : '' }}">
                            <span class="ranking-position position-{{ min($row->position, 3) }}">{{ $row->position }}</span>
                            <div class="ranking-person">
                                <div class="avatar">{{ strtoupper(substr($row->nickname, 0, 1)) }}</div>
                                <div>
                                    <strong>{{ $row->nickname }}</strong>
                                    <em>{{ $row->name }}</em>
                                </div>
                            </div>
                            <strong class="ranking-points">{{ (int) $row->points }}</strong>
                            <span class="ranking-exacts">{{ (int) $row->exact_scores }}</span>
                            <span class="ranking-count">{{ (int) $row->predictions_count }}</span>
                        </article>
                    @endforeach
                </div>
            </section>
        </main>
    </div>

    <script>
        const sidebarToggle = document.querySelector('#sidebar-toggle');
        const storedSidebar = localStorage.getItem('sidebar-collapsed');

        if (storedSidebar === 'true') {
            document.body.classList.add('sidebar-collapsed');
        }

        sidebarToggle?.addEventListener('click', () => {
            document.body.classList.toggle('sidebar-collapsed');
            localStorage.setItem('sidebar-collapsed', document.body.classList.contains('sidebar-collapsed'));
        });
    </script>
</body>
</html>
