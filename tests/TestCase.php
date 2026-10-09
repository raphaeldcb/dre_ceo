<?php

class TestCase
{
    protected $testCount = 0;
    protected $passCount = 0;
    protected $failCount = 0;
    protected $errors = [];

    public function run()
    {
        $methods = get_class_methods($this);
        $testMethods = array_filter($methods, fn($m) => strpos($m, 'test') === 0);

        foreach ($testMethods as $method) {
            $this->testCount++;
            try {
                $this->$method();
                $this->passCount++;
                echo "✅ " . get_class($this) . "::{$method}\n";
            } catch (Exception $e) {
                $this->failCount++;
                $this->errors[] = get_class($this) . "::{$method}: " . $e->getMessage();
                echo "❌ " . get_class($this) . "::{$method}\n";
                echo "   Error: " . $e->getMessage() . "\n";
            }
        }
    }

    protected function assertEquals($expected, $actual, $message = '')
    {
        if ($expected !== $actual) {
            throw new Exception("Assertion failed: expected " . var_export($expected, true) .
                              " but got " . var_export($actual, true) . " {$message}");
        }
    }

    protected function assertTrue($condition, $message = '')
    {
        if (!$condition) {
            throw new Exception("Assertion failed: expected true {$message}");
        }
    }

    protected function assertFalse($condition, $message = '')
    {
        if ($condition) {
            throw new Exception("Assertion failed: expected false {$message}");
        }
    }

    protected function assertCount($expected, $actual, $message = '')
    {
        if (count($actual) !== $expected) {
            throw new Exception("Assertion failed: expected count {$expected} but got " . count($actual) . " {$message}");
        }
    }

    protected function assertNotNull($value, $message = '')
    {
        if ($value === null) {
            throw new Exception("Assertion failed: expected not null {$message}");
        }
    }

    protected function assertIsArray($value, $message = '')
    {
        if (!is_array($value)) {
            throw new Exception("Assertion failed: expected array {$message}");
        }
    }

    protected function assertArrayHasKey($key, $array, $message = '')
    {
        if (!isset($array[$key])) {
            throw new Exception("Assertion failed: array does not have key '{$key}' {$message}");
        }
    }

    protected function assertFileExists($filePath, $message = '')
    {
        if (!file_exists($filePath)) {
            throw new Exception("Assertion failed: file '{$filePath}' does not exist {$message}");
        }
    }

    protected function assertInstanceOf($class, $object, $message = '')
    {
        if (!($object instanceof $class)) {
            throw new Exception("Assertion failed: expected instance of {$class} {$message}");
        }
    }

    public function getResults()
    {
        return [
            'total' => $this->testCount,
            'passed' => $this->passCount,
            'failed' => $this->failCount,
            'errors' => $this->errors
        ];
    }
}
