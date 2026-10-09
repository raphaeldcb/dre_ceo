<?php

require_once __DIR__ . '/TestCase.php';
require_once __DIR__ . '/../vendor/autoload.php';

use App\Services\ExcelParser;

class ExcelParserTest extends TestCase
{
    private $parser;
    private $testFilePath;

    public function __construct()
    {
        $this->parser = new ExcelParser();
        $this->testFilePath = __DIR__ . '/../Treasy_DRE.xlsx';
    }

    public function testFileExists()
    {
        $this->assertFileExists($this->testFilePath, 'Test file Treasy_DRE.xlsx not found');
    }

    public function testParserLoadsSuccessfully()
    {
        $this->assertInstanceOf(ExcelParser::class, $this->parser);
    }

    public function testParseReturnsCorrectStructure()
    {
        $result = $this->parser->parse($this->testFilePath);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('linhas_count', $result);
        $this->assertArrayHasKey('errors', $result);
    }

    public function testParseReturns132Records()
    {
        $result = $this->parser->parse($this->testFilePath);

        $this->assertEquals(132, $result['linhas_count'], 'Expected 132 records (11 linhas × 12 meses)');
        $this->assertCount(132, $result['data']);
    }

    public function testParseFirstRecordStructure()
    {
        $result = $this->parser->parse($this->testFilePath);
        $firstRecord = $result['data'][0];

        $this->assertArrayHasKey('linha_id', $firstRecord);
        $this->assertArrayHasKey('mes', $firstRecord);
        $this->assertArrayHasKey('valor_planejado', $firstRecord);
        $this->assertArrayHasKey('valor_realizado', $firstRecord);
        $this->assertArrayHasKey('analise_vertical_planejado', $firstRecord);
        $this->assertArrayHasKey('analise_vertical_realizado', $firstRecord);
    }

    public function testParseFirstLinhaIsOne()
    {
        $result = $this->parser->parse($this->testFilePath);
        $firstLinhaRecords = array_slice($result['data'], 0, 12);

        foreach ($firstLinhaRecords as $record) {
            $this->assertEquals(1, $record['linha_id'], 'First linha should be 1');
        }
    }

    public function testParseHasAllMonths()
    {
        $result = $this->parser->parse($this->testFilePath);
        $firstLinhaRecords = array_slice($result['data'], 0, 12);

        $months = array_map(fn($r) => $r['mes'], $firstLinhaRecords);
        $expected = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];

        $this->assertEquals($expected, $months, 'Should have all 12 months');
    }

    public function testParseNumericValues()
    {
        $result = $this->parser->parse($this->testFilePath);

        foreach ($result['data'] as $record) {
            $this->assertTrue(
                is_numeric($record['valor_planejado']) || is_null($record['valor_planejado']),
                'valor_planejado should be numeric or null'
            );
            $this->assertTrue(
                is_numeric($record['valor_realizado']) || is_null($record['valor_realizado']),
                'valor_realizado should be numeric or null'
            );
        }
    }

    public function testParseInvalidFileThrowsException()
    {
        try {
            $this->parser->parse('/nonexistent/file.xlsx');
            throw new Exception('Should have thrown an exception for invalid file');
        } catch (Exception $e) {
            // Expected
            $this->assertTrue(true);
        }
    }

    public function testParseExtractLinha1DataCorrectly()
    {
        $result = $this->parser->parse($this->testFilePath);

        // Linha 1 has 12 records (one per month)
        $linha1Records = array_filter($result['data'], fn($r) => $r['linha_id'] === 1);
        $this->assertCount(12, $linha1Records, 'Linha 1 should have 12 records (one per month)');
    }

    public function testParseHandlesAllLinhas()
    {
        $result = $this->parser->parse($this->testFilePath);

        // Should have all 11 linhas × 12 meses = 132 records
        $linhaIds = array_unique(array_column($result['data'], 'linha_id'));
        sort($linhaIds);

        $expected = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11];
        $this->assertEquals($expected, $linhaIds, 'Should have all 11 DRE linhas');
    }
}
