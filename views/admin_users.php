<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuários - DRE CEO Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
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
        .container-main {
            position: relative;
            z-index: 1;
            max-width: 900px;
            margin: 30px auto;
            padding: 0 20px;
        }
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .header-section h1 {
            color: #e0f0ff;
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
        }
        .btn-back {
            background: linear-gradient(135deg, #1a5f7a 0%, #244a68 100%);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #f0f0f0;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(26, 95, 122, 0.3);
            color: #f0f0f0;
        }
        .card-container {
            background: #0f3460;
            border-radius: 15px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 30px;
            margin-bottom: 30px;
        }
        .form-section {
            margin-bottom: 40px;
            padding-bottom: 30px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .form-section h2 {
            color: #d0e0f0;
            font-size: 1.3rem;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .form-group {
            margin-bottom: 15px;
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
            border-radius: 8px;
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
        .btn-create {
            background: linear-gradient(135deg, #1a5f7a 0%, #244a68 100%);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 12px 24px;
            font-weight: 600;
            color: #f0f0f0;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .btn-create:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(26, 95, 122, 0.3);
            background: linear-gradient(135deg, #244a68 0%, #2a5a80 100%);
            color: #f0f0f0;
            text-decoration: none;
        }
        .btn-create:disabled {
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
        .users-list {
            margin-top: 30px;
        }
        .users-list h2 {
            color: #d0e0f0;
            font-size: 1.3rem;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .user-item {
            background: #1a4a6a;
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .user-info h3 {
            color: #e0f0ff;
            font-size: 1rem;
            margin: 0 0 5px 0;
            font-weight: 600;
        }
        .user-info p {
            color: #a0c0d0;
            font-size: 0.85rem;
            margin: 0;
        }
        .user-badge {
            background: rgba(26, 95, 122, 0.3);
            color: #60a5fa;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .admin-badge {
            background: rgba(96, 144, 168, 0.3);
            color: #a0c0d0;
        }
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #a0c0d0;
        }
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <div class="container-main">
        <div class="header-section">
            <h1><i class="fas fa-users me-2"></i>Gerenciar Usuários</h1>
            <a href="/home" class="btn-back">
                <i class="fas fa-arrow-left me-2"></i>Voltar
            </a>
        </div>

        <div class="card-container">
            <div class="form-section">
                <h2>Criar Novo Usuário</h2>

                <div id="successAlert" class="alert alert-success" style="display: none;" role="alert"></div>
                <div id="errorAlert" class="alert alert-danger" style="display: none;" role="alert"></div>

                <form id="createUserForm">
                    <div class="form-group">
                        <label for="username" class="form-label">Usuário</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Mínimo 3 caracteres" required>
                    </div>

                    <div class="form-group">
                        <label for="name" class="form-label">Nome Completo</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Nome do usuário" required>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Mínimo 6 caracteres" required>
                    </div>

                    <button type="submit" class="btn btn-create">
                        <span id="btnText"><i class="fas fa-user-plus me-2"></i>Criar Usuário</span>
                        <span id="spinner" style="display: none;">
                            <i class="fas fa-spinner fa-spin me-2"></i>Criando...
                        </span>
                    </button>
                </form>
            </div>

            <div class="users-list">
                <h2>Usuários Cadastrados</h2>
                <div id="usersList">
                    <div class="empty-state">
                        <i class="fas fa-users"></i>
                        <p>Carregando usuários...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const createUserForm = document.getElementById('createUserForm');
        const successAlert = document.getElementById('successAlert');
        const errorAlert = document.getElementById('errorAlert');
        const btnText = document.getElementById('btnText');
        const spinner = document.getElementById('spinner');
        const usersList = document.getElementById('usersList');

        // Load users on page load
        loadUsers();

        createUserForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            successAlert.style.display = 'none';
            errorAlert.style.display = 'none';

            const username = document.getElementById('username').value;
            const name = document.getElementById('name').value;
            const password = document.getElementById('password').value;

            btnText.style.display = 'none';
            spinner.style.display = 'inline';
            createUserForm.querySelector('button').disabled = true;

            try {
                const response = await fetch('/api/admin/users', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ username, name, password })
                });

                const result = await response.json();

                if (result.success) {
                    successAlert.textContent = '✅ ' + result.message;
                    successAlert.style.display = 'block';
                    createUserForm.reset();
                    loadUsers();
                } else {
                    errorAlert.textContent = '❌ ' + (result.error || 'Erro ao criar usuário');
                    errorAlert.style.display = 'block';
                }
            } catch (error) {
                errorAlert.textContent = '❌ Erro: ' + error.message;
                errorAlert.style.display = 'block';
            } finally {
                btnText.style.display = 'inline';
                spinner.style.display = 'none';
                createUserForm.querySelector('button').disabled = false;
            }
        });

        async function loadUsers() {
            try {
                const response = await fetch('/api/dashboard/overview-summary');
                if (!response.ok) {
                    // Users data is not available via API, display them from the form
                    usersList.innerHTML = '<div class="empty-state"><i class="fas fa-info-circle"></i><p>Usuários são gerenciados através do formulário acima</p></div>';
                    return;
                }
            } catch (error) {
                console.error('Error loading users:', error);
            }
        }
    </script>
</body>
</html>
