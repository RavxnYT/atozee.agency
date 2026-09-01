<?php

declare(strict_types=1);

if (defined('ATOZEE_BOOTSTRAPPED')) {
    return;
}

define('ATOZEE_BOOTSTRAPPED', true);
define('ATOZEE_ROOT', dirname(__DIR__));
define('ATOZEE_DATA', ATOZEE_ROOT . '/data');
define('ATOZEE_UPLOADS', ATOZEE_ROOT . '/uploads/agencies');
define('ATOZEE_SEED_CONTENT', ATOZEE_DATA . '/content.seed.json');
define('ATOZEE_SEED_SETTINGS', ATOZEE_DATA . '/settings.seed.json');
define('ATOZEE_CONTENT_FILE', ATOZEE_DATA . '/content.json');
define('ATOZEE_SETTINGS_FILE', ATOZEE_DATA . '/settings.json');

date_default_timezone_set('Asia/Beirut');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name('atozee_admin');
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
        'use_strict_mode' => true,
    ]);
}

require_once ATOZEE_ROOT . '/includes/store.php';
require_once ATOZEE_ROOT . '/includes/auth.php';

atozee_ensure_storage();

function atozee_site_url(string $path = ''): string
{
    $scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
    $scriptFile = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_FILENAME'] ?? ''));
    $dir = rtrim(dirname($scriptName), '/');

    if (basename(dirname($scriptFile)) === 'admin' || str_ends_with($dir, '/admin')) {
        $dir = rtrim(dirname($dir), '/');
    }

    if ($dir === '/' || $dir === '\\' || $dir === '.') {
        $dir = '';
    }

    return $dir . '/' . ltrim($path, '/');
}

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function atozee_slug(string $name): string
{
    $slug = strtolower(trim($name));
    $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug) ?? '';
    $slug = trim($slug, '-');
    return $slug !== '' ? $slug : 'category';
}

function atozee_id(string $prefix = 'id'): string
{
    return $prefix . '_' . bin2hex(random_bytes(6));
}

function atozee_flash(?string $message = null, string $type = 'success'): ?array
{
    if ($message !== null) {
        $_SESSION['flash'] = ['message' => $message, 'type' => $type];
        return null;
    }

    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return is_array($flash) ? $flash : null;
}

function atozee_csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return (string) $_SESSION['csrf_token'];
}

function atozee_csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(atozee_csrf_token()) . '">';
}

function atozee_verify_csrf(): void
{
    $token = (string) ($_POST['csrf_token'] ?? '');
    if ($token === '' || !hash_equals(atozee_csrf_token(), $token)) {
        http_response_code(400);
        exit('Invalid request. Please refresh and try again.');
    }
}

function atozee_redirect(string $path): never
{
    header('Location: ' . atozee_site_url($path));
    exit;
}

function atozee_image_src(string $image): string
{
    if ($image === '') {
        return atozee_site_url('assets/logo.png');
    }

    if (preg_match('#^https?://#i', $image)) {
        return $image;
    }

    return atozee_site_url(ltrim($image, '/'));
}

function atozee_whatsapp_url(string $number, string $text = ''): string
{
    $digits = preg_replace('/\D+/', '', $number) ?? '';
    $url = 'https://wa.me/' . $digits;
    if ($text !== '') {
        $url .= '?text=' . rawurlencode($text);
    }
    return $url;
}

function atozee_mailto(string $email, string $subject = '', string $body = ''): string
{
    $url = 'mailto:' . $email;
    $params = [];
    if ($subject !== '') {
        $params[] = 'subject=' . rawurlencode($subject);
    }
    if ($body !== '') {
        $params[] = 'body=' . rawurlencode($body);
    }
    if ($params) {
        $url .= '?' . implode('&', $params);
    }
    return $url;
}
