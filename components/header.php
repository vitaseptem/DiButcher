<?php
/**
 * Header / Navegação — Di Bútcher Premium Burger
 */
if (!defined('BRAND_NAME')) {
    require_once __DIR__ . '/../config.php';
}
$waHref = htmlspecialchars(whatsapp_link(), ENT_QUOTES, 'UTF-8');
?>
<header class="site-header">
    <nav class="nav" aria-label="Navegação principal">
        <a class="nav__brand" href="#hero" aria-label="<?= htmlspecialchars(BRAND_FULL_NAME, ENT_QUOTES, 'UTF-8') ?> — início">
            <img src="/assets/img/logo.png" alt="" width="40" height="40">
            <span>Di <span class="accent">Bútcher</span></span>
        </a>

        <div class="nav__menu">
            <ul class="nav__links">
                <li><a class="nav__link" href="#cardapio">Cardápio</a></li>
                <li><a class="nav__link" href="#historia">História</a></li>
                <li><a class="nav__link" href="#delivery">Delivery</a></li>
            </ul>

            <a class="btn btn--primary nav__cta" href="<?= $waHref ?>" target="_blank" rel="noopener">
                Fazer Pedido
            </a>

            <button class="nav__toggle" type="button" aria-expanded="false"
                    aria-controls="nav-drawer" aria-label="Abrir menu">
                <span></span><span></span><span></span>
            </button>
        </div>
    </nav>

    <!-- Drawer mobile -->
    <div class="nav__drawer" id="nav-drawer">
        <a class="nav__link" href="#cardapio">Cardápio</a>
        <a class="nav__link" href="#historia">História</a>
        <a class="nav__link" href="#delivery">Delivery</a>
        <a class="btn btn--primary btn--block" href="<?= $waHref ?>" target="_blank" rel="noopener">
            Fazer Pedido no WhatsApp
        </a>
    </div>
</header>
