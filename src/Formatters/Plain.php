<?php

namespace  Differ\Formatters\Plain;

use function Differ\Tree\getType;
use function Differ\Tree\getName;
use function Differ\Tree\getOldValue;
use function Differ\Tree\getNewValue;
use function Differ\Tree\getChildren;
use function Differ\Preparation\boolToString;
use function Funct\Collection\flattenAll;

function iter($tree, $preName)
{
    $result = array_reduce($tree, function ($res, $node) use ($preName) {
        $type = getType($node);
        $name = $preName . getName($node);
        switch ($type) {
            case 'added':
                $newValue = prepareValue(getNewValue($node));
                $res[] = "Property '{$name}' was {$type} with value: {$newValue}";
                break;
            case 'removed':
                $res[] = "Property '{$name}' was {$type}";
                break;
            case 'notChanged':
                break;
            case 'updated':
                $oldValue = prepareValue(getOldValue($node));
                $newValue = prepareValue(getNewValue($node));
                $res[] = "Property '{$name}' was {$type}. From {$oldValue} to {$newValue}";
                break;
            case 'nested':
                $children = getChildren($node);
                $res[] = iter($children, $name . '.');
        };
        return $res;
    }, []);
    return flattenAll($result);
}

function plain($tree)
{
    return implode("\n", iter($tree, ''));
}

function prepareValue($value)
{
    $preparedValue = is_string($value) ? "'{$value}'" : boolToString($value);
    $preparedValue = is_object($preparedValue) ? '[complex value]' : $preparedValue;
    return $preparedValue;
}
