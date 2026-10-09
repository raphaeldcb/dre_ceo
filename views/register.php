<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - DRE CEO Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .register-container {
            background: #0f3460;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .register-header {
            background: linear-gradient(135deg, #0f3460 0%, #1a5f7a 100%);
            color: #f0f0f0;
            padding: 40px 30px;
            text-align: center;
        }
        .register-header h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: bold;
        }
        .register-header p {
            margin: 10px 0 0 0;
            opacity: 0.85;
            color: #d0d0d0;
        }
        .register-body {
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
        .btn-register {
            background: linear-gradient(135deg, #0f3460 0%, #1a5f7a 100%);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 12px;
            font-weight: 600;
            margin-top: 20px;
            color: #f0f0f0;
        }
        .btn-register:hover {
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
        .text-center a {
            color: #1a5f7a;
            text-decoration: none;
        }
        .text-center a:hover {
            color: #1a7fa0;
            text-decoration: underline;
        }
        hr {
            border-color: rgba(255, 255, 255, 0.1);
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
