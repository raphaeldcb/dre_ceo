<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DRE CEO Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background: #0f3460;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .login-header {
            background: linear-gradient(135deg, #0f3460 0%, #1a5f7a 100%);
            color: #f0f0f0;
            padding: 40px 30px;
            text-align: center;
        }
        .login-header h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: bold;
        }
        .login-header p {
            margin: 10px 0 0 0;
            opacity: 0.85;
            color: #d0d0d0;
        }
        .login-body {
            padding: 40px;
        }
        .form-label {
            color: #e0f0ff;
            font-weight: 500;
        }
        .form-control {
            background: #1a4a6a;
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #f0f0f0;
        }
        .form-control::placeholder {
            color: #80a0b0;
        }
        .form-control:focus {
            background: #1a4a6a;
            border-color: #1a5f7a;
            box-shadow: 0 0 0 0.2rem rgba(26, 95, 122, 0.25);
            color: #f0f0f0;
        }
        .btn-login {
            background: linear-gradient(135deg, #0f3460 0%, #1a5f7a 100%);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 12px;
            font-weight: 600;
            margin-top: 20px;
            color: #f0f0f0;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #1a5f7a 0%, #244a68 100%);
            border-color: rgba(255, 255, 255, 0.3);
            color: #f0f0f0;
        }
        .error-message {
            display: none;
        }
        .text-muted {
            color: #b0d0e0 !important;
        }
        code {
            background: rgba(26, 95, 122, 0.3);
            color: #e0f0ff;
            padding: 2px 6px;
            border-radius: 3px;
        }
        hr {
            border-color: rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>📊 DRE CEO</h1>
            <p>Dashboard de Gestão Financeira</p>
        </div>
        <div class="login-body">
            <form id="loginForm">
                <div id="errorAlert" class="alert alert-danger error-message" role="alert"></div>

                <div class="mb-3">
                    <label for="username" class="form-label">Usuário</label>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Digite seu usuário" required autofocus>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Senha</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Digite sua senha" required>
                </div>

                <button type="submit" class="btn btn-login btn-primary w-100">
                    <span id="btnText">🔐 Entrar</span>
                    <span id="spinner" style="display: none;">
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Carregando...
                    </span>
                </button>
            </form>

            <hr class="my-4">
            <small class="text-muted text-center d-block">
                <strong>Credenciais de Demo:</strong><br>
                Usuário: <code>admin</code><br>
                Senha: <code>123456</code>
            </small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const loginForm = document.getElementById('loginForm');
        const errorAlert = document.getElementById('errorAlert');
        const btnText = document.getElementById('btnText');
        const spinner = document.getElementById('spinner');

        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            errorAlert.style.display = 'none';

            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;

            btnText.style.display = 'none';
            spinner.style.display = 'inline';
            loginForm.querySelector('button').disabled = true;

            try {
                const response = await fetch('/api/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ username, password })
                });

                const result = await response.json();

                if (result.success) {
                    window.location.href = '/home';
                } else {
                    errorAlert.textContent = '❌ ' + (result.error || 'Erro ao fazer login');
                    errorAlert.style.display = 'block';
                }
            } catch (error) {
                errorAlert.textContent = '❌ Erro: ' + error.message;
                errorAlert.style.display = 'block';
            } finally {
                btnText.style.display = 'inline';
                spinner.style.display = 'none';
                loginForm.querySelector('button').disabled = false;
            }
        });
    </script>
</body>
</html>
