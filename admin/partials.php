<?php
/**
 * Partials de layout do painel admin (header + footer + nav).
 */
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/settings.php';

if (!function_exists('e')) {
    function e(?string $v): string
    {
        return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
    }
}

function admin_header(string $title, string $active = ''): void
{
    $links = [
        'cardapio'  => ['Cardápio', '/admin/'],
        'categorias'=> ['Categorias', '/admin/categories.php'],
        'config'    => ['Configurações', '/admin/settings.php'],
        'conta'     => ['Minha conta', '/admin/account.php'],
    ];
    ?><!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?= e($title) ?> · Admin Di Bútcher</title>
    <link rel="icon" href="/assets/img/favicon.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="/admin/assets/admin.css">
</head>
<body>
    <header class="admin-top">
        <a class="admin-top__brand" href="/admin/">
            <img src="/assets/img/logo.png" alt="">
            <span>Di <span class="accent">Bútcher</span> · Admin</span>
        </a>
        <div class="admin-top__right">
            <a href="/" target="_blank" rel="noopener">Ver site ↗</a>
            <?php if (is_logged_in()): ?>
                <span class="admin-top__user"><?= e($_SESSION['admin_user'] ?? '') ?></span>
                <a class="btn btn--ghost btn--sm" href="/admin/logout.php">Sair</a>
            <?php endif; ?>
        </div>
    </header>

    <?php if (is_logged_in()): ?>
    <nav class="admin-nav" aria-label="Seções do admin">
        <div class="admin-nav__inner">
            <?php foreach ($links as $key => [$label, $href]): ?>
                <a class="admin-nav__link <?= $active === $key ? 'is-active' : '' ?>"
                   href="<?= e($href) ?>"><?= e($label) ?></a>
            <?php endforeach; ?>
        </div>
    </nav>
    <?php endif; ?>

    <main class="admin-wrap">
    <?php
}

function admin_footer(): void
{
    ?>
    </main>
</body>
</html>
    <?php
}
