<?php
/**
 * Setup inicial — cria o primeiro usuário admin.
 * Acessível apenas enquanto NÃO existir nenhum admin cadastrado.
 */
declare(strict_types=1);

require_once __DIR__ . '/partials.php';

auth_boot();

// Se já existe admin, este fluxo está bloqueado.
if (has_admin()) {
    header('Location: /admin/login.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $user  = trim((string) ($_POST['username'] ?? ''));
    $pass  = (string) ($_POST['password'] ?? '');
    $pass2 = (string) ($_POST['password2'] ?? '');

    if ($user === '' || $pass === '') {
        $error = 'Preencha usuário e senha.';
    } elseif (strlen($pass) < 8) {
        $error = 'A senha precisa ter pelo menos 8 caracteres.';
    } elseif ($pass !== $pass2) {
        $error = 'As senhas não conferem.';
    } elseif (create_admin($user, $pass)) {
        login($user, $pass);
        // Vai direto para o cadastro do 2FA (Google Authenticator)
        header('Location: /admin/enroll.php');
        exit;
    } else {
        $error = 'Não foi possível criar o usuário. Tente outro nome.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Configuração inicial · Admin Di Bútcher</title>
    <link rel="icon" href="/assets/img/favicon.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="/admin/assets/admin.css">
</head>
<body>
    <div class="auth-screen">
        <form class="auth-box" method="post" action="/admin/setup.php" autocomplete="off">
            <img src="/assets/img/logo.png" alt="Di Bútcher">
            <h1>Configuração inicial</h1>
            <p class="sub">Crie o usuário administrador para gerenciar o cardápio.</p>

            <?php if ($error): ?>
                <div class="alert alert--error"><?= e($error) ?></div>
            <?php endif; ?>

            <?= csrf_field() ?>

            <div class="field">
                <label for="username">Usuário</label>
                <input id="username" name="username" type="text" required
                       value="<?= e($_POST['username'] ?? '') ?>">
            </div>
            <div class="field">
                <label for="password">Senha (mín. 8 caracteres)</label>
                <input id="password" name="password" type="password" required minlength="8">
            </div>
            <div class="field">
                <label for="password2">Confirmar senha</label>
                <input id="password2" name="password2" type="password" required minlength="8">
            </div>

            <button class="btn btn--primary btn--block" type="submit">Criar e entrar</button>
        </form>
    </div>
</body>
</html>
