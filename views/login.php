<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DRE CEO Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            background: linear-gradient(135deg, #0f172a 0%, #0a1929 50%, #132849 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            overflow: hidden;
        }
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 20% 50%, rgba(51, 102, 153, 0.1) 0%, transparent 50%),
                        radial-gradient(circle at 80% 80%, rgba(26, 95, 122, 0.1) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }
        .login-container {
            background: linear-gradient(135deg, #1a4a6a 0%, #0f3460 100%);
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3),
                        inset 0 1px 0 rgba(255, 255, 255, 0.1);
            overflow: hidden;
            max-width: 420px;
            width: 100%;
            border: 1px solid rgba(255, 255, 255, 0.15);
            position: relative;
            z-index: 1;
        }
        .login-header {
            background: linear-gradient(135deg, #1a5f7a 0%, #0f3460 100%);
            color: #f0f0f0;
            padding: 50px 30px 40px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .login-header i {
            font-size: 3rem;
            margin-bottom: 15px;
            background: linear-gradient(135deg, #4ade80 0%, #60a5fa 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .login-header h1 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        .login-header p {
            margin: 8px 0 0 0;
            opacity: 0.75;
            color: #a0c0d0;
            font-size: 0.95rem;
        }
        .login-body {
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
            background: rgba(255, 255, 255, 0.05);
            border: 1.5px solid rgba(255, 255, 255, 0.1);
            color: #f0f0f0;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }
        .form-control::placeholder {
            color: #708090;
        }
        .form-control:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: #4ade80;
            box-shadow: 0 0 0 3px rgba(74, 222, 128, 0.1);
            color: #f0f0f0;
            outline: none;
        }
        .btn-login {
            background: linear-gradient(135deg, #4ade80 0%, #22d3ee 100%);
            border: none;
            padding: 14px 24px;
            font-weight: 600;
            margin-top: 30px;
            color: #0f172a;
            border-radius: 10px;
            font-size: 1rem;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(74, 222, 128, 0.3);
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(74, 222, 128, 0.4);
            background: linear-gradient(135deg, #22d3ee 0%, #4ade80 100%);
            color: #0f172a;
        }
        .btn-login:disabled {
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
            background: rgba(248, 113, 113, 0.1);
            border-color: rgba(248, 113, 113, 0.3);
            color: #fca5a5;
        }
        .alert-success {
            background: rgba(74, 222, 128, 0.1);
            border-color: rgba(74, 222, 128, 0.3);
            color: #86efac;
        }
        .error-message {
            display: none;
        }
        .text-muted {
            color: #a0c0d0 !important;
            font-size: 0.85rem;
        }
        code {
            background: rgba(74, 222, 128, 0.15);
            color: #86efac;
            padding: 4px 8px;
            border-radius: 5px;
            font-family: 'Monaco', 'Courier New', monospace;
        }
        hr {
            border-color: rgba(255, 255, 255, 0.08);
            margin: 25px 0;
        }
        .register-link {
            color: #4ade80;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        .register-link:hover {
            color: #22d3ee;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <i class="fas fa-chart-line"></i>
            <h1>DRE CEO</h1>
            <p>Dashboard Financeiro</p>
        </div>
        <div class="login-body">
            <form id="loginForm">
                <div id="errorAlert" class="alert alert-danger error-message" role="alert"></div>

                <div class="form-group">
                    <label for="username" class="form-label">Usuário</label>
                    <input type="text" class="form-control" id="username" name="username" placeholder="seu usuário" required autofocus>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Senha</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="sua senha" required>
                </div>

                <button type="submit" class="btn btn-login w-100">
                    <span id="btnText"><i class="fas fa-sign-in-alt me-2"></i>Entrar</span>
                    <span id="spinner" style="display: none;">
                        <i class="fas fa-spinner fa-spin me-2"></i>Entrando...
                    </span>
                </button>
            </form>

            <hr>
            <div style="text-align: center;">
                <small class="text-muted" style="display: block; margin-bottom: 15px;">
                    <strong>Demo:</strong> <code>admin</code> / <code>123456</code>
                </small>
                <p class="text-muted" style="margin: 0;">
                    Não tem conta? <a href="/register" class="register-link">Cadastre-se</a>
                </p>
            </div>
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
