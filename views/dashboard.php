<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - DRE CEO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        body { background: #f8f9fa; }
        .navbar { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .chart-container { position: relative; height: 400px; margin-bottom: 30px; }
        .card { border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .card-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .table-responsive { border-radius: 8px; overflow: hidden; }
        .metric-card { text-align: center; padding: 20px; background: white; border-radius: 8px; margin-bottom: 15px; }
        .metric-value { font-size: 1.8rem; font-weight: bold; color: #667eea; }
        .metric-label { font-size: 0.85rem; color: #666; text-transform: uppercase; }
        .positive { color: #28a745; }
        .negative { color: #dc3545; }
        .tab-content { padding: 20px 0; }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">📊 DRE CEO Dashboard</a>
            <div class="navbar-text text-white">
                Ano: <select id="anoSelector" class="form-select form-select-sm d-inline-block w-auto ms-2">
                    <?php for ($y = 2024; $y <= 2026; $y++): ?>
                        <option value="<?= $y ?>" <?= ($ano === $y) ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <!-- Overview Summary -->
        <div class="row mb-4">
            <div class="col-12">
                <h4 class="mb-3">📈 Resumo Executivo</h4>
            </div>
            <?php if (!empty($overviewSummary)): ?>
                <?php foreach ($overviewSummary as $name => $metrics): ?>
                    <div class="col-md-6 col-lg-3">
                        <div class="metric-card">
                            <div class="metric-label"><?= htmlspecialchars($name) ?></div>
                            <div class="metric-value <?= ($metrics['percentual'] >= 100) ? 'positive' : 'negative' ?>">
                                <?= number_format($metrics['percentual'], 1, ',', '.') ?>%
                            </div>
                            <small class="text-muted">
                                R$ <?= number_format($metrics['total_realizado'], 0, ',', '.') ?>
                            </small>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Tabs -->
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="area-tab" data-bs-toggle="tab" data-bs-target="#areaContent" type="button" role="tab">
                            📊 Área <?= $areaId ?>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="comparative-tab" data-bs-toggle="tab" data-bs-target="#comparativeContent" type="button" role="tab">
                            📈 Comparativo
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body">
                <!-- Area Tab -->
                <div class="tab-content" id="tabContent">
                    <div class="tab-pane fade show active" id="areaContent" role="tabpanel">
                        <div class="row">
                            <!-- Line Chart -->
                            <div class="col-12 col-lg-6">
                                <h6 class="mb-3">Evolução Mensal - Receita (Planejado vs Realizado)</h6>
                                <div class="chart-container">
                                    <canvas id="lineChart"></canvas>
                                </div>
                            </div>

                            <!-- Bar Chart -->
                            <div class="col-12 col-lg-6">
                                <h6 class="mb-3">Variação por Mês</h6>
                                <div class="chart-container">
                                    <canvas id="barChart"></canvas>
                                </div>
                            </div>

                            <!-- Data Table -->
                            <div class="col-12">
                                <h6 class="mb-3">Dados Detalhados</h6>
                                <div class="table-responsive">
                                    <table class="table table-hover table-sm">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Linha</th>
                                                <th class="text-end">Planejado</th>
                                                <th class="text-end">Realizado</th>
                                                <th class="text-end">Variação</th>
                                                <th class="text-end">%</th>
                                            </tr>
                                        </thead>
                                        <tbody id="dataTable">
                                            <!-- Populated by JS -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Comparative Tab -->
                    <div class="tab-pane fade" id="comparativeContent" role="tabpanel">
                        <div class="row">
                            <!-- Receita Comparison -->
                            <div class="col-12 col-lg-6">
                                <h6 class="mb-3">RECEITA - Comparativo entre Áreas</h6>
                                <div class="chart-container">
                                    <canvas id="comparativeChart1"></canvas>
                                </div>
                            </div>

                            <!-- EBITDA Comparison -->
                            <div class="col-12 col-lg-6">
                                <h6 class="mb-3">EBITDA - Comparativo entre Áreas</h6>
                                <div class="chart-container">
                                    <canvas id="comparativeChart2"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const baseUrl = window.location.origin;
        const areaId = <?= $areaId ?>;
        const ano = <?= $ano ?>;

        // Area data
        let areaData = {};
        let comparativeData = {};

        // Initialize
        async function init() {
            try {
                // Load area data
                const areaResponse = await fetch(`/api/dashboard/area-data?area_id=${areaId}&ano=${ano}`);
                areaData = await areaResponse.json();

                // Render area charts
                renderAreaCharts();
                renderDataTable();

                // Load comparative data (RECEITA = linha_id 1)
                const compResponse1 = await fetch(`/api/dashboard/comparative-data?linha_id=1&ano=${ano}`);
                comparativeData.receita = await compResponse1.json();

                const compResponse2 = await fetch(`/api/dashboard/comparative-data?linha_id=4&ano=${ano}`);
                comparativeData.ebitda = await compResponse2.json();

                // Render comparative charts
                renderComparativeCharts();

            } catch (error) {
                console.error('Error loading dashboard data:', error);
            }
        }

        function renderAreaCharts() {
            // Get RECEITA line data (linha_id = 1)
            const receita = areaData.linhas.find(l => l.id === 1);
            if (!receita) return;

            const months = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
            const planejado = [];
            const realizado = [];
            const variacao = [];

            for (let mes = 1; mes <= 12; mes++) {
                const mes_data = receita.meses.find(m => m.mes === mes);
                planejado.push(mes_data ? (mes_data.valor_planejado / 1000) : 0);
                realizado.push(mes_data ? (mes_data.valor_realizado / 1000) : 0);
                variacao.push(mes_data ? (mes_data.variancia / 1000) : 0);
            }

            // Line Chart
            const lineCtx = document.getElementById('lineChart').getContext('2d');
            new Chart(lineCtx, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [
                        {
                            label: 'Planejado',
                            data: planejado,
                            borderColor: '#667eea',
                            backgroundColor: 'rgba(102, 126, 234, 0.1)',
                            tension: 0.4,
                            fill: true,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        },
                        {
                            label: 'Realizado',
                            data: realizado,
                            borderColor: '#764ba2',
                            backgroundColor: 'rgba(118, 75, 162, 0.1)',
                            tension: 0.4,
                            fill: true,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'top' } },
                    scales: { y: { beginAtZero: true } }
                }
            });

            // Bar Chart
            const barCtx = document.getElementById('barChart').getContext('2d');
            new Chart(barCtx, {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [
                        {
                            label: 'Variação (R$ mil)',
                            data: variacao,
                            backgroundColor: variacao.map(v => v >= 0 ? '#28a745' : '#dc3545'),
                            borderRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: true } },
                    scales: { y: { beginAtZero: true } }
                }
            });
        }

        function renderDataTable() {
            const tbody = document.getElementById('dataTable');
            tbody.innerHTML = '';

            areaData.linhas.forEach(linha => {
                let totalPlanejado = 0;
                let totalRealizado = 0;

                for (let mes = 1; mes <= 12; mes++) {
                    const mes_data = linha.meses.find(m => m.mes === mes);
                    if (mes_data) {
                        totalPlanejado += mes_data.valor_planejado;
                        totalRealizado += mes_data.valor_realizado;
                    }
                }

                const variacao = totalRealizado - totalPlanejado;
                const percentual = totalPlanejado !== 0 ? (totalRealizado / totalPlanejado * 100) : 0;

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${htmlEscape(linha.nome)}</td>
                    <td class="text-end">R$ ${(totalPlanejado / 1000).toFixed(1)}k</td>
                    <td class="text-end">R$ ${(totalRealizado / 1000).toFixed(1)}k</td>
                    <td class="text-end ${variacao >= 0 ? 'positive' : 'negative'}">R$ ${(variacao / 1000).toFixed(1)}k</td>
                    <td class="text-end ${percentual >= 100 ? 'positive' : 'negative'}">${percentual.toFixed(1)}%</td>
                `;
                tbody.appendChild(row);
            });
        }

        function renderComparativeCharts() {
            if (!comparativeData.receita || !comparativeData.ebitda) return;

            // RECEITA Chart
            renderComparativeChart('comparativeChart1', comparativeData.receita);

            // EBITDA Chart
            renderComparativeChart('comparativeChart2', comparativeData.ebitda);
        }

        function renderComparativeChart(canvasId, data) {
            const areas = ['Adm', 'Vendas', 'Marketing', 'RH', 'Ops', 'Financeiro', 'Tech', 'Qualidade'];
            const monthIndex = 12; // Last month data

            const valores = data.areas.map(area => {
                const mes_data = area.meses.find(m => m.mes === monthIndex);
                return mes_data ? mes_data.valor_realizado / 1000 : 0;
            });

            const ctx = document.getElementById(canvasId).getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: areas,
                    datasets: [{
                        label: `${data.linha_nome} (Realizado - R$ mil)`,
                        data: valores,
                        backgroundColor: '#667eea',
                        borderColor: '#764ba2',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'top' } },
                    scales: { y: { beginAtZero: true } }
                }
            });
        }

        function htmlEscape(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Year selector
        document.getElementById('anoSelector').addEventListener('change', (e) => {
            const newAno = e.target.value;
            window.location.search = `ano=${newAno}`;
        });

        // Load on page load
        document.addEventListener('DOMContentLoaded', init);
    </script>
</body>
</html>
