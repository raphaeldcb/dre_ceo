<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - DRE CEO Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            overflow: hidden;
            color: #e0e0e0;
        }
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 20% 50%, rgba(26, 95, 122, 0.1) 0%, transparent 50%),
                        radial-gradient(circle at 80% 80%, rgba(15, 52, 96, 0.1) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }
        .register-container {
            background: #0f3460;
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4),
                        inset 0 1px 0 rgba(255, 255, 255, 0.1);
            overflow: hidden;
            max-width: 420px;
            width: 100%;
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            z-index: 1;
        }
        .register-header {
            background: linear-gradient(135deg, #0f3460 0%, #1a5f7a 100%);
            color: #f0f0f0;
            padding: 50px 30px 40px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .register-header h1 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 700;
            letter-spacing: -0.5px;
            color: #e0f0ff;
        }
        .register-header p {
            margin: 8px 0 0 0;
            opacity: 0.8;
            color: #b0d0e0;
            font-size: 0.95rem;
        }
        .register-body {
            padding: 40px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            color: #d0e0f0;
            font-weight: 600;
            font-size: 0.9rem;
            display: block;
            margin-bottom: 8px;
            letter-spacing: 0.3px;
        }
        .form-control {
            background: #1a4a6a;
            border: 1.5px solid rgba(255, 255, 255, 0.15);
            color: #f0f0f0;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }
        .form-control::placeholder {
            color: #80a0b0;
        }
        .form-control:focus {
            background: #1a4a6a;
            border-color: #1a5f7a;
            box-shadow: 0 0 0 3px rgba(26, 95, 122, 0.2);
            color: #f0f0f0;
            outline: none;
        }
        .btn-register {
            background: linear-gradient(135deg, #1a5f7a 0%, #244a68 100%);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 14px 24px;
            font-weight: 600;
            margin-top: 30px;
            color: #f0f0f0;
            border-radius: 10px;
            font-size: 1rem;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(26, 95, 122, 0.2);
        }
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(26, 95, 122, 0.3);
            background: linear-gradient(135deg, #244a68 0%, #2a5a80 100%);
            border-color: rgba(255, 255, 255, 0.3);
        }
        .btn-register:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }
        .alert {
            border-radius: 10px;
            border: 1px solid;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
        .alert-danger {
            background: rgba(244, 63, 94, 0.1);
            border-color: rgba(244, 63, 94, 0.2);
            color: #f87171;
        }
        .alert-success {
            background: rgba(96, 165, 250, 0.1);
            border-color: rgba(96, 165, 250, 0.2);
            color: #93c5fd;
        }
        .error-message {
            display: none;
        }
        .text-muted {
            color: #a0c0d0 !important;
            font-size: 0.85rem;
        }
        hr {
            border-color: rgba(255, 255, 255, 0.08);
            margin: 25px 0;
        }
        .register-link {
            color: #60a5fa;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        .register-link:hover {
            color: #93c5fd;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <h1>📝 Cadastro</h1>
            <p>Crie sua conta no DRE CEO</p>
        </div>
        <div class="register-body">
            <form id="registerForm">
                <div id="errorAlert" class="alert alert-danger error-message" role="alert"></div>
                <div id="successAlert" class="alert alert-success error-message" role="alert"></div>

                <div class="mb-3">
                    <label for="username" class="form-label">Usuário</label>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Escolha um usuário" required>
                    <small class="text-muted">Mínimo 3 caracteres</small>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Senha</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Digite uma senha" required>
                    <small class="text-muted">Mínimo 6 caracteres</small>
                </div>

                <div class="mb-3">
                    <label for="confirmPassword" class="form-label">Confirmar Senha</label>
                    <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Repita a senha" required>
                </div>

                <div class="mb-3">
                    <label for="name" class="form-label">Nome Completo</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Seu nome" required>
                </div>

                <button type="submit" class="btn btn-register btn-primary w-100">
                    <span id="btnText">✓ Criar Conta</span>
                    <span id="spinner" style="display: none;">
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Carregando...
                    </span>
                </button>
            </form>

            <hr class="my-4">
            <p class="text-center text-muted">
                Já tem uma conta? <a href="/">Faça login</a>
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const registerForm = document.getElementById('registerForm');
        const errorAlert = document.getElementById('errorAlert');
        const successAlert = document.getElementById('successAlert');
        const btnText = document.getElementById('btnText');
        const spinner = document.getElementById('spinner');

        registerForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            errorAlert.style.display = 'none';
            successAlert.style.display = 'none';

            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const name = document.getElementById('name').value.trim();

            // Validations
            if (username.length < 3) {
                errorAlert.textContent = '❌ Usuário deve ter pelo menos 3 caracteres';
                errorAlert.style.display = 'block';
                return;
            }

            if (password.length < 6) {
                errorAlert.textContent = '❌ Senha deve ter pelo menos 6 caracteres';
                errorAlert.style.display = 'block';
                return;
            }

            if (password !== confirmPassword) {
                errorAlert.textContent = '❌ As senhas não conferem';
                errorAlert.style.display = 'block';
                return;
            }

            if (name.length < 3) {
                errorAlert.textContent = '❌ Nome deve ter pelo menos 3 caracteres';
                errorAlert.style.display = 'block';
                return;
            }

            btnText.style.display = 'none';
            spinner.style.display = 'inline';
            registerForm.querySelector('button').disabled = true;

            try {
                const response = await fetch('/api/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ username, password, name })
                });

                const result = await response.json();

                if (result.success) {
                    successAlert.textContent = '✅ Cadastro realizado com sucesso! Redirecionando para login...';
                    successAlert.style.display = 'block';
                    setTimeout(() => {
                        window.location.href = '/';
                    }, 2000);
                } else {
                    errorAlert.textContent = '❌ ' + (result.error || 'Erro ao cadastrar');
                    errorAlert.style.display = 'block';
                }
            } catch (error) {
                errorAlert.textContent = '❌ Erro: ' + error.message;
                errorAlert.style.display = 'block';
            } finally {
                btnText.style.display = 'inline';
                spinner.style.display = 'none';
                registerForm.querySelector('button').disabled = false;
            }
        });
    </script>
</body>
</html>
