<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - DRE CEO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        body {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            color: #e0e0e0;
            min-height: 100vh;
        }
        .navbar {
            background: linear-gradient(135deg, #0f3460 0%, #1a5f7a 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .navbar-brand { color: #f0f0f0 !important; }
        .navbar-text { color: #d0d0d0 !important; }
        .chart-container { position: relative; height: 400px; margin-bottom: 30px; }
        .card {
            border: 1px solid rgba(255,255,255,0.1);
            background: #0f3460;
            color: #e0e0e0;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            margin-bottom: 20px;
        }
        .card-header {
            background: linear-gradient(135deg, #0f3460 0%, #1a5f7a 100%);
            color: #e0f0ff;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .table-responsive { border-radius: 8px; overflow: hidden; }
        .metric-card {
            text-align: center;
            padding: 20px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            margin-bottom: 15px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .metric-value {
            font-size: 1.8rem;
            font-weight: bold;
            color: #1f2937;
        }
        .metric-label {
            font-size: 0.85rem;
            color: #6b7280;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .positive { color: #10b981 !important; }
        .negative { color: #ef4444 !important; }
        .tab-content { padding: 20px 0; }
        .chart-wrapper {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .chart-wrapper h6 {
            color: #1f2937;
            font-weight: 600;
            margin-bottom: 15px;
        }
        .form-select {
            background: #1a4a6a;
            border: 1px solid rgba(255,255,255,0.2);
            color: #f0f0f0;
        }
        .form-select:focus {
            background: #1a4a6a;
            border-color: #1a5f7a;
            color: #f0f0f0;
            box-shadow: 0 0 0 0.25rem rgba(26, 95, 122, 0.25);
        }
        .table { color: #e0e0e0; }
        .table thead { background: #1a4a6a; border-bottom: 2px solid rgba(255,255,255,0.1); }
        .table tbody tr { border-bottom: 1px solid rgba(255,255,255,0.05); }
        .nav-tabs .nav-link {
            color: #b0d0e0;
            border-color: transparent;
        }
        .nav-tabs .nav-link.active {
            background: #1a4a6a;
            color: #e0f0ff;
            border-bottom: 3px solid #1a5f7a;
        }
        .btn-back {
            color: #e0f0ff;
            text-decoration: none;
            margin-right: 15px;
            font-size: 1.2rem;
        }
        .btn-back:hover { color: #f0f0f0; }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="btn-back" href="/home" title="Voltar">← Voltar</a>
            <a class="navbar-brand" href="/">📊 DRE CEO Dashboard</a>
            <div class="navbar-text text-white d-flex gap-3">
                <div>
                    Área: <select id="areaSelector" class="form-select form-select-sm d-inline-block w-auto ms-2">
                        <option value="1" <?= ($areaId === 1) ? 'selected' : '' ?>>Compras BR</option>
                        <option value="2" <?= ($areaId === 2) ? 'selected' : '' ?>>Siga</option>
                        <option value="3" <?= ($areaId === 3) ? 'selected' : '' ?>>Eficaz</option>
                        <option value="4" <?= ($areaId === 4) ? 'selected' : '' ?>>Outsourcing</option>
                    </select>
                </div>
                <div>
                    Ano: <select id="anoSelector" class="form-select form-select-sm d-inline-block w-auto ms-2">
                        <?php for ($y = 2024; $y <= 2026; $y++): ?>
                            <option value="<?= $y ?>" <?= ($ano === $y) ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
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
                            📊 <?php
                                $areaNames = [1 => 'Compras BR', 2 => 'Siga', 3 => 'Eficaz', 4 => 'Outsourcing'];
                                echo $areaNames[$areaId] ?? "Área {$areaId}";
                            ?>
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
                                <div class="chart-wrapper">
                                    <h6 class="mb-3">Evolução Mensal - Receita (Planejado vs Realizado)</h6>
                                    <div class="chart-container">
                                        <canvas id="lineChart"></canvas>
                                    </div>
                                </div>
                            </div>

                            <!-- Bar Chart -->
                            <div class="col-12 col-lg-6">
                                <div class="chart-wrapper">
                                    <h6 class="mb-3">Variação por Mês</h6>
                                    <div class="chart-container">
                                        <canvas id="barChart"></canvas>
                                    </div>
                                </div>
                            </div>

                            <!-- Margem de Contribuição Chart -->
                            <div class="col-12 col-lg-6">
                                <div class="chart-wrapper">
                                    <h6 class="mb-3">Margem de Contribuição - Variação</h6>
                                    <div class="chart-container">
                                        <canvas id="margemChart"></canvas>
                                    </div>
                                </div>
                            </div>

                            <!-- EBITDA Chart -->
                            <div class="col-12 col-lg-6">
                                <div class="chart-wrapper">
                                    <h6 class="mb-3">EBITDA - Variação</h6>
                                    <div class="chart-container">
                                        <canvas id="ebitdaChart"></canvas>
                                    </div>
                                </div>
                            </div>

                            <!-- Monthly Breakdown Table -->
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">Dados Detalhados - Por Mês</h6>
                                    <select id="monthSelector" class="form-select form-select-sm w-auto">
                                        <option value="1">Janeiro</option>
                                        <option value="2">Fevereiro</option>
                                        <option value="3">Março</option>
                                        <option value="4">Abril</option>
                                        <option value="5">Maio</option>
                                        <option value="6">Junho</option>
                                        <option value="7">Julho</option>
                                        <option value="8">Agosto</option>
                                        <option value="9">Setembro</option>
                                        <option value="10">Outubro</option>
                                        <option value="11">Novembro</option>
                                        <option value="12">Dezembro</option>
                                    </select>
                                </div>
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
                                        <tbody id="monthlyDataTable">
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
                                <div class="chart-wrapper">
                                    <h6 class="mb-3">RECEITA - Comparativo entre Áreas</h6>
                                    <div class="chart-container">
                                        <canvas id="comparativeChart1"></canvas>
                                    </div>
                                </div>
                            </div>

                            <!-- EBITDA Comparison -->
                            <div class="col-12 col-lg-6">
                                <div class="chart-wrapper">
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const baseUrl = window.location.origin;
        const areaId = <?= $areaId ?>;
        const ano = <?= $ano ?>;

        // Area names mapping
        const areaNames = {
            1: 'Compras BR',
            2: 'Siga',
            3: 'Eficaz',
            4: 'Outsourcing'
        };

        function getAreaName(id) {
            return areaNames[id] || `Área ${id}`;
        }

        // Area data
        let areaData = {};
        let comparativeData = {};

        // Initialize
        async function init() {
            try {
                console.log('Initializing dashboard for area', areaId, 'year', ano);

                // Load area data
                const areaResponse = await fetch(`/api/dashboard/area-data?area_id=${areaId}&ano=${ano}`);
                areaData = await areaResponse.json();
                console.log('Loaded areaData:', areaData);

                // Render area charts
                renderAreaCharts();
                renderDataTable();
                renderMonthlyDataTable();

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
            try {
                console.log('renderAreaCharts called, areaData.linhas:', areaData.linhas);

                // Get RECEITA line data (linha_id = 1)
                const receita = areaData.linhas.find(l => l.id === 1);
                console.log('Found receita:', receita);

                if (!receita || !receita.meses || receita.meses.length === 0) {
                    console.warn('No RECEITA data available');
                    return;
                }

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
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.15)',
                            tension: 0.4,
                            fill: true,
                            pointRadius: 5,
                            pointBackgroundColor: '#10b981',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointHoverRadius: 7,
                            borderWidth: 3
                        },
                        {
                            label: 'Realizado',
                            data: realizado,
                            borderColor: '#ef4444',
                            backgroundColor: 'rgba(239, 68, 68, 0.15)',
                            tension: 0.4,
                            fill: true,
                            pointRadius: 5,
                            pointBackgroundColor: '#ef4444',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointHoverRadius: 7,
                            borderWidth: 3
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
                            backgroundColor: variacao.map(v => v >= 0 ? '#10b981' : '#ef4444'),
                            borderRadius: 4,
                            borderWidth: 1,
                            borderColor: variacao.map(v => v >= 0 ? '#059669' : '#dc2626')
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

            // Margem de Contribuição Chart
            const margem = areaData.linhas.find(l => l.id === 6);
            if (margem && margem.meses && margem.meses.length > 0) {
                const margemVariacao = [];
                for (let mes = 1; mes <= 12; mes++) {
                    const mes_data = margem.meses.find(m => m.mes === mes);
                    margemVariacao.push(mes_data ? (mes_data.variancia / 1000) : 0);
                }
                const margemCtx = document.getElementById('margemChart').getContext('2d');
                new Chart(margemCtx, {
                    type: 'bar',
                    data: {
                        labels: months,
                        datasets: [{
                            label: 'Variação (R$ mil)',
                            data: margemVariacao,
                            backgroundColor: margemVariacao.map(v => v >= 0 ? '#10b981' : '#ef4444'),
                            borderRadius: 4,
                            borderWidth: 1,
                            borderColor: margemVariacao.map(v => v >= 0 ? '#059669' : '#dc2626')
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: true } },
                        scales: { y: { beginAtZero: true } }
                    }
                });
            }

            // EBITDA Chart
            const ebitda = areaData.linhas.find(l => l.id === 8);
            if (ebitda && ebitda.meses && ebitda.meses.length > 0) {
                const ebitdaVariacao = [];
                for (let mes = 1; mes <= 12; mes++) {
                    const mes_data = ebitda.meses.find(m => m.mes === mes);
                    ebitdaVariacao.push(mes_data ? (mes_data.variancia / 1000) : 0);
                }
                const ebitdaCtx = document.getElementById('ebitdaChart').getContext('2d');
                new Chart(ebitdaCtx, {
                    type: 'bar',
                    data: {
                        labels: months,
                        datasets: [{
                            label: 'Variação (R$ mil)',
                            data: ebitdaVariacao,
                            backgroundColor: ebitdaVariacao.map(v => v >= 0 ? '#10b981' : '#ef4444'),
                            borderRadius: 4,
                            borderWidth: 1,
                            borderColor: ebitdaVariacao.map(v => v >= 0 ? '#059669' : '#dc2626')
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: true } },
                        scales: { y: { beginAtZero: true } }
                    }
                });
            }
            } catch (err) {
                console.error('Error rendering area charts:', err);
            }
        }

        function renderDataTable() {
            try {
                const tbody = document.getElementById('dataTable');
                if (!tbody) {
                    console.warn('dataTable element not found');
                    return;
                }
                tbody.innerHTML = '';

                if (!areaData.linhas || areaData.linhas.length === 0) {
                    console.warn('No linhas data available');
                    return;
                }

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
                        <td class="text-end">${formatBR(totalPlanejado)}</td>
                        <td class="text-end">${formatBR(totalRealizado)}</td>
                        <td class="text-end ${variacao >= 0 ? 'positive' : 'negative'}">${formatBR(variacao)}</td>
                        <td class="text-end ${percentual >= 100 ? 'positive' : 'negative'}">${formatNumberBR(percentual)}%</td>
                    `;
                    tbody.appendChild(row);
                });
            } catch (err) {
                console.error('Error rendering data table:', err);
            }
        }

        function renderMonthlyDataTable(mesSelected = 1) {
            try {
                const tbody = document.getElementById('monthlyDataTable');
                if (!tbody) {
                    console.warn('monthlyDataTable element not found');
                    return;
                }
                tbody.innerHTML = '';

                if (!areaData.linhas || areaData.linhas.length === 0) {
                    console.warn('No linhas data available');
                    return;
                }

                const months = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];

                areaData.linhas.forEach(linha => {
                    const mes_data = linha.meses.find(m => m.mes === mesSelected);

                    if (!mes_data) return;

                    const planejado = mes_data.valor_planejado;
                    const realizado = mes_data.valor_realizado;
                    const variacao = realizado - planejado;
                    const percentual = planejado !== 0 ? (realizado / planejado * 100) : 0;

                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${htmlEscape(linha.nome)}</td>
                        <td class="text-end">${formatBR(planejado)}</td>
                        <td class="text-end">${formatBR(realizado)}</td>
                        <td class="text-end ${variacao >= 0 ? 'positive' : 'negative'}">${formatBR(variacao)}</td>
                        <td class="text-end ${percentual >= 100 ? 'positive' : 'negative'}">${formatNumberBR(percentual)}%</td>
                    `;
                    tbody.appendChild(row);
                });
            } catch (err) {
                console.error('Error rendering monthly data table:', err);
            }
        }

        function renderComparativeCharts() {
            if (!comparativeData.receita || !comparativeData.ebitda) return;

            try {
                // RECEITA Chart
                renderComparativeChart('comparativeChart1', comparativeData.receita);

                // EBITDA Chart
                renderComparativeChart('comparativeChart2', comparativeData.ebitda);
            } catch (err) {
                console.error('Error rendering comparative charts:', err);
            }
        }

        function renderComparativeChart(canvasId, data) {
            try {
                const areas = ['Compras BR', 'Siga', 'Eficaz', 'Outsourcing'];
                const monthIndex = 12; // Last month data

                if (!data.areas || data.areas.length === 0) {
                    console.warn('No areas data for chart:', canvasId);
                    return;
                }

                const valores = data.areas.map(area => {
                    if (!area.meses || area.meses.length === 0) return 0;
                    const mes_data = area.meses.find(m => m.mes === monthIndex);
                    return mes_data ? mes_data.valor_realizado / 1000 : 0;
                });

                const canvasEl = document.getElementById(canvasId);
                if (!canvasEl) {
                    console.warn('Canvas element not found:', canvasId);
                    return;
                }

                const ctx = canvasEl.getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: areas,
                        datasets: [{
                            label: `${data.linha_nome} (Realizado - R$ mil)`,
                            data: valores,
                            backgroundColor: '#3b82f6',
                            borderColor: '#1e40af',
                            borderWidth: 2,
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'top' } },
                        scales: { y: { beginAtZero: true } }
                    }
                });
            } catch (err) {
                console.error('Error in renderComparativeChart:', err);
            }
        }

        function htmlEscape(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Format number to BR standard (1.234.567,89)
        function formatBR(value) {
            if (value === null || value === undefined) return 'R$ 0,00';
            const num = parseFloat(value);
            return new Intl.NumberFormat('pt-BR', {
                style: 'currency',
                currency: 'BRL',
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(num);
        }

        // Format number without currency (1.234.567,89)
        function formatNumberBR(value) {
            if (value === null || value === undefined) return '0,00';
            const num = parseFloat(value);
            return new Intl.NumberFormat('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(num);
        }

        // Area selector
        document.getElementById('areaSelector').addEventListener('change', (e) => {
            const newAreaId = e.target.value;
            window.location.search = `area_id=${newAreaId}&ano=${ano}`;
        });

        // Year selector
        document.getElementById('anoSelector').addEventListener('change', (e) => {
            const newAno = e.target.value;
            window.location.search = `area_id=${areaId}&ano=${newAno}`;
        });

        // Month selector
        const monthSelector = document.getElementById('monthSelector');
        if (monthSelector) {
            monthSelector.addEventListener('change', (e) => {
                const selectedMonth = parseInt(e.target.value);
                renderMonthlyDataTable(selectedMonth);
            });
        }

        // Load on page load
        document.addEventListener('DOMContentLoaded', init);
    </script>
</body>
</html>
