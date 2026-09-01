<?php

declare(strict_types=1);

function atozee_logged_in(): bool
{
    return !empty($_SESSION['admin_logged_in']);
}

function atozee_require_admin(): void
{
    if (!atozee_logged_in()) {
        atozee_redirect('admin/');
    }
}

function atozee_attempt_login(string $username, string $password): bool
{
    $settings = atozee_settings();
    $expectedUser = (string) ($settings['username'] ?? 'admin');
    $hash = (string) ($settings['password_hash'] ?? '');

    if (!hash_equals($expectedUser, $username)) {
        return false;
    }

    if ($hash === '' || !password_verify($password, $hash)) {
        return false;
    }

    session_regenerate_id(true);
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_user'] = $expectedUser;

    return true;
}

function atozee_logout(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}

function atozee_change_password(string $current, string $next): string
{
    $settings = atozee_settings();
    $hash = (string) ($settings['password_hash'] ?? '');

    if (!password_verify($current, $hash)) {
        return 'Current password is incorrect.';
    }

    if (strlen($next) < 8) {
        return 'New password must be at least 8 characters.';
    }

    $settings['password_hash'] = password_hash($next, PASSWORD_DEFAULT);
    if (!atozee_save_settings($settings)) {
        return 'Could not save the new password. Check that the data folder is writable.';
    }

    return '';
}
