<?php
/**
 * Autenticação do painel admin + proteção CSRF
 * Di Bútcher Premium Burger
 *
 * Sessão segura · senha com password_hash() · tokens CSRF em todo POST.
 */

declare(strict_types=1);

require_once __DIR__ . '/db.php';

/**
 * Inicia a sessão com parâmetros de cookie seguros.
 */
function auth_boot(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'secure'   => $https,
        'samesite' => 'Lax',
    ]);

    session_name('DIBUTCHER_ADMIN');
    session_start();
}

/* ─────────────────────────────────────────────────────────────
 * USUÁRIOS ADMIN
 * ───────────────────────────────────────────────────────────── */

/**
 * Existe ao menos um admin cadastrado?
 */
function has_admin(): bool
{
    return (int) db()->query('SELECT COUNT(*) FROM admin_users')->fetchColumn() > 0;
}

/**
 * Cria o primeiro (ou um novo) admin.
 */
function create_admin(string $username, string $password): bool
{
    $username = trim($username);
    if ($username === '' || strlen($password) < 8) {
        return false;
    }
    $stmt = db()->prepare(
        'INSERT INTO admin_users (username, password_hash) VALUES (:u, :h)'
    );
    return $stmt->execute([
        ':u' => $username,
        ':h' => password_hash($password, PASSWORD_DEFAULT),
    ]);
}

/**
 * Verifica credenciais e autentica a sessão.
 */
function login(string $username, string $password): bool
{
    $stmt = db()->prepare('SELECT * FROM admin_users WHERE username = :u');
    $stmt->execute([':u' => trim($username)]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        return false;
    }

    // Rehash se o algoritmo padrão mudou
    if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
        $upd = db()->prepare('UPDATE admin_users SET password_hash = :h WHERE id = :id');
        $upd->execute([':h' => password_hash($password, PASSWORD_DEFAULT), ':id' => $user['id']]);
    }

    session_regenerate_id(true);
    $_SESSION['admin_id']   = (int) $user['id'];
    $_SESSION['admin_user'] = $user['username'];
    return true;
}

/**
 * Encerra a sessão.
 */
function logout(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

/**
 * Está autenticado?
 */
function is_logged_in(): bool
{
    return !empty($_SESSION['admin_id']);
}

/**
 * Exige login — redireciona para a tela de login caso contrário.
 */
function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: /admin/login.php');
        exit;
    }
}

/* ─────────────────────────────────────────────────────────────
 * CSRF
 * ───────────────────────────────────────────────────────────── */

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

/**
 * Campo hidden pronto para inserir nos formulários.
 */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="'
        . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Valida o token enviado via POST. Aborta a requisição se inválido.
 */
function csrf_check(): void
{
    $sent = $_POST['csrf'] ?? '';
    if (!is_string($sent) || !hash_equals(csrf_token(), $sent)) {
        http_response_code(419);
        exit('Token de sessão inválido. Recarregue a página e tente novamente.');
    }
}
