<?php

if (!function_exists('current_page')) {
    function current_page(): string
    {
        $page = $_GET['page'] ?? 'home';
        if ($page === '' || $page === 'menu') {
            return 'home';
        }

        return $page;
    }
}

if (!function_exists('nav_active')) {
    function nav_active(string $target): string
    {
        return current_page() === $target ? 'is-active' : '';
    }
}

if (!function_exists('e')) {
    function e(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('format_number')) {
    function format_number($value, int $decimals = 0): string
    {
        if ($value === null || $value === '') {
            return number_format(0, $decimals);
        }

        return number_format((float) $value, $decimals);
    }
}

if (!function_exists('format_currency')) {
    function format_currency($value): string
    {
        return number_format((float) $value, 2);
    }
}

