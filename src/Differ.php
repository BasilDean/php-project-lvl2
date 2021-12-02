<?php

namespace  Differ\Differ;

use function Differ\Parsers\parse;
use function Differ\Builder\diffAsTree;
use function Differ\Formatters\Stylish\stylish;
use function Differ\Formatters\Plain\plain;
use function Differ\Formatters\Json\json;

function genDiff($pathToFile1, $pathToFile2, $format = 'stylish')
{
    $data1 = getData($pathToFile1);
    $data2 = getData($pathToFile2);
        
    $mapping = [
        'stylish' =>
            fn($tree) => stylish($tree),
        'plain' =>
            fn($tree) => plain($tree),
        'json' =>
            fn($tree) => json($tree),
    ];
    return $mapping[$format](diffAsTree($data1, $data2));
}

function getData($pathToFile)
{
    $type = pathinfo($pathToFile, PATHINFO_EXTENSION);
    $rawData = file_get_contents($pathToFile);
    return parse($rawData, $type);
}
