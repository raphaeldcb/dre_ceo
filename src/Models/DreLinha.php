<?php

namespace App\Models;

use App\Utils\Database;

class DreLinha
{
    public int $id;
    public int $ordem;
    public string $nome;
    public ?string $descricao;
    public bool $ativa;
    public string $criada_em;
    public string $atualizada_em;

    /**
     * Get all DRE lines ordered by ordem
     *
     * @return array Array of DreLinha objects
     */
    public static function getAll(): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'SELECT id, ordem, nome, descricao, ativa, criada_em, atualizada_em
             FROM dre_linhas
             ORDER BY ordem ASC'
        );
        $stmt->execute();

        $results = [];
        foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $results[] = self::fromArray($row);
        }
        return $results;
    }

    /**
     * Get DRE line by ID
     *
     * @param int $id
     * @return DreLinha|null
     */
    public static function getById(int $id): ?self
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'SELECT id, ordem, nome, descricao, ativa, criada_em, atualizada_em
             FROM dre_linhas
             WHERE id = ?'
        );
        $stmt->execute([$id]);

        if ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            return self::fromArray($row);
        }
        return null;
    }

    /**
     * Get priority DRE lines (main financial indicators)
     * IDs: 1=RECEITA, 3=MARGENS, 4=EBITDA, 6=RESULTADO
     *
     * @return array Array of DreLinha objects
     */
    public static function getPriority(): array
    {
        $priorityIds = [1, 3, 4, 6];
        $db = Database::getInstance();

        $placeholders = implode(',', array_fill(0, count($priorityIds), '?'));
        $stmt = $db->prepare(
            "SELECT id, ordem, nome, descricao, ativa, criada_em, atualizada_em
             FROM dre_linhas
             WHERE id IN ({$placeholders})
             ORDER BY ordem ASC"
        );
        $stmt->execute($priorityIds);

        $results = [];
        foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $results[] = self::fromArray($row);
        }
        return $results;
    }

    /**
     * Create instance from array
     *
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $linha = new self();
        $linha->id = (int)$data['id'];
        $linha->ordem = (int)$data['ordem'];
        $linha->nome = $data['nome'];
        $linha->descricao = $data['descricao'] ?? null;
        $linha->ativa = (bool)$data['ativa'];
        $linha->criada_em = $data['criada_em'];
        $linha->atualizada_em = $data['atualizada_em'];
        return $linha;
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
            'ordem' => $this->ordem,
            'nome' => $this->nome,
            'descricao' => $this->descricao,
            'ativa' => $this->ativa,
            'criada_em' => $this->criada_em,
            'atualizada_em' => $this->atualizada_em,
        ];
    }
}
