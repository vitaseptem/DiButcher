<?php
/**
 * Tela de login do painel admin.
 */
declare(strict_types=1);

require_once __DIR__ . '/partials.php';

auth_boot();

// Sem admin cadastrado → vai para o setup inicial.
if (!has_admin()) {
    header('Location: /admin/setup.php');
    exit;
}

// Já logado → dashboard.
if (is_logged_in()) {
    header('Location: /admin/');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $user = (string) ($_POST['username'] ?? '');
    $pass = (string) ($_POST['password'] ?? '');

    if (login($user, $pass)) {
        header('Location: /admin/');
        exit;
    }
    $error = 'Usuário ou senha incorretos.';
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
            <p class="sub">Acesso restrito · gestão do cardápio</p>

            <?php if ($error): ?>
                <div class="alert alert--error"><?= e($error) ?></div>
            <?php endif; ?>

            <?= csrf_field() ?>

            <div class="field">
                <label for="username">Usuário</label>
                <input id="username" name="username" type="text" required autofocus>
            </div>
            <div class="field">
                <label for="password">Senha</label>
                <input id="password" name="password" type="password" required>
            </div>

            <button class="btn btn--primary btn--block" type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>
