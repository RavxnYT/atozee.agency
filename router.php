<?php
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$file = __DIR__ . $uri;

if (preg_match('#^/(data|includes)(/|$)#', $uri)) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Forbidden';
    return true;
}

if ($uri !== '/' && is_file($file)) {
    return false;
}

if (is_dir($file) && is_file($file . '/index.php')) {
    require $file . '/index.php';
    return true;
}

require __DIR__ . '/index.php';
