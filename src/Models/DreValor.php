<?php

namespace App\Models;

use App\Utils\Database;

class DreValor
{
    public int $id;
    public int $area_id;
    public int $dre_linha_id;
    public int $mes;
    public int $ano;
    public float $valor_planejado;
    public float $valor_realizado;
    public float $variancia;
    public float $percentual_realizacao;
    public ?string $analises;
    public string $status;
    public string $criado_em;
    public string $atualizado_em;

    /**
     * Get all DRE values for a specific area and year
     *
     * @param int $areaId
     * @param int $ano
     * @return array Array of DreValor objects
     */
    public static function getByAreaAndYear(int $areaId, int $ano): array
    {
        $db = Database::getInstance();
        $stmt = $db->getConnection()->prepare(
            'SELECT id, area_id, dre_linha_id, mes, ano, valor_planejado, valor_realizado,
                    variancia, percentual_realizacao, analises, status, criado_em, atualizado_em
             FROM dre_valores
             WHERE area_id = ? AND ano = ?
             ORDER BY dre_linha_id ASC, mes ASC'
        );
        $stmt->execute([$areaId, $ano]);

        $results = [];
        foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $results[] = self::fromArray($row);
        }
        return $results;
    }

    /**
     * Get DRE values for a specific line across all areas
     * Used for comparative analysis
     *
     * @param int $linhaId
     * @param int $ano
     * @param int|null $mes Optional: filter by specific month
     * @return array Array of DreValor objects
     */
    public static function getByLinhaAndYear(int $linhaId, int $ano, ?int $mes = null): array
    {
        $db = Database::getInstance();

        $sql = 'SELECT id, area_id, dre_linha_id, mes, ano, valor_planejado, valor_realizado,
                       variancia, percentual_realizacao, analises, status, criado_em, atualizado_em
                FROM dre_valores
                WHERE dre_linha_id = ? AND ano = ?';

        $params = [$linhaId, $ano];

        if ($mes !== null) {
            $sql .= ' AND mes = ?';
            $params[] = $mes;
        }

        $sql .= ' ORDER BY mes ASC, area_id ASC';

        $stmt = $db->getConnection()->prepare($sql);
        $stmt->execute($params);

        $results = [];
        foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $results[] = self::fromArray($row);
        }
        return $results;
    }

    /**
     * Get values for multiple areas (for comparative charts)
     *
     * @param array $areaIds
     * @param int $ano
     * @return array Array of DreValor objects grouped by area
     */
    public static function getByAreasAndYear(array $areaIds, int $ano): array
    {
        if (empty($areaIds)) {
            return [];
        }

        $db = Database::getInstance();

        $placeholders = implode(',', array_fill(0, count($areaIds), '?'));
        $params = array_merge($areaIds, [$ano]);

        $stmt = $db->getConnection()->prepare(
            "SELECT id, area_id, dre_linha_id, mes, ano, valor_planejado, valor_realizado,
                    variancia, percentual_realizacao, analises, status, criado_em, atualizado_em
             FROM dre_valores
             WHERE area_id IN ({$placeholders}) AND ano = ?
             ORDER BY area_id ASC, dre_linha_id ASC, mes ASC"
        );
        $stmt->execute($params);

        $results = [];
        foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $results[] = self::fromArray($row);
        }
        return $results;
    }

    /**
     * Get summary statistics for a specific area and year
     *
     * @param int $areaId
     * @param int $ano
     * @return array Summary with min, max, average values
     */
    public static function getSummaryByAreaAndYear(int $areaId, int $ano): array
    {
        $db = Database::getInstance();
        $stmt = $db->getConnection()->prepare(
            'SELECT
                COUNT(*) as total_records,
                SUM(valor_planejado) as total_planejado,
                SUM(valor_realizado) as total_realizado,
                AVG(percentual_realizacao) as media_realizacao,
                MIN(valor_realizado) as min_realizado,
                MAX(valor_realizado) as max_realizado
             FROM dre_valores
             WHERE area_id = ? AND ano = ?'
        );
        $stmt->execute([$areaId, $ano]);

        return $stmt->fetch(\PDO::FETCH_ASSOC) ?? [];
    }

    /**
     * Create instance from array
     *
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $valor = new self();
        $valor->id = (int)$data['id'];
        $valor->area_id = (int)$data['area_id'];
        $valor->dre_linha_id = (int)$data['dre_linha_id'];
        $valor->mes = (int)$data['mes'];
        $valor->ano = (int)$data['ano'];
        $valor->valor_planejado = (float)$data['valor_planejado'];
        $valor->valor_realizado = (float)$data['valor_realizado'];
        $valor->variancia = (float)$data['variancia'];
        $valor->percentual_realizacao = (float)$data['percentual_realizacao'];
        $valor->analises = $data['analises'] ?? null;
        $valor->status = $data['status'];
        $valor->criado_em = $data['criado_em'];
        $valor->atualizado_em = $data['atualizado_em'];
        return $valor;
    }

    /**
     * Convert to array
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'area_id' => $this->area_id,
            'dre_linha_id' => $this->dre_linha_id,
            'mes' => $this->mes,
            'ano' => $this->ano,
            'valor_planejado' => $this->valor_planejado,
            'valor_realizado' => $this->valor_realizado,
            'variancia' => $this->variancia,
            'percentual_realizacao' => $this->percentual_realizacao,
            'analises' => $this->analises,
            'status' => $this->status,
            'criado_em' => $this->criado_em,
            'atualizado_em' => $this->atualizado_em,
        ];
    }
}
