<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DRE CEO Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
        }
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
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
            opacity: 0.9;
        }
        .login-body {
            padding: 40px;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px;
            font-weight: 600;
            margin-top: 20px;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #5568d3 0%, #6a3f8f 100%);
            color: white;
        }
        .error-message {
            display: none;
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
