<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload DRE - CEO Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
            background: #0f3460;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .card-header {
            background: linear-gradient(135deg, #0f3460 0%, #1a5f7a 100%);
            color: #e0f0ff;
        }
        .card-body {
            color: #e0e0e0;
        }
        .form-label {
            color: #e0f0ff;
        }
        .form-control {
            background: #1a4a6a;
            border: 1px solid rgba(255,255,255,0.2);
            color: #f0f0f0;
        }
        .form-control::placeholder {
            color: #80a0b0;
        }
        .form-control:focus {
            background: #1a4a6a;
            border-color: #1a5f7a;
            color: #f0f0f0;
            box-shadow: 0 0 0 0.2rem rgba(26, 95, 122, 0.25);
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
            box-shadow: 0 0 0 0.2rem rgba(26, 95, 122, 0.25);
        }
        .upload-zone {
            border: 2px dashed #1a5f7a;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: rgba(26, 95, 122, 0.1);
            color: #e0f0ff;
        }
        .upload-zone:hover {
            border-color: #1a5f7a;
            background-color: rgba(26, 95, 122, 0.2);
        }
        .upload-zone.dragover {
            border-color: #1a5f7a;
            background-color: rgba(26, 95, 122, 0.3);
        }
        .btn-primary {
            background: linear-gradient(135deg, #0f3460 0%, #1a5f7a 100%);
            border: 1px solid rgba(255,255,255,0.2);
            color: #f0f0f0;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #1a5f7a 0%, #244a68 100%);
            border-color: rgba(255,255,255,0.3);
        }
        .text-muted {
            color: #b0d0e0 !important;
        }
        .text-danger {
            color: #f87171 !important;
        }
        hr {
            border-color: rgba(255,255,255,0.1);
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center py-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div style="margin-bottom: 20px;">
                    <a href="/home" style="color: #e0f0ff; text-decoration: none; font-size: 1.1rem;">← Voltar para Início</a>
                </div>
                <div class="card">
                    <div class="card-header text-white">
                        <h4 class="mb-0">📤 Upload DRE</h4>
                    </div>
                    <div class="card-body p-5">
                        <form id="uploadForm" enctype="multipart/form-data" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

                            <!-- Area Selection -->
                            <div class="mb-3">
                                <label for="area_id" class="form-label">Área <span class="text-danger">*</span></label>
                                <select id="area_id" name="area_id" class="form-select" required>
                                    <option value="">Selecione uma área...</option>
                                    <option value="1">Compras BR</option>
                                    <option value="2">Siga</option>
                                    <option value="3">Eficaz</option>
                                    <option value="4">Outsourcing</option>
                                </select>
                            </div>

                            <!-- Year -->
                            <div class="mb-3">
                                <label for="ano" class="form-label">Ano <span class="text-danger">*</span></label>
                                <input type="number" id="ano" name="ano" class="form-control" value="<?= date('Y') ?>" required>
                            </div>

                            <!-- File Upload Zone -->
                            <div class="mb-4">
                                <label for="file" class="form-label">Arquivo Excel <span class="text-danger">*</span></label>
                                <div id="uploadZone" class="upload-zone">
                                    <div id="uploadPrompt">
                                        <div style="font-size: 2rem; margin-bottom: 10px;">📁</div>
                                        <p class="mb-0"><strong>Clique ou arraste o arquivo aqui</strong></p>
                                        <small class="text-muted">Apenas arquivos .xlsx (máx 5MB)</small>
                                    </div>
                                    <div id="uploadSuccess" style="display: none;">
                                        <p class="mb-0">✅ Arquivo selecionado: <strong id="fileName"></strong></p>
                                    </div>
                                </div>
                                <input type="file" id="file" name="file" accept=".xlsx" style="display: none;" required>
                            </div>

                            <!-- Status Messages -->
                            <div id="alertBox"></div>

                            <!-- Warning Message -->
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                ⚠️ <strong>Atenção:</strong> Ao fazer upload, os dados existentes deste ano para esta área serão <strong>removidos</strong> e substituídos pelos dados do arquivo.
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <span id="btnText">📤 Enviar Arquivo</span>
                                <span id="spinner" style="display: none;">
                                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Enviando...
                                </span>
                            </button>
                        </form>

                        <hr class="my-4">
                        <small class="text-muted d-block text-center">
                            O arquivo deve conter a aba "DRE Sintético" com 11 linhas e 12 meses de dados.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const uploadZone = document.getElementById('uploadZone');
        const fileInput = document.getElementById('file');
        const uploadForm = document.getElementById('uploadForm');
        const alertBox = document.getElementById('alertBox');

        // Click to select file
        uploadZone.addEventListener('click', () => fileInput.click());

        // Drag and drop
        uploadZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadZone.classList.add('dragover');
        });

        uploadZone.addEventListener('dragleave', () => {
            uploadZone.classList.remove('dragover');
        });

        uploadZone.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadZone.classList.remove('dragover');
            if (e.dataTransfer.files.length > 0) {
                fileInput.files = e.dataTransfer.files;
                handleFileSelect();
            }
        });

        // File selection change
        fileInput.addEventListener('change', handleFileSelect);

        function handleFileSelect() {
            if (fileInput.files.length > 0) {
                const file = fileInput.files[0];
                document.getElementById('fileName').textContent = file.name;
                document.getElementById('uploadPrompt').style.display = 'none';
                document.getElementById('uploadSuccess').style.display = 'block';
            } else {
                document.getElementById('uploadPrompt').style.display = 'block';
                document.getElementById('uploadSuccess').style.display = 'none';
            }
        }

        // Form submission
        uploadForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            if (!fileInput.files.length) {
                showAlert('Selecione um arquivo', 'danger');
                return;
            }

            const formData = new FormData(uploadForm);
            const btnText = document.getElementById('btnText');
            const spinner = document.getElementById('spinner');

            // Show loading state
            btnText.style.display = 'none';
            spinner.style.display = 'inline';
            uploadForm.querySelector('button[type="submit"]').disabled = true;

            try {
                const response = await fetch('/api/upload', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    showAlert(`✅ ${result.message}`, 'success');
                    setTimeout(() => uploadForm.reset(), 2000);
                } else {
                    showAlert(`❌ ${result.error}`, 'danger');
                }
            } catch (error) {
                showAlert(`Erro: ${error.message}`, 'danger');
            } finally {
                btnText.style.display = 'inline';
                spinner.style.display = 'none';
                uploadForm.querySelector('button[type="submit"]').disabled = false;
            }
        });

        function showAlert(message, type) {
            alertBox.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>`;
        }
    </script>
</body>
</html>
