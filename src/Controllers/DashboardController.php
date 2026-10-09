<?php

namespace App\Controllers;

use App\Services\DashboardService;

class DashboardController
{
    private $service;

    public function __construct()
    {
        $this->service = new DashboardService();
    }

    /**
     * Display dashboard main page
     */
    public function index(): void
    {
        // Get current year from query or use current year
        $ano = isset($_GET['ano']) ? (int)$_GET['ano'] : date('Y');

        // Get area ID from query, session, or use first area
        $areaId = isset($_GET['area_id']) ? (int)$_GET['area_id'] : ($_SESSION['user_area_id'] ?? 1);

        // Validate area ID (1-4)
        if ($areaId < 1 || $areaId > 4) {
            $areaId = 1;
        }

        // Get data for rendering
        $areaData = $this->service->getAreaData($areaId, $ano);
        $overviewSummary = $this->service->getOverviewSummary($ano);
        $priorityLines = $this->service->getPriorityLines();

        include __DIR__ . '/../../views/dashboard.php';
    }

    /**
     * API endpoint: Get area data in JSON format
     */
    public function apiAreaData(): void
    {
        header('Content-Type: application/json');

        try {
            // Validate parameters
            if (empty($_GET['area_id']) || !is_numeric($_GET['area_id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing or invalid area_id parameter']);
                return;
            }

            if (empty($_GET['ano']) || !is_numeric($_GET['ano'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing or invalid ano parameter']);
                return;
            }

            $areaId = (int)$_GET['area_id'];
            $ano = (int)$_GET['ano'];

            // Validate area exists (1-4)
            if ($areaId < 1 || $areaId > 4) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid area_id. Must be between 1 and 4']);
                return;
            }

            $data = $this->service->getAreaData($areaId, $ano);

            http_response_code(200);
            echo json_encode($data, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * API endpoint: Get comparative data (same line across areas)
     */
    public function apiComparativeData(): void
    {
        header('Content-Type: application/json');

        try {
            // Validate parameters
            if (empty($_GET['linha_id']) || !is_numeric($_GET['linha_id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing or invalid linha_id parameter']);
                return;
            }

            if (empty($_GET['ano']) || !is_numeric($_GET['ano'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing or invalid ano parameter']);
                return;
            }

            $linhaId = (int)$_GET['linha_id'];
            $ano = (int)$_GET['ano'];
            $mes = isset($_GET['mes']) && is_numeric($_GET['mes']) ? (int)$_GET['mes'] : null;

            // Validate linha_id (1-11)
            if ($linhaId < 1 || $linhaId > 11) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid linha_id. Must be between 1 and 11']);
                return;
            }

            // Validate mes if provided (1-12)
            if ($mes !== null && ($mes < 1 || $mes > 12)) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid mes. Must be between 1 and 12']);
                return;
            }

            $data = $this->service->getComparativeData($linhaId, $ano, $mes);

            http_response_code(200);
            echo json_encode($data, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * API endpoint: Get overview summary
     */
    public function apiOverviewSummary(): void
    {
        header('Content-Type: application/json');

        try {
            if (empty($_GET['ano']) || !is_numeric($_GET['ano'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing or invalid ano parameter']);
                return;
            }

            $ano = (int)$_GET['ano'];
            $data = $this->service->getOverviewSummary($ano);

            http_response_code(200);
            echo json_encode($data, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * API endpoint: Export year data
     */
    public function apiExportYear(): void
    {
        header('Content-Type: application/json');

        try {
            if (empty($_GET['ano']) || !is_numeric($_GET['ano'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing or invalid ano parameter']);
                return;
            }

            $ano = (int)$_GET['ano'];
            $data = $this->service->exportYearData($ano);

            http_response_code(200);
            echo json_encode($data, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
