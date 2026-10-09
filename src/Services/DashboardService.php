<?php

namespace App\Services;

use App\Models\DreLinha;
use App\Models\DreValor;

class DashboardService
{
    /**
     * Get DRE data for a specific area and year
     * Used to populate area-specific dashboard tab
     *
     * @param int $areaId
     * @param int $ano
     * @return array Structured data with linhas and values
     */
    public function getAreaData(int $areaId, int $ano): array
    {
        $linhas = DreLinha::getAll();
        $valores = DreValor::getByAreaAndYear($areaId, $ano);
        $summary = DreValor::getSummaryByAreaAndYear($areaId, $ano);

        // Group values by linha_id for easier lookup
        $valoresByLinha = [];
        foreach ($valores as $valor) {
            $valoresByLinha[$valor->dre_linha_id][] = $valor;
        }

        // Build structured response
        $data = [
            'area_id' => $areaId,
            'ano' => $ano,
            'summary' => $summary,
            'linhas' => []
        ];

        foreach ($linhas as $linha) {
            $linhaData = [
                'id' => $linha->id,
                'ordem' => $linha->ordem,
                'nome' => $linha->nome,
                'meses' => []
            ];

            // Add monthly values for this line (as array, not object)
            if (isset($valoresByLinha[$linha->id])) {
                foreach ($valoresByLinha[$linha->id] as $valor) {
                    $linhaData['meses'][] = [
                        'mes' => $valor->mes,
                        'valor_planejado' => $valor->valor_planejado,
                        'valor_realizado' => $valor->valor_realizado,
                        'variancia' => $valor->variancia,
                        'percentual_realizacao' => $valor->percentual_realizacao,
                    ];
                }
            }

            $data['linhas'][] = $linhaData;
        }

        return $data;
    }

    /**
     * Get comparative data for a specific DRE line across all areas
     * Used to compare same metric across different departments
     *
     * @param int $linhaId
     * @param int $ano
     * @param int|null $mes Optional: filter by specific month
     * @return array Data with area comparison
     */
    public function getComparativeData(int $linhaId, int $ano, ?int $mes = null): array
    {
        $linha = DreLinha::getById($linhaId);
        if (!$linha) {
            return ['error' => 'Linha not found'];
        }

        $valores = DreValor::getByLinhaAndYear($linhaId, $ano, $mes);

        // Group by area
        $valuesByArea = [];
        foreach ($valores as $valor) {
            if (!isset($valuesByArea[$valor->area_id])) {
                $valuesByArea[$valor->area_id] = [];
            }
            $valuesByArea[$valor->area_id][] = $valor;
        }

        $data = [
            'linha_id' => $linhaId,
            'linha_nome' => $linha->nome,
            'ano' => $ano,
            'mes' => $mes,
            'areas' => []
        ];

        // Build comparison data
        for ($areaId = 1; $areaId <= 8; $areaId++) {
            $areaData = [
                'area_id' => $areaId,
                'meses' => []
            ];

            if (isset($valuesByArea[$areaId])) {
                foreach ($valuesByArea[$areaId] as $valor) {
                    $areaData['meses'][] = [
                        'mes' => $valor->mes,
                        'valor_planejado' => $valor->valor_planejado,
                        'valor_realizado' => $valor->valor_realizado,
                        'variancia' => $valor->variancia,
                        'percentual_realizacao' => $valor->percentual_realizacao,
                    ];
                }
            }

            $data['areas'][] = $areaData;
        }

        return $data;
    }

    /**
     * Get priority DRE lines (main financial indicators)
     * Returns: RECEITA, MARGENS, EBITDA, RESULTADO
     *
     * @return array Array of priority DreLinha objects
     */
    public function getPriorityLines(): array
    {
        return DreLinha::getPriority();
    }

    /**
     * Get summary data for dashboard overview
     * Shows key metrics across all areas for quick insights
     *
     * @param int $ano
     * @return array Summary metrics
     */
    public function getOverviewSummary(int $ano): array
    {
        $priorityLines = $this->getPriorityLines();
        $summary = [];

        foreach ($priorityLines as $linha) {
            $valores = DreValor::getByLinhaAndYear($linha->id, $ano);

            $totalPlanejado = 0;
            $totalRealizado = 0;

            foreach ($valores as $valor) {
                $totalPlanejado += $valor->valor_planejado;
                $totalRealizado += $valor->valor_realizado;
            }

            $summary[$linha->nome] = [
                'linha_id' => $linha->id,
                'total_planejado' => $totalPlanejado,
                'total_realizado' => $totalRealizado,
                'variancia' => $totalRealizado - $totalPlanejado,
                'percentual' => $totalPlanejado != 0 ? ($totalRealizado / $totalPlanejado * 100) : 0
            ];
        }

        return $summary;
    }

    /**
     * Export DRE data for all areas in a given year
     * Useful for reports and data downloads
     *
     * @param int $ano
     * @return array Complete dataset
     */
    public function exportYearData(int $ano): array
    {
        $linhas = DreLinha::getAll();
        $allValores = DreValor::getByAreasAndYear(range(1, 8), $ano);

        $export = [
            'ano' => $ano,
            'linhas' => array_map(fn($l) => $l->toArray(), $linhas),
            'valores' => array_map(fn($v) => $v->toArray(), $allValores)
        ];

        return $export;
    }
}
