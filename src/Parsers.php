<?php

namespace  Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parse($rawData, $type)
{
    $mapping = [
        'yml' =>
            fn($rawData) => Yaml::parse($rawData, Yaml::PARSE_OBJECT_FOR_MAP),
        'json' =>
            fn($rawData) => json_decode($rawData),
    ];
    return $mapping[$type]($rawData);
}
