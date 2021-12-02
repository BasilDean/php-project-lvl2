<?php

namespace  Differ\Formatters\Stylish;

use function Differ\Tree\getType;
use function Differ\Tree\getName;
use function Differ\Tree\getOldValue;
use function Differ\Tree\getNewValue;
use function Differ\Tree\getChildren;
use function Differ\Preparation\boolToString;
use function Funct\Collection\flattenAll;

function iter($tree, $space)
{
    $addedSpace = '    ';
    $result = array_reduce($tree, function ($res, $node) use ($space, $addedSpace) {
        $type = getType($node);
        $name = getName($node);
        switch ($type) {
            case 'added':
                $newValue = getNewValue($node);
                $res[] = $space . "  + {$name}: " . prepareValue($newValue, $space . $addedSpace);
                break;
            case 'removed':
                $oldValue = getOldValue($node);
                $res[] = $space . "  - {$name}: " . prepareValue($oldValue, $space . $addedSpace);
                break;
            case 'notChanged':
                $newValue = getNewValue($node);
                $res[] = $space . "    {$name}: " . prepareValue($newValue, $space . $addedSpace);
                break;
            case 'updated':
                $oldValue = getOldValue($node);
                $newValue = getNewValue($node);
                $res[] = $space . "  - {$name}: " . prepareValue($oldValue, $space . $addedSpace);
                $res[] = $space . "  + {$name}: " . prepareValue($newValue, $space . $addedSpace);
                break;
            case 'nested':
                $children = getChildren($node);
                $res[] = $space . "    {$name}: {";
                $res[] = iter($children, $space . $addedSpace);
                $res[] = $space . '    }';
        };
        return $res;
    }, []);
    return flattenAll($result);
}

function stylish($tree)
{
    $res = implode("\n", iter($tree, ''));
    return "{\n" . $res . "\n}\n";
}

function prepareValue($value, $space = '')
{
    if (!is_object($value)) {
        return boolToString($value);
    }
    $arr = (array) ($value);
    $res = implode('', array_map(function ($key, $value) use ($space) {
        return "\n" . $space . "    {$key}: " . prepareValue($value, $space . '    ');
    }, array_keys($arr), $arr));
    return '{' . $res . "\n" . $space . '}';
}
