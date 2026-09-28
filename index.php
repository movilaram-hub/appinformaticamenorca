<?php
require_once __DIR__ . '/config.php';
$accept = strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'es');
$locale = 'es';
foreach (['ca','en','fr','de','es'] as $candidate) {
    if (preg_match('/(^|[,;\s])' . preg_quote($candidate, '/') . '([-_][a-z]{2})?/i', $accept)) { $locale = $candidate; break; }
}
header('Location: ' . rtrim(BASE_URL, '/') . '/' . $locale . '/');
exit;
