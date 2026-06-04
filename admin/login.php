<?php
/**
 * Login do painel admin — duas etapas quando há 2FA:
 *   1) usuário + senha
 *   2) código do app autenticador (guardado via sessão pendente, 5 min)
 */
declare(strict_types=1);

require_once __DIR__ . '/partials.php';

auth_boot();

if (!has_admin()) {
    header('Location: /admin/setup.php');
    exit;
}
if (is_logged_in()) {
    header('Location: /admin/');
    exit;
}

const PENDING_TTL = 300; // 5 minutos

$error   = '';
$need2fa = false;

// Cancela a etapa de 2FA e volta ao início
if (isset($_GET['cancel'])) {
    unset($_SESSION['pending_2fa_id'], $_SESSION['pending_2fa_time']);
    header('Location: /admin/login.php');
    exit;
}

// Sessão de 2FA pendente ainda válida?
$pendingId = $_SESSION['pending_2fa_id'] ?? null;
if ($pendingId && (time() - ($_SESSION['pending_2fa_time'] ?? 0) > PENDING_TTL)) {
    unset($_SESSION['pending_2fa_id'], $_SESSION['pending_2fa_time']);
    $pendingId = null;
}
$need2fa = (bool) $pendingId;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    if (isset($_POST['code']) && $pendingId) {
        // Etapa 2: valida o código
        $u = get_admin((int) $pendingId);
        if ($u && !empty($u['totp_secret']) && totp_verify($u['totp_secret'], (string) $_POST['code'])) {
            start_admin_session($u);
            header('Location: /admin/');
            exit;
        }
        $error = 'Código de verificação incorreto.';
        $need2fa = true;
    } else {
        // Etapa 1: usuário + senha
        $user = verify_credentials((string) ($_POST['username'] ?? ''), (string) ($_POST['password'] ?? ''));
        if (!$user) {
            $error = 'Usuário ou senha incorretos.';
        } elseif (!empty($user['totp_enabled'])) {
            $_SESSION['pending_2fa_id']   = (int) $user['id'];
            $_SESSION['pending_2fa_time'] = time();
            $need2fa = true;
        } else {
            start_admin_session($user);
            header('Location: /admin/');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Entrar · Admin Di Bútcher</title>
    <link rel="icon" href="/assets/img/favicon.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="/admin/assets/admin.css">
</head>
<body>
    <div class="auth-screen">
        <form class="auth-box" method="post" action="/admin/login.php">
            <img src="/assets/img/logo.png" alt="Di Bútcher">
            <h1>Painel Di Bútcher</h1>
            <p class="sub"><?= $need2fa ? 'Verificação em duas etapas' : 'Acesso restrito · gestão do cardápio' ?></p>

            <?php if ($error): ?>
                <div class="alert alert--error"><?= e($error) ?></div>
            <?php endif; ?>

            <?= csrf_field() ?>

            <?php if (!$need2fa): ?>
                <div class="field">
                    <label for="username">Usuário</label>
                    <input id="username" name="username" type="text" required autofocus autocomplete="username">
                </div>
                <div class="field">
                    <label for="password">Senha</label>
                    <input id="password" name="password" type="password" required autocomplete="current-password">
                </div>
                <button class="btn btn--primary btn--block" type="submit">Entrar</button>
            <?php else: ?>
                <p class="sub" style="margin-top:-.5rem;">Digite o código de 6 dígitos do seu app autenticador.</p>
                <div class="field">
                    <label for="code">Código</label>
                    <input id="code" name="code" type="text" inputmode="numeric" autocomplete="one-time-code"
                           pattern="[0-9]*" maxlength="6" required autofocus
                           style="letter-spacing:.4em; font-size:1.3rem; text-align:center;">
                </div>
                <button class="btn btn--primary btn--block" type="submit">Verificar</button>
                <p class="sub" style="margin-top:1rem;"><a href="/admin/login.php?cancel=1">← Voltar</a></p>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>
