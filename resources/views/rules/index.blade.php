<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Regras - {{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body>
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
                <a class="nav-item active" href="{{ route('rules.index') }}" aria-current="page">
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

        <main class="dashboard-main rules-main">
            <section class="overview">
                <div>
                    <p class="eyebrow">Pontuação</p>
                    <h1>Regras</h1>
                    <p class="lead">Os pontos são calculados automaticamente quando a API confirma o resultado oficial.</p>
                </div>
            </section>

            <section class="rules-grid" aria-label="Regras de pontuação">
                <article class="rule-card exact">
                    <strong>10 pts</strong>
                    <h2>Placar exato</h2>
                    <p>Você acertou o vencedor (ou empate) <em>e</em> também acertou os dois placares.</p>
                    <div class="rule-example">
                        <strong>Exemplo:</strong> palpite 2 × 0, resultado 2 × 0.
                    </div>
                </article>

                <article class="rule-card partial">
                    <strong>3 pts</strong>
                    <h2>Resultado correto</h2>
                    <p>Você acertou quem venceu ou que seria empate, mas errou o placar exato.</p>
                    <div class="rule-example">
                        <strong>Exemplo:</strong> palpite 1 × 0, resultado 2 × 0.
                    </div>
                </article>

                <article class="rule-card wrong">
                    <strong>0 pts</strong>
                    <h2>Erro</h2>
                    <p>Você errou o vencedor ou apostou empate quando houve vencedor (ou vice-versa).</p>
                    <div class="rule-example">
                        <strong>Exemplo:</strong> palpite 1 × 1, resultado 2 × 0.
                    </div>
                </article>
            </section>

            <div class="examples-panel">
                <h2>Exemplos Práticos</h2>
                <table class="example-table" aria-label="Exemplos de pontuação">
                    <thead>
                        <tr>
                            <th>Palpite</th>
                            <th>Resultado oficial</th>
                            <th>Situação</th>
                            <th>Pontos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>2 × 1</td>
                            <td>2 × 1</td>
                            <td>Placar exato — vencedor e placares corretos</td>
                            <td><span class="example-pts pts-10">10 pts</span></td>
                        </tr>
                        <tr>
                            <td>1 × 0</td>
                            <td>3 × 0</td>
                            <td>Resultado correto — vencedor certo, placar errado</td>
                            <td><span class="example-pts pts-3">3 pts</span></td>
                        </tr>
                        <tr>
                            <td>1 × 1</td>
                            <td>1 × 1</td>
                            <td>Placar exato — empate e placares corretos</td>
                            <td><span class="example-pts pts-10">10 pts</span></td>
                        </tr>
                        <tr>
                            <td>2 × 0</td>
                            <td>0 × 2</td>
                            <td>Erro — apostou na equipe errada como vencedora</td>
                            <td><span class="example-pts pts-0">0 pts</span></td>
                        </tr>
                        <tr>
                            <td>1 × 1</td>
                            <td>2 × 0</td>
                            <td>Erro — apostou empate, houve vencedor</td>
                            <td><span class="example-pts pts-0">0 pts</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <section class="rules-panel">
                <h2>Como ler os palpites</h2>
                <div class="rules-legend">
                    <div>
                        <span class="legend-box hit"></span>
                        <div>
                            <strong>Verde</strong>
                            <p>O número do palpite bateu com o placar oficial.</p>
                        </div>
                    </div>
                    <div>
                        <span class="legend-box miss"></span>
                        <div>
                            <strong>Vermelho</strong>
                            <p>O número do palpite não bateu com o placar oficial.</p>
                        </div>
                    </div>
                    <div>
                        <span class="legend-box pending"></span>
                        <div>
                            <strong>Cinza</strong>
                            <p>Jogo ainda sem resultado oficial da API.</p>
                        </div>
                    </div>
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
