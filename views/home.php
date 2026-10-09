<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Início - DRE CEO Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .navbar-custom {
            background: rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
            color: white !important;
        }
        .welcome-section {
            padding: 60px 20px;
            text-align: center;
            color: white;
        }
        .welcome-section h1 {
            font-size: 3rem;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .welcome-section p {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 40px;
        }
        .cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            padding: 40px 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .card-custom {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s, box-shadow 0.3s;
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
        }
        .card-custom:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            color: inherit;
            text-decoration: none;
        }
        .card-icon {
            font-size: 3rem;
            padding: 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
        }
        .card-content {
            padding: 30px;
            flex-grow: 1;
        }
        .card-content h2 {
            margin-bottom: 10px;
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
        }
        .card-content p {
            color: #666;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        .card-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: 600;
            text-align: center;
            display: inline-block;
            margin-top: auto;
        }
        .card-custom:hover .card-btn {
            background: linear-gradient(135deg, #5568d3 0%, #6a3f8f 100%);
        }
        .stats {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 30px;
            margin: 40px auto;
            max-width: 1200px;
            color: white;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
        }
        .stat-item {
            text-align: center;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container-fluid px-4">
            <span class="navbar-brand">📊 DRE CEO Dashboard</span>
            <div class="ms-auto">
                <span class="text-white me-3">Bem-vindo, <strong><?= htmlspecialchars($_SESSION['user_name'] ?? 'Usuário') ?></strong></span>
                <a href="/logout" class="btn btn-sm btn-light">Sair</a>
            </div>
        </div>
    </nav>

    <!-- Welcome Section -->
    <div class="welcome-section">
        <h1>Bem-vindo ao DRE CEO! 👋</h1>
        <p>Sistema integrado de gestão e análise de dados financeiros</p>
    </div>

    <!-- Statistics -->
    <div class="stats">
        <div class="stat-item">
            <div class="stat-number">4</div>
            <div class="stat-label">Áreas Ativas</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">12</div>
            <div class="stat-label">Meses de Dados</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">3</div>
            <div class="stat-label">Anos Disponíveis</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">132+</div>
            <div class="stat-label">Registros no Sistema</div>
        </div>
    </div>

    <!-- Main Cards -->
    <div class="cards-container">
        <!-- Dashboard Card -->
        <a href="/dashboard" class="card-custom">
            <div class="card-icon">📈</div>
            <div class="card-content">
                <h2>Dashboard</h2>
                <p>Visualize dados em gráficos interativos, tabelas detalhadas e análises comparativas entre áreas.</p>
                <div class="card-btn">Acessar →</div>
            </div>
        </a>

        <!-- Upload Card -->
        <a href="/upload" class="card-custom">
            <div class="card-icon">📤</div>
            <div class="card-content">
                <h2>Upload de Dados</h2>
                <p>Importe arquivos Excel com dados DRE. Novos uploads substituem dados antigos automaticamente.</p>
                <div class="card-btn">Acessar →</div>
            </div>
        </a>

        <!-- Analytics Card -->
        <a href="/dashboard?area_id=1" class="card-custom">
            <div class="card-icon">📊</div>
            <div class="card-content">
                <h2>Análise por Área</h2>
                <p>Acesse dados específicos de cada área: Compras BR, Siga, Eficaz e Outsourcing.</p>
                <div class="card-btn">Acessar →</div>
            </div>
        </a>
    </div>

    <!-- Footer -->
    <footer style="text-align: center; padding: 40px 20px; color: white;">
        <p style="margin: 0; opacity: 0.8;">DRE CEO Dashboard © 2026 - Sistema de Gestão Financeira</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
