<?php

declare(strict_types=1);

use App\Core\Container;

function app_base_path(): string
{
    $config = Container::get('config');
    $base = $config['app']['base_path'] ?? '';
    $base = rtrim((string)$base, '/');
    if ($base === '/' || $base === '.') {
        return '';
    }
    return $base;
}

function url(string $path = ''): string
{
    $base = app_base_path();
    $path = '/' . ltrim($path, '/');
    return ($base ?: '') . ($path === '/' ? '/' : $path);
}

function base_url(string $path = ''): string
{
    $config = Container::get('config');
    $root = rtrim($config['app']['url'], '/');
    return $root . url($path);
}

function view(string $view, array $data = []): void
{
    extract($data, EXTR_SKIP);
    $viewFile = BASE_PATH . '/app/views/' . $view . '.php';
    if (!file_exists($viewFile)) {
        http_response_code(404);
        echo "Vista no encontrada: {$view}";
        return;
    }

    ob_start();
    require BASE_PATH . '/app/views/layouts/main.php';
    $html = ob_get_clean();

    $base = app_base_path();
    if ($base !== '') {
        $html = preg_replace_callback('/\b(href|src|action)=("|\')\/(?!\/)/', static function (array $m) use ($base): string {
            return $m[1] . '=' . $m[2] . $base . '/';
        }, $html) ?? $html;
    }

    echo $html;
}

function redirect(string $path): void
{
    if (!preg_match('#^https?://#i', $path)) {
        $path = url($path);
    }
    header('Location: ' . $path);
    exit;
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . csrf_token() . '">';
}

function verify_csrf(): bool
{
    $token = $_POST['_csrf'] ?? '';
    return hash_equals($_SESSION['_csrf'] ?? '', $token);
}

function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['_flash'][$key] = $message;
        return null;
    }
    $msg = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);
    return $msg;
}

function auth_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function can(array|string $roles): bool
{
    $user = auth_user();
    if (!$user) {
        return false;
    }
    $roles = (array)$roles;
    return in_array($user['role_name'] ?? '', $roles, true);
}


function has_permission(string $permission): bool
{
    $user = auth_user();
    if (!$user) {
        return false;
    }
    if (($user['role_name'] ?? '') === 'root') {
        return true;
    }
    return in_array($permission, $user['permissions'] ?? [], true);
}

function old(string $key, string $default = ''): string
{
    return e($_POST[$key] ?? $default);
}

function q(string $key, string $default = ''): string
{
    return e($_GET[$key] ?? $default);
}

function request_int(string $key, int $default = 1): int
{
    $value = filter_input(INPUT_GET, $key, FILTER_VALIDATE_INT);
    return $value && $value > 0 ? $value : $default;
}
