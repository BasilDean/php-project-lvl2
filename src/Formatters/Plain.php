<?php

namespace Differ\Formatters\Plain;

function getPlainFormat(array $data): string
{
    return implode("\n", getDiff($data));
}

function getDiff(array $data, string $path = ''): array
{
    $result = array_map(fn ($node) => getFormat($node, $path), $data);

    return array_filter($result, fn ($name) => !is_null($name));
}

function getFormat(array $node, string $path): string | null
{
    $newPath = $path == "" ? $node['key'] : "{$path}.{$node['key']}";
    switch ($node['status']) {
        case 'unchanged':
            return null;
        case 'added':
            return "Property '{$newPath}' was added with value: " . displayValue($node['newValue']);
        case 'removed':
            return "Property '{$newPath}' was removed";
        case 'updated':
            return "Property '{$newPath}' was updated. From "
            . displayValue($node['oldValue']) . " to " . displayValue($node['newValue']);
        case "parent":
            return implode("\n", getDiff($node['children'], $newPath));
        default:
            throw new \Exception("unknown status: " . $node['status'] . " for getFormat in Plain format");
    }
}

function displayValue(mixed $value): string | int | float
{
    if (is_bool($value)) {
        return ($value === true) ? "true" : "false";
    }

    if (is_numeric($value)) {
        return $value;
    }

    if (is_null($value)) {
        return "null";
    }

    return is_array($value) ? '[complex value]' : "'$value'";
}
