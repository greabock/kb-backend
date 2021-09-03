<?php

function data_get_struct($data, array $struct): array
{
    $results = [];

    $plainKeys = array_filter($struct, static fn($value, $key) => is_int($key) && is_string($value), ARRAY_FILTER_USE_BOTH);
    $objects = array_filter($struct, static fn($value, $key) => is_string($key) && is_array($value), ARRAY_FILTER_USE_BOTH);

    foreach ($plainKeys as $key) {
        if (is_array($data) && array_key_exists($key, $data)) {
            $results[$key] = $data[$key];
        }
    }

    foreach ($objects as $key => $objectStruct) {
        if (array_key_exists($key, $data)) {
            $results[$key] = data_get_struct($data[$key], $objectStruct);
        }
    }

    if (empty($plainKeys) && empty($objects) && isset($struct[0])) {
        foreach ($data as $element) {
            $results[] = data_get_struct($element, $struct[0]);
        }
    }

    return $results;
}
