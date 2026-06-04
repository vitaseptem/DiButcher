<?php
/**
 * Di Bútcher Premium Burger — Página principal (single page)
 * São Luís, MA · Est. 2026
 * PHP 8.2+
 */

declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/lib/menu.php';

/**
 * Cardápio vem do banco SQLite. Se o banco estiver indisponível
 * (ex.: permissão de escrita), cai no array do config.php — o site
 * público nunca quebra.
 *
 * @var array<string, array<int, array{nome:string,descricao:string,preco:string,badge:?string}>> $menu
 */
try {
    $menu       = get_menu_grouped(true);
    $categorias = menu_categories();
} catch (Throwable $e) {
    $menu       = $GLOBALS['menu'];
    $categorias = [
        'smashes'         => 'Smashes',
        'acompanhamentos' => 'Acompanhamentos',
        'bebidas'         => 'Bebidas',
    ];
}

$waMain = htmlspecialchars(whatsapp_link(), ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<?php require __DIR__ . '/components/head.php'; ?>
<body data-wa-number="<?= htmlspecialchars(WHATSAPP_NUMBER, ENT_QUOTES, 'UTF-8') ?>">

    <a class="skip-link" href="#cardapio">Pular para o cardápio</a>

    <?php require __DIR__ . '/components/header.php'; ?>

    <main id="main">

        <!-- ───────────── [1] HERO ───────────── -->
        <section class="hero" id="hero">
            <div class="hero__inner">
                <img class="hero__logo" src="/assets/img/logo.png"
                     alt="<?= htmlspecialchars(BRAND_FULL_NAME, ENT_QUOTES, 'UTF-8') ?>"
                     width="230" height="230" fetchpriority="high">

                <h1 class="hero__title">Di <span class="accent">Bútcher</span></h1>

                <p class="hero__slogan"><?= htmlspecialchars(BRAND_SLOGAN_MAIN, ENT_QUOTES, 'UTF-8') ?></p>

                <p class="hero__promise"><?= htmlspecialchars(BRAND_PROMISE, ENT_QUOTES, 'UTF-8') ?></p>

                <div class="hero__ctas">
                    <a class="btn btn--primary" href="<?= $waMain ?>" target="_blank" rel="noopener">
                        Fazer Pedido no WhatsApp
                    </a>
                    <a class="btn btn--outline" href="#cardapio">Ver Cardápio</a>
                </div>
            </div>

            <a class="hero__scroll" href="#cardapio" aria-label="Rolar para o cardápio">
                <span>Role</span>
                <span class="hero__scroll-line" aria-hidden="true"></span>
            </a>
        </section>

        <!-- ───────────── [2] CARDÁPIO ───────────── -->
        <section class="section reveal" id="cardapio">
            <div class="container">
                <div class="section__head">
                    <span class="section__eyebrow">O Corte</span>
                    <h2 class="section__title">Cardápio</h2>
                    <hr class="divider">
                </div>

                <div class="menu-tabs" role="tablist" aria-label="Categorias do cardápio">
                    <button class="menu-tab is-active" role="tab" aria-selected="true"
                            data-filter="all">Todos</button>
                    <?php foreach ($categorias as $slug => $label): ?>
                        <button class="menu-tab" role="tab" aria-selected="false"
                                data-filter="<?= htmlspecialchars($slug, ENT_QUOTES, 'UTF-8') ?>">
                            <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                        </button>
                    <?php endforeach; ?>
                </div>

                <?php foreach ($categorias as $slug => $label): ?>
                    <?php if (empty($menu[$slug])) { continue; } ?>
                    <div class="menu-category" data-category="<?= htmlspecialchars($slug, ENT_QUOTES, 'UTF-8') ?>">
                        <h3 class="menu-category__title"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></h3>
                        <div class="menu-grid">
                            <?php foreach ($menu[$slug] as $item): ?>
                                <?php require __DIR__ . '/components/menu-item.php'; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- ───────────── [3] MANIFESTO / HISTÓRIA ───────────── -->
        <section class="section reveal" id="historia">
            <div class="container">
                <div class="section__head">
                    <span class="section__eyebrow">Origin Story · São Luís 2026</span>
                    <h2 class="section__title">A História</h2>
                    <hr class="divider divider--red">
                </div>

                <div class="manifesto">
                    <p>São Luís, 2026. Uma cidade que carrega séculos de história, azulejos coloniais, o calor do Nordeste e uma cultura gastronômica que mistura força e sabor. Foi nesse contexto que nasceu Di Bútcher — não como mais uma hamburgueria, mas como uma declaração.</p>

                    <p>O nome carrega intenção. <strong>“Di”</strong> — uma partícula que soa italiana, aristocrática, como quem assina uma obra de arte. <strong>“Bútcher”</strong> — o açougueiro. Aquele que domina a carne com maestria, que conhece cada corte, que transforma o bruto em algo extraordinário.</p>

                    <p>Di Bútcher não vende hambúrguer. Vende o ritual da carne. Nasceu em casa, nas mãos de quem acredita que comida de verdade não precisa de palco grandioso — precisa de obsessão com o produto.</p>

                    <blockquote class="manifesto__quote">
                        “Começamos em casa. Crescemos com propósito.”
                        <cite>Di Bútcher · Manifesto da Marca · 2026</cite>
                    </blockquote>

                    <p>Cada blend desenvolvido com precisão. Cada smash executado com técnica. Cada entrega como se fosse a última. Uma marca construída para crescer — do fogão de casa para a cidade. Da cidade para o mundo.</p>
                </div>
            </div>
        </section>

        <!-- ───────────── [4] DELIVERY & CONTATO ───────────── -->
        <section class="section reveal" id="delivery">
            <div class="container">
                <div class="section__head">
                    <span class="section__eyebrow">Peça já</span>
                    <h2 class="section__title">Delivery</h2>
                    <hr class="divider">
                </div>

                <div class="delivery-grid">
                    <a class="delivery-card" href="<?= $waMain ?>" target="_blank" rel="noopener">
                        <span class="delivery-card__icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="#25D366">
                                <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38a9.9 9.9 0 0 0 4.79 1.22h.01c5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2zm5.8 14.16c-.24.68-1.42 1.31-1.96 1.36-.5.05-.97.24-3.27-.68-2.75-1.08-4.52-3.86-4.66-4.04-.14-.18-1.12-1.49-1.12-2.84 0-1.35.71-2.01.96-2.29.24-.27.53-.34.71-.34l.51.01c.16.01.38-.06.6.46.24.55.81 1.9.88 2.04.07.14.12.3.02.48-.09.18-.14.3-.27.46-.14.16-.29.36-.41.48-.14.14-.28.29-.12.57.16.27.71 1.17 1.53 1.9 1.05.94 1.94 1.23 2.21 1.37.27.14.43.12.59-.07.16-.18.68-.79.86-1.06.18-.27.36-.23.6-.14.25.09 1.58.75 1.85.89.27.14.45.2.52.32.07.11.07.66-.17 1.34z"/>
                            </svg>
                        </span>
                        <h3>WhatsApp</h3>
                        <p class="delivery-card__detail"><?= htmlspecialchars(WHATSAPP_DISPLAY, ENT_QUOTES, 'UTF-8') ?></p>
                        <span class="btn btn--whatsapp btn--block">Pedir agora</span>
                    </a>

                    <a class="delivery-card" href="<?= htmlspecialchars(IFOOD_URL, ENT_QUOTES, 'UTF-8') ?>"
                       target="_blank" rel="noopener">
                        <span class="delivery-card__icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="#EA1D2C" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 2v7c0 1.1.9 2 2 2h0a2 2 0 0 0 2-2V2"></path>
                                <line x1="5" y1="11" x2="5" y2="22"></line>
                                <path d="M17 2a3 3 0 0 0-3 3v6h3"></path>
                                <line x1="17" y1="2" x2="17" y2="22"></line>
                            </svg>
                        </span>
                        <h3>iFood</h3>
                        <p class="delivery-card__detail">Peça pelo app</p>
                        <span class="btn btn--outline btn--block">Abrir no iFood</span>
                    </a>
                </div>

                <div class="delivery-info">
                    <div class="delivery-info__hours">
                        <span><?= htmlspecialchars(HORARIO_SEMANA, ENT_QUOTES, 'UTF-8') ?></span>
                        <span><?= htmlspecialchars(HORARIO_FIM_DE_SEMANA, ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <span class="delivery-info__area">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                        <?= htmlspecialchars(DELIVERY_AREA, ENT_QUOTES, 'UTF-8') ?>
                    </span>
                </div>
            </div>
        </section>

    </main>

    <?php require __DIR__ . '/components/footer.php'; ?>
    <?php require __DIR__ . '/components/whatsapp-float.php'; ?>

    <script src="/assets/js/app.js" defer></script>
</body>
</html>
