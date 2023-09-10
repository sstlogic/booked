<?php
/**
 * Copyright 2022 Twinkle Toes Software, LLC
 */

function apiencode($val): mixed
{
    if (empty($val)) {
        return $val;
    }

    if (is_array($val)) {
        return array_map('htmlspecialchars', array_map('trim', $val));
    }

    if (is_string($val)) {
        return htmlspecialchars(trim($val));
    }

    return $val;
}

function apidecode($val): mixed
{
    if (empty($val)) {
        return $val;
    }

    if (is_array($val)) {
        return array_map('htmlspecialchars_decode', $val);
    }

    if (is_string($val)) {
        return htmlspecialchars_decode($val);
    }

    return $val;
}