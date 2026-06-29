<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lógica - {{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body>
    <div class="toast-stack" id="toast-stack" aria-live="polite" aria-atomic="true"></div>

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
                <a class="nav-item active" href="{{ route('dashboard') }}" aria-current="page">
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

        <main class="dashboard-main">
            <section class="overview">
                <div>
                    <p class="eyebrow">Sobre o projeto</p>
                    <h1>Lógica para Computação</h1>
                    <p class="lead">Como o Bolão Copa 2026 foi construído — com autenticação, palpites, ranking, chaveamento e integração com API externa para atualizar os placares.</p>
                </div>
            </section>

            <section class="teacher-showcase" aria-label="Resumo visual do trabalho">
                <div class="showcase-copy">
                    <p class="eyebrow">Resumo para apresentação</p>
                    <h2>Um sistema real para demonstrar lógica aplicada</h2>
                    <p>O projeto transforma regras formais em uma experiência completa: o aluno registra palpites, o sistema recebe placares oficiais pela API, avalia proposições verdadeiras ou falsas e recalcula a pontuação automaticamente.</p>
                </div>
                <div class="showcase-stats" aria-label="Principais entregas do sistema">
                    <div class="showcase-stat">
                        <span>01</span>
                        <strong>Palpites</strong>
                        <p>Entrada dos dados do usuário antes dos jogos.</p>
                    </div>
                    <div class="showcase-stat">
                        <span>02</span>
                        <strong>API externa</strong>
                        <p>Comunicação HTTP com resultados da Copa 2026.</p>
                    </div>
                    <div class="showcase-stat">
                        <span>03</span>
                        <strong>Ranking</strong>
                        <p>Saída calculada pelas regras lógicas.</p>
                    </div>
                </div>
            </section>

            <section class="logic-block" aria-labelledby="stack-heading">
                <div class="logic-block-header">
                    <h2 id="stack-heading">Stack do Projeto</h2>
                    <p>Linguagens e ferramentas que fazem o bolão funcionar.</p>
                </div>
                <div class="tech-grid">
                    <article class="tech-card">
                        <div class="tech-badge php">PHP</div>
                        <strong>PHP 8.3</strong>
                        <span>Linguagem principal do backend</span>
                    </article>
                    <article class="tech-card">
                        <div class="tech-badge laravel">L</div>
                        <strong>Laravel 13</strong>
                        <span>Framework MVC — rotas, banco e autenticação</span>
                    </article>
                    <article class="tech-card">
                        <div class="tech-badge js">JS</div>
                        <strong>JavaScript</strong>
                        <span>Navegação de rodadas e validações no cliente</span>
                    </article>
                    <article class="tech-card">
                        <div class="tech-badge db">DB</div>
                        <strong>MySQL / MariaDB</strong>
                        <span>Banco de dados relacional</span>
                    </article>
                    <article class="tech-card">
                        <div class="tech-badge blade">{ }</div>
                        <strong>Blade</strong>
                        <span>Template engine nativa do Laravel</span>
                    </article>
                    <article class="tech-card">
                        <div class="tech-badge css3">CSS</div>
                        <strong>CSS3</strong>
                        <span>Estilização responsiva sem bibliotecas externas</span>
                    </article>
                    <article class="tech-card featured">
                        <div class="tech-badge api">API</div>
                        <strong>WorldCup26 API</strong>
                        <span>Fonte externa dos jogos, placares e status das partidas</span>
                    </article>
                </div>
            </section>

            <section class="logic-block" aria-labelledby="api-heading">
                <div class="logic-block-header">
                    <h2 id="api-heading">API de Comunicação Utilizada</h2>
                    <p>O sistema consome uma API externa para buscar jogos, placares ao vivo e resultados finalizados da Copa 2026.</p>
                </div>

                <div class="api-panel">
                    <div class="api-endpoint">
                        <span>Endpoint configurado no .env</span>
                        <code>WORLDCUP_2026_GAMES_URL=https://worldcup26.ir/get/games</code>
                    </div>

                    <div class="api-flow" aria-label="Fluxo de comunicação com a API">
                        <div class="api-flow-step">
                            <span>1</span>
                            <strong>Requisição HTTP</strong>
                            <p>Laravel usa o client <code>Http</code> com timeout, retry e resposta JSON.</p>
                        </div>
                        <div class="api-flow-arrow" aria-hidden="true">→</div>
                        <div class="api-flow-step">
                            <span>2</span>
                            <strong>Sincronização</strong>
                            <p><code>WorldCupResultsSyncer</code> compara a API com as partidas salvas.</p>
                        </div>
                        <div class="api-flow-arrow" aria-hidden="true">→</div>
                        <div class="api-flow-step">
                            <span>3</span>
                            <strong>Atualização</strong>
                            <p>Placares, status e jogos eliminatórios são persistidos no banco.</p>
                        </div>
                        <div class="api-flow-arrow" aria-hidden="true">→</div>
                        <div class="api-flow-step">
                            <span>4</span>
                            <strong>Pontuação</strong>
                            <p>O sistema recalcula pontos e reflete o resultado no ranking.</p>
                        </div>
                    </div>

                    <div class="api-details">
                        <article>
                            <strong>Segurança e confiabilidade</strong>
                            <p>A chamada tem limite de 20 segundos, duas tentativas automáticas e validação de erro com <code>throw()</code>.</p>
                        </article>
                        <article>
                            <strong>Automação</strong>
                            <p>O job <code>SyncWorldCupResults</code> foi agendado para rodar a cada minuto e manter os resultados atualizados.</p>
                        </article>
                        <article>
                            <strong>Ligação com a lógica</strong>
                            <p>A API fornece o fato oficial; o sistema usa esse fato para decidir se as proposições do palpite são V ou F.</p>
                        </article>
                    </div>
                </div>
            </section>

            <section class="logic-block" aria-labelledby="why-heading">
                <div class="logic-block-header">
                    <h2 id="why-heading">Por que um Bolão?</h2>
                </div>
                <div class="why-box">
                    <p>O bolão da Copa do Mundo é um problema real com lógica clara: há <strong>entradas</strong> (palpites do usuário), <strong>processamento</strong> (regras de pontuação) e <strong>saídas</strong> (pontos e ranking). Esse tipo de sistema é ideal para demonstrar como proposições lógicas se transformam em código real e testável.</p>
                    <p>Em vez de exemplos abstratos como "p = verdadeiro", o bolão exibe proposições concretas a cada partida:</p>
                    <ul>
                        <li><strong>p</strong>: "O participante acertou o resultado?" — vitória, derrota ou empate.</li>
                        <li><strong>q</strong>: "O participante acertou o placar exato?" — os dois números.</li>
                    </ul>
                    <p>Cada palpite avaliado pelo sistema é, na prática, uma tabela verdade rodando em produção.</p>
                </div>
            </section>

            <section class="logic-block" aria-labelledby="prop-heading">
                <div class="logic-block-header">
                    <h2 id="prop-heading">Proposições no Sistema de Pontuação</h2>
                    <p>Uma proposição é um enunciado que possui valor <strong>Verdadeiro (V)</strong> ou <strong>Falso (F)</strong>.</p>
                </div>

                <div class="prop-def-grid">
                    <div class="prop-def-card">
                        <span class="prop-symbol">p</span>
                        <strong>Proposição p — Resultado</strong>
                        <p>"O participante acertou o resultado: quem venceu ou se houve empate."</p>
                    </div>
                    <div class="prop-def-card">
                        <span class="prop-symbol">q</span>
                        <strong>Proposição q — Placar Exato</strong>
                        <p>"O participante acertou os dois placares. Nota: q implica p, pois quem acerta o placar acerta o resultado."</p>
                    </div>
                </div>

                <div class="logic-block-header">
                    <h3>Conectivos Lógicos Usados</h3>
                </div>
                <div class="prop-connector-grid">
                    <div class="connector-card">
                        <span class="conn-symbol">∧</span>
                        <span class="conn-name">E (AND)</span>
                        <span class="conn-desc">Verdadeiro somente se <em>ambas</em> as proposições forem V.</span>
                    </div>
                    <div class="connector-card">
                        <span class="conn-symbol">∨</span>
                        <span class="conn-name">OU (OR)</span>
                        <span class="conn-desc">Verdadeiro se <em>pelo menos uma</em> das proposições for V.</span>
                    </div>
                    <div class="connector-card">
                        <span class="conn-symbol">¬</span>
                        <span class="conn-name">NÃO (NOT)</span>
                        <span class="conn-desc">Inverte o valor: <em>¬V = F</em> e <em>¬F = V</em>.</span>
                    </div>
                    <div class="connector-card">
                        <span class="conn-symbol">→</span>
                        <span class="conn-name">SE…ENTÃO</span>
                        <span class="conn-desc">Falso somente quando antecedente é V e consequente é F.</span>
                    </div>
                </div>

                <div class="truth-table-wrapper">
                    <h3>Regras de Pontuação como Proposições</h3>
                    <div class="scoring-rules">
                        <div class="scoring-rule">
                            <span class="rule-formula">SE p ∧ q ENTÃO</span>
                            <span class="rule-text">acertou resultado <em>e</em> placar exato</span>
                            <span class="rule-pts pts-10">10 pts</span>
                        </div>
                        <div class="scoring-rule">
                            <span class="rule-formula">SE p ∧ ¬q ENTÃO</span>
                            <span class="rule-text">acertou resultado mas <em>não</em> o placar exato</span>
                            <span class="rule-pts pts-3">3 pts</span>
                        </div>
                        <div class="scoring-rule">
                            <span class="rule-formula">SE ¬p ENTÃO</span>
                            <span class="rule-text">errou o resultado (e portanto o placar)</span>
                            <span class="rule-pts pts-0">0 pts</span>
                        </div>
                    </div>

                    <h3>Tabela Verdade</h3>
                    <table class="truth-table" aria-label="Tabela verdade da pontuação">
                        <thead>
                            <tr>
                                <th>p — Resultado</th>
                                <th>q — Placar exato</th>
                                <th>Pontuação</th>
                                <th>Situação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="tt-true">V</td>
                                <td class="tt-true">V</td>
                                <td class="tt-pts-10">10 pts</td>
                                <td>Placar exato</td>
                            </tr>
                            <tr>
                                <td class="tt-true">V</td>
                                <td class="tt-false">F</td>
                                <td class="tt-pts-3">3 pts</td>
                                <td>Resultado correto</td>
                            </tr>
                            <tr>
                                <td class="tt-false">F</td>
                                <td class="tt-false">F</td>
                                <td class="tt-pts-0">0 pts</td>
                                <td>Erro</td>
                            </tr>
                            <tr>
                                <td class="tt-false">F</td>
                                <td class="tt-na">V</td>
                                <td class="tt-na">—</td>
                                <td class="tt-na">Impossível (q → p)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="logic-block" aria-labelledby="setup-heading">
                <div class="logic-block-header">
                    <h2 id="setup-heading">Como Rodar o Projeto</h2>
                    <p>Requisitos e passos para executar o bolão localmente.</p>
                </div>
                <div class="setup-wrapper">
                    <div class="setup-panel">
                        <h3>Requisitos</h3>
                        <ul class="setup-steps">
                            <li class="setup-step"><span class="step-num">✓</span><span>PHP 8.3 ou superior</span></li>
                            <li class="setup-step"><span class="step-num">✓</span><span>Composer — gerenciador de pacotes PHP</span></li>
                            <li class="setup-step"><span class="step-num">✓</span><span>MySQL ou MariaDB</span></li>
                            <li class="setup-step"><span class="step-num">✓</span><span>Node.js (opcional, para compilar assets)</span></li>
                        </ul>
                    </div>
                    <div class="setup-panel">
                        <h3>Passos</h3>
                        <ol class="setup-steps">
                            <li class="setup-step"><span class="step-num">1</span><span>Clone o repositório e entre na pasta</span></li>
                            <li class="setup-step"><span class="step-num">2</span><span>Execute <code>composer install</code></span></li>
                            <li class="setup-step"><span class="step-num">3</span><span>Copie <code>.env.example</code> para <code>.env</code> e configure o banco</span></li>
                            <li class="setup-step"><span class="step-num">4</span><span>Execute <code>php artisan migrate --seed</code></span></li>
                            <li class="setup-step"><span class="step-num">5</span><span>Inicie com <code>php artisan serve</code></span></li>
                        </ol>
                    </div>
                </div>
            </section>
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
            if (!toastStack || !message) return;
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

        @if (session('login_success'))
            showToast(@json(session('login_success')));
        @endif
    </script>
</body>
</html>
