<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body data-auth-mode="{{ old('form_mode') === 'register' || $errors->any() ? 'register' : 'login' }}">
    <div class="toast-stack" id="toast-stack" aria-live="polite" aria-atomic="true"></div>

    @if (session('registered_success'))
        <div class="success-modal" id="success-modal" role="status" aria-live="polite">
            <div class="success-box">
                <div class="success-icon">&#10003;</div>
                <strong>{{ session('registered_success') }}</strong>
                <span>Agora voce ja pode entrar usando seu nickname e senha.</span>
            </div>
        </div>
    @endif

    <main class="page">
        <section class="intro">
            <div class="brand">
                <img class="brand-mark" src="{{ asset('images/world-cup-2026-emblem.svg') }}" alt="">
                <span>Bol&atilde;o da Copa</span>
            </div>

            <div class="headline">
                <h1>Entre no jogo antes do apito.</h1>
                <p>Palpites, placares e pontuacao deduzida por regras logicas.</p>
            </div>
        </section>

        <section class="login-side">
            <div class="login-panel">
                <div class="auth-stage">
                    <div class="auth-view login-view">
                        <div class="panel-top">
                            <p class="eyebrow">Acesso</p>
                            <h2>Bem-vindo de volta</h2>
                        </div>

                        <form class="form" id="login-form" method="post" action="{{ route('login.store') }}" novalidate>
                            @csrf
                            <div class="field">
                                <label for="login-nickname">Nickname</label>
                                <input id="login-nickname" name="nickname" type="text" autocomplete="username" placeholder="Seu nickname" value="{{ old('login_nickname') }}" required>
                            </div>

                            <div class="field">
                                <label for="login-password">Senha</label>
                                <input id="login-password" name="password" type="password" autocomplete="current-password" placeholder="Sua senha" required>
                            </div>

                            <div class="actions">
                                <button class="button primary" type="submit">Entrar</button>
                                <button class="button secondary" type="button" id="show-register">Registrar</button>
                            </div>
                        </form>
                    </div>

                    <div class="auth-view register-view">
                        <div class="panel-top">
                            <p class="eyebrow">Cadastro</p>
                            <h2>Crie sua conta</h2>
                        </div>

                        <form class="form" id="register-form" method="post" action="{{ route('register.store') }}" novalidate>
                            @csrf
                            <input type="hidden" name="form_mode" value="register">

                            <div class="field">
                                <label for="name">Nome</label>
                                <input id="name" class="@error('name') is-invalid @enderror" name="name" type="text" autocomplete="name" placeholder="Nome e sobrenome" value="{{ old('name') }}" required>
                                @error('name')
                                    <span class="error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="field">
                                <label for="nickname">Nickname</label>
                                <input id="nickname" class="@error('nickname') is-invalid @enderror" name="nickname" type="text" autocomplete="username" placeholder="Seu nickname" value="{{ old('nickname') }}" required>
                                @error('nickname')
                                    <span class="error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="field">
                                <label for="password">Senha</label>
                                <div class="password-row">
                                    <input id="password" class="@error('password') is-invalid @enderror" name="password" type="password" autocomplete="new-password" placeholder="Minimo 6 e caractere especial" required>
                                    <button class="password-tool password-toggle" type="button" aria-label="Mostrar senha">
                                        <svg class="eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                            <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                        <svg class="eye-closed" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true" hidden>
                                            <path d="M3 3l18 18"></path>
                                            <path d="M10.6 10.6A2 2 0 0 0 12 14a2 2 0 0 0 1.4-.6"></path>
                                            <path d="M7.1 7.5C3.8 9.2 2 12 2 12s3.5 6 10 6c1.5 0 2.8-.3 3.9-.8"></path>
                                            <path d="M17.7 14.2C20.5 12.6 22 12 22 12s-3.5-6-10-6c-.8 0-1.5.1-2.2.2"></path>
                                        </svg>
                                    </button>
                                    <button class="password-tool generate-password" id="generate-password" type="button">GERAR</button>
                                </div>
                                @error('password')
                                    <span class="error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="field">
                                <label for="password_confirmation">Confirmar senha</label>
                                <div class="password-row confirm-row">
                                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" placeholder="Repita sua senha" required>
                                    <button class="password-tool password-toggle" type="button" aria-label="Mostrar senha">
                                        <svg class="eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                            <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                        <svg class="eye-closed" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true" hidden>
                                            <path d="M3 3l18 18"></path>
                                            <path d="M10.6 10.6A2 2 0 0 0 12 14a2 2 0 0 0 1.4-.6"></path>
                                            <path d="M7.1 7.5C3.8 9.2 2 12 2 12s3.5 6 10 6c1.5 0 2.8-.3 3.9-.8"></path>
                                            <path d="M17.7 14.2C20.5 12.6 22 12 22 12s-3.5-6-10-6c-.8 0-1.5.1-2.2.2"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="actions">
                                <button class="button primary" type="submit">Salvar</button>
                                <button class="button secondary" type="button" id="show-login">Voltar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script>
        const body = document.body;
        const showRegister = document.querySelector('#show-register');
        const showLogin = document.querySelector('#show-login');
        const successModal = document.querySelector('#success-modal');
        const toastStack = document.querySelector('#toast-stack');
        const loginForm = document.querySelector('#login-form');
        const registerForm = document.querySelector('#register-form');
        const passwordInput = document.querySelector('#password');
        const passwordConfirmationInput = document.querySelector('#password_confirmation');
        const generatePasswordButton = document.querySelector('#generate-password');
        const passwordToggleButtons = document.querySelectorAll('.password-toggle');

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

        showRegister?.addEventListener('click', () => {
            body.dataset.authMode = 'register';
            window.setTimeout(() => document.querySelector('#name')?.focus(), 180);
        });

        showLogin?.addEventListener('click', () => {
            body.dataset.authMode = 'login';
            window.setTimeout(() => document.querySelector('#login-nickname')?.focus(), 180);
        });

        if (successModal) {
            window.setTimeout(() => {
                successModal.classList.add('is-hidden');
                window.setTimeout(() => successModal.remove(), 260);
            }, 3000);
        }

        function generatePassword(length = 15) {
            const lower = 'abcdefghijklmnopqrstuvwxyz';
            const upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            const numbers = '0123456789';
            const special = '!@#$%&*?+-_';
            const all = lower + upper + numbers + special;
            const required = [
                lower[Math.floor(Math.random() * lower.length)],
                upper[Math.floor(Math.random() * upper.length)],
                numbers[Math.floor(Math.random() * numbers.length)],
                special[Math.floor(Math.random() * special.length)],
            ];

            const remaining = Array.from({ length: length - required.length }, () => all[Math.floor(Math.random() * all.length)]);
            const chars = [...required, ...remaining];

            for (let index = chars.length - 1; index > 0; index -= 1) {
                const swapIndex = Math.floor(Math.random() * (index + 1));
                [chars[index], chars[swapIndex]] = [chars[swapIndex], chars[index]];
            }

            return chars.join('');
        }

        function setPasswordVisibility(visible) {
            const type = visible ? 'text' : 'password';
            passwordInput?.setAttribute('type', type);
            passwordConfirmationInput?.setAttribute('type', type);

            passwordToggleButtons.forEach((button) => {
                button.setAttribute('aria-label', visible ? 'Ocultar senha' : 'Mostrar senha');
                button.querySelector('.eye-open').hidden = visible;
                button.querySelector('.eye-closed').hidden = !visible;
            });
        }

        passwordToggleButtons.forEach((button) => {
            button.addEventListener('click', () => {
                setPasswordVisibility(passwordInput?.type === 'password');
            });
        });

        generatePasswordButton?.addEventListener('click', () => {
            const password = generatePassword();

            if (passwordInput && passwordConfirmationInput) {
                passwordInput.value = password;
                passwordConfirmationInput.value = password;
                passwordInput.classList.remove('is-invalid');
                passwordConfirmationInput.classList.remove('is-invalid');
                showToast('Senha gerada e confirmada automaticamente.');
            }
        });

        loginForm?.addEventListener('submit', (event) => {
            const requiredFields = [
                { selector: '#login-nickname', label: 'Nickname' },
                { selector: '#login-password', label: 'Senha' },
            ];

            const blankField = requiredFields.find((field) => {
                const input = loginForm.querySelector(field.selector);
                return input && input.value.trim() === '';
            });

            if (blankField) {
                event.preventDefault();
                const input = loginForm.querySelector(blankField.selector);
                input?.focus();
                showToast(`Informe o campo ${blankField.label}.`);
            }
        });

        registerForm?.addEventListener('submit', (event) => {
            const requiredFields = [
                { selector: '#name', label: 'Nome' },
                { selector: '#nickname', label: 'Nickname' },
                { selector: '#password', label: 'Senha' },
                { selector: '#password_confirmation', label: 'Confirmar senha' },
            ];

            const blankField = requiredFields.find((field) => {
                const input = registerForm.querySelector(field.selector);
                return input && input.value.trim() === '';
            });

            if (blankField) {
                event.preventDefault();
                const input = registerForm.querySelector(blankField.selector);
                input?.focus();
                showToast(`Informe o campo ${blankField.label}.`);
                return;
            }

            if (passwordInput?.value !== passwordConfirmationInput?.value) {
                event.preventDefault();
                passwordInput?.classList.add('is-invalid');
                passwordConfirmationInput?.classList.add('is-invalid');
                passwordConfirmationInput?.focus();
                showToast('A senha e a confirmacao precisam ser iguais.');
            }
        });

        @if ($errors->any())
            showToast(@json($errors->first()));
        @endif

        @if (session('login_error'))
            showToast(@json(session('login_error')));
        @endif
    </script>
</body>
</html>

