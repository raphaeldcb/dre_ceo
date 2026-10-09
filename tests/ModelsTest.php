<?php

require_once __DIR__ . '/TestCase.php';
require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\DreLinha;
use App\Models\DreValor;

class ModelsTest extends TestCase
{
    public function testDreLinhaFromArray()
    {
        $data = [
            'id' => 1,
            'ordem' => 1,
            'nome' => 'RECEITA',
            'descricao' => 'Receita Total',
            'ativa' => true,
            'criada_em' => '2026-10-08 12:00:00',
            'atualizada_em' => '2026-10-08 12:00:00'
        ];

        $linha = DreLinha::fromArray($data);

        $this->assertInstanceOf(DreLinha::class, $linha);
        $this->assertEquals(1, $linha->id);
        $this->assertEquals('RECEITA', $linha->nome);
    }

    public function testDreLinhaToArray()
    {
        $data = [
            'id' => 1,
            'ordem' => 1,
            'nome' => 'RECEITA',
            'descricao' => 'Receita Total',
            'ativa' => true,
            'criada_em' => '2026-10-08 12:00:00',
            'atualizada_em' => '2026-10-08 12:00:00'
        ];

        $linha = DreLinha::fromArray($data);
        $array = $linha->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('nome', $array);
        $this->assertArrayHasKey('ativa', $array);
        $this->assertEquals('RECEITA', $array['nome']);
    }

    public function testDreValorFromArray()
    {
        $data = [
            'id' => 1,
            'area_id' => 1,
            'dre_linha_id' => 1,
            'mes' => 1,
            'ano' => 2026,
            'valor_planejado' => 1000000.00,
            'valor_realizado' => 1050000.00,
            'variancia' => 50000.00,
            'percentual_realizacao' => 105.00,
            'analises' => 'Acima do esperado',
            'status' => 'FINALIZADO',
            'criado_em' => '2026-10-08 12:00:00',
            'atualizado_em' => '2026-10-08 12:00:00'
        ];

        $valor = DreValor::fromArray($data);

        $this->assertInstanceOf(DreValor::class, $valor);
        $this->assertEquals(1, $valor->id);
        $this->assertEquals(1050000.00, $valor->valor_realizado);
    }

    public function testDreValorToArray()
    {
        $data = [
            'id' => 1,
            'area_id' => 1,
            'dre_linha_id' => 1,
            'mes' => 1,
            'ano' => 2026,
            'valor_planejado' => 1000000.00,
            'valor_realizado' => 1050000.00,
            'variancia' => 50000.00,
            'percentual_realizacao' => 105.00,
            'analises' => null,
            'status' => 'FINALIZADO',
            'criado_em' => '2026-10-08 12:00:00',
            'atualizado_em' => '2026-10-08 12:00:00'
        ];

        $valor = DreValor::fromArray($data);
        $array = $valor->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('valor_planejado', $array);
        $this->assertArrayHasKey('valor_realizado', $array);
        $this->assertArrayHasKey('variancia', $array);
        $this->assertArrayHasKey('percentual_realizacao', $array);
    }

    public function testDreValorVarianciaCalculation()
    {
        $data = [
            'id' => 1,
            'area_id' => 1,
            'dre_linha_id' => 1,
            'mes' => 1,
            'ano' => 2026,
            'valor_planejado' => 1000.00,
            'valor_realizado' => 1200.00,
            'variancia' => 200.00,
            'percentual_realizacao' => 120.00,
            'analises' => null,
            'status' => 'FINALIZADO',
            'criado_em' => '2026-10-08 12:00:00',
            'atualizado_em' => '2026-10-08 12:00:00'
        ];

        $valor = DreValor::fromArray($data);

        $expectedVariancia = 1200.00 - 1000.00;
        $this->assertEquals($expectedVariancia, $valor->variancia);
    }

    public function testDreValorPercentualRealizacao()
    {
        $data = [
            'id' => 1,
            'area_id' => 1,
            'dre_linha_id' => 1,
            'mes' => 1,
            'ano' => 2026,
            'valor_planejado' => 1000.00,
            'valor_realizado' => 500.00,
            'variancia' => -500.00,
            'percentual_realizacao' => 50.00,
            'analises' => null,
            'status' => 'FINALIZADO',
            'criado_em' => '2026-10-08 12:00:00',
            'atualizado_em' => '2026-10-08 12:00:00'
        ];

        $valor = DreValor::fromArray($data);

        $expectedPercentual = (500.00 / 1000.00) * 100;
        $this->assertEquals($expectedPercentual, $valor->percentual_realizacao);
    }

    public function testDreValorStatusEnum()
    {
        $statuses = ['PLANEJADO', 'PARCIAL', 'FINALIZADO'];

        foreach ($statuses as $status) {
            $data = [
                'id' => 1,
                'area_id' => 1,
                'dre_linha_id' => 1,
                'mes' => 1,
                'ano' => 2026,
                'valor_planejado' => 1000.00,
                'valor_realizado' => 1000.00,
                'variancia' => 0.00,
                'percentual_realizacao' => 100.00,
                'analises' => null,
                'status' => $status,
                'criado_em' => '2026-10-08 12:00:00',
                'atualizado_em' => '2026-10-08 12:00:00'
            ];

            $valor = DreValor::fromArray($data);
            $this->assertEquals($status, $valor->status);
        }
    }
}
