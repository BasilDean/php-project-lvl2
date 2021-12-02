<?php

namespace  Differ\Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    public function addDataProvider()
    {
        return [
            ['before.json', 'after.json', 'stylish', 'diffStylish'],
            ['before.yml', 'after.yml', 'stylish', 'diffStylish'],
            ['before.json', 'after.json', 'plain', 'diffPlain'],
            ['before.yml', 'after.yml', 'plain', 'diffPlain'],
            ['before.json', 'after.json', 'json', 'diffJson'],
            ['before.yml', 'after.yml', 'json', 'diffJson']
        ];
    }

    /**
     * @dataProvider addDataProvider
     */
    
    public function testGenDiff($nameBefore, $nameAfter, $format, $nameResult)
    {
        $pathToFile1 = $this->genPath($nameBefore);
        $pathToFile2 = $this->genPath($nameAfter);
        $pathToExpected = $this->genPath($nameResult);
        $actual = genDiff($pathToFile1, $pathToFile2, $format);
        $expected = file_get_contents($pathToExpected);
        $this->assertEquals($expected, $actual);
    }

    private function genPath($baseName)
    {
        $dir = '__fixtures__';
        return "./{$dir}/{$baseName}";
    }
}
