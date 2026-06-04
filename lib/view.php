<?php
/**
 * Camada de apresentação do site público (layout multi-página).
 * Centraliza <head>, header, footer, drawer do carrinho e botão flutuante.
 */

declare(strict_types=1);

require_once __DIR__ . '/settings.php';
require_once __DIR__ . '/menu.php';

if (!function_exists('e')) {
    function e(?string $v): string
    {
        return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
    }
}

/**
 * Ícones SVG inline reutilizáveis.
 */
function icon(string $name): string
{
    $icons = [
        'whatsapp' => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38a9.9 9.9 0 0 0 4.79 1.22h.01c5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2zm5.8 14.16c-.24.68-1.42 1.31-1.96 1.36-.5.05-.97.24-3.27-.68-2.75-1.08-4.52-3.86-4.66-4.04-.14-.18-1.12-1.49-1.12-2.84 0-1.35.71-2.01.96-2.29.24-.27.53-.34.71-.34l.51.01c.16.01.38-.06.6.46.24.55.81 1.9.88 2.04.07.14.12.3.02.48-.09.18-.14.3-.27.46-.14.16-.29.36-.41.48-.14.14-.28.29-.12.57.16.27.71 1.17 1.53 1.9 1.05.94 1.94 1.23 2.21 1.37.27.14.43.12.59-.07.16-.18.68-.79.86-1.06.18-.27.36-.23.6-.14.25.09 1.58.75 1.85.89.27.14.45.2.52.32.07.11.07.66-.17 1.34z"/></svg>',
        'cart'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>',
        'instagram'=> '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="2" width="20" height="20" rx="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>',
        'tiktok'   => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M16.6 5.82A4.28 4.28 0 0 1 15.54 3h-3.09v12.4a2.59 2.59 0 0 1-2.59 2.5 2.59 2.59 0 0 1 0-5.18c.27 0 .53.04.77.12v-3.2a5.78 5.78 0 0 0-.77-.05A5.78 5.78 0 1 0 15.64 15.4V9.01a7.5 7.5 0 0 0 4.36 1.39V7.31a4.28 4.28 0 0 1-3.4-1.49z"/></svg>',
        'pin'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>',
        'clock'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><polyline points="12 7 12 12 15 14"></polyline></svg>',
        'plus'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>',
        'minus'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"></line></svg>',
        'close'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>',
        'user'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>',
        'phone'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>',
        'truck'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>',
        'plate'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><circle cx="12" cy="12" r="4"></circle></svg>',
        'money'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="6" width="20" height="12" rx="2"></rect><circle cx="12" cy="12" r="2.5"></circle><path d="M6 12h.01M18 12h.01"></path></svg>',
        'note'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="8" y1="13" x2="16" y2="13"></line><line x1="8" y1="17" x2="13" y2="17"></line></svg>',
        'cleaver'  => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3 3l1.5 1.5L9 9l-1.4 1.4-1-1L3 13.9V21h2.1l4.5-4.5 1 1L12 17l9 9-1.4-1.4-8.2-8.2 6.3-6.3c1.2-1.2 1.2-3.1 0-4.3-1.2-1.2-3.1-1.2-4.3 0L9 7.6 3 3z"/></svg>',
        'home'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 9.5L12 3l9 6.5"></path><path d="M5 10v10h14V10"></path></svg>',
        'menu'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" aria-hidden="true"><line x1="4" y1="7" x2="20" y2="7"></line><line x1="4" y1="12" x2="20" y2="12"></line><line x1="4" y1="17" x2="20" y2="17"></line></svg>',
    ];
    return $icons[$name] ?? '';
}

/**
 * Início da página: doctype, head e header.
 *
 * @param array{title?:string,description?:string,active?:string,path?:string} $opts
 */
function page_start(array $opts = []): void
{
    $brand   = cfg('brand_name', 'Di Bútcher');
    $full    = cfg('brand_full_name');
    $title   = $opts['title'] ?? cfg('seo_title', $full);
    $desc    = $opts['description'] ?? cfg('seo_description');
    $active  = $opts['active'] ?? '';
    $GLOBALS['__page_active'] = $active;
    $siteUrl = rtrim(cfg('site_url'), '/');
    $path    = $opts['path'] ?? ($_SERVER['REQUEST_URI'] ?? '/');
    $canonical = $siteUrl . $path;
    $ogImage = $siteUrl . '/assets/img/og-image.jpg';
    ?><!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title) ?></title>
    <meta name="description" content="<?= e($desc) ?>">
    <meta name="theme-color" content="#0D0D0D">
    <?php if ($siteUrl): ?><link rel="canonical" href="<?= e($canonical) ?>"><?php endif; ?>
    <meta name="robots" content="index, follow, max-image-preview:large">

    <meta property="og:type" content="restaurant.restaurant">
    <meta property="og:site_name" content="<?= e($full) ?>">
    <meta property="og:title" content="<?= e($title) ?>">
    <meta property="og:description" content="<?= e($desc) ?>">
    <meta property="og:image" content="<?= e($ogImage) ?>">
    <?php if ($siteUrl): ?><meta property="og:url" content="<?= e($canonical) ?>"><?php endif; ?>
    <meta property="og:locale" content="pt_BR">
    <meta name="twitter:card" content="summary_large_image">

    <link rel="icon" href="/assets/img/favicon.ico" sizes="any">
    <link rel="apple-touch-icon" href="/assets/img/logo.png">

    <script type="application/ld+json">
    <?= json_encode(restaurant_schema(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Abril+Fatface&family=Oswald:wght@400;500;600&family=Inter:wght@300;400;500;600&display=swap">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/components.css">
</head>
<body data-page="<?= e($active) ?>">
    <a class="skip-link" href="#main">Pular para o conteúdo</a>
    <?php site_header($active); ?>
    <main id="main">
<?php
}

/**
 * Fim da página: footer, drawer do carrinho, botão flutuante e scripts.
 */
function page_end(): void
{
    ?>
    </main>
    <?php site_footer(); ?>
    <?php if (cart_enabled()) { cart_drawer(); } ?>
    <a class="wa-float" href="<?= e(wa_link()) ?>" target="_blank" rel="noopener"
       aria-label="Fazer pedido pelo WhatsApp"><?= icon('whatsapp') ?></a>
    <?php mobile_nav($GLOBALS['__page_active'] ?? ''); ?>

    <script>
    window.DIBUTCHER = {
        waNumber: <?= json_encode(wa_number()) ?>,
        brand: <?= json_encode(cfg('brand_name')) ?>,
        deliveryFeeCents: <?= delivery_fee_cents() ?>,
        minOrderCents: <?= min_order_cents() ?>,
        cartEnabled: <?= cart_enabled() ? 'true' : 'false' ?>
    };
    </script>
    <script src="/assets/js/app.js" defer></script>
    <?php if (cart_enabled()): ?><script src="/assets/js/cart.js" defer></script><?php endif; ?>
</body>
</html>
<?php
}

/**
 * Schema.org Restaurant a partir das configurações.
 */
function restaurant_schema(): array
{
    $siteUrl = rtrim(cfg('site_url'), '/');
    $schema = [
        '@context'    => 'https://schema.org',
        '@type'       => 'Restaurant',
        'name'        => cfg('brand_full_name'),
        'description' => cfg('seo_description'),
        'servesCuisine' => ['Hambúrguer', 'Smash Burger'],
        'priceRange'  => 'R$ 5,00 - R$ 60,00',
        'address'     => [
            '@type'           => 'PostalAddress',
            'streetAddress'   => trim(cfg('address_street') . ' ' . cfg('address_district')),
            'addressLocality' => cfg('address_city'),
            'addressRegion'   => cfg('address_state'),
            'postalCode'      => cfg('address_cep'),
            'addressCountry'  => 'BR',
        ],
    ];
    if (wa_number()) {
        $schema['telephone'] = '+' . wa_number();
    }
    if ($siteUrl) {
        $schema['url']   = $siteUrl . '/';
        $schema['image'] = $siteUrl . '/assets/img/og-image.jpg';
    }
    $sameAs = array_values(array_filter([cfg('instagram_url'), cfg('tiktok_url')]));
    if ($sameAs) {
        $schema['sameAs'] = $sameAs;
    }
    return $schema;
}

/**
 * Header / navegação.
 */
function site_header(string $active = ''): void
{
    $brand = cfg('brand_name', 'Di Bútcher');
    [$b1, $b2] = explode(' ', $brand . ' ', 2);
    $links = [
        ''         => ['Início', '/'],
        'cardapio' => ['Cardápio', '/cardapio'],
        'sobre'    => ['Sobre', '/sobre'],
        'contato'  => ['Contato', '/contato'],
    ];
    ?>
    <header class="site-header">
        <nav class="nav" aria-label="Navegação principal">
            <a class="nav__brand" href="/" aria-label="<?= e(cfg('brand_full_name')) ?>">
                <img src="/assets/img/logo.png" alt="" width="36" height="36">
                <span><?= e(trim($b1)) ?> <span class="accent"><?= e(trim($b2)) ?></span></span>
            </a>

            <ul class="nav__links">
                <?php foreach ($links as $key => [$label, $href]): ?>
                    <li>
                        <a class="nav__link <?= $active === $key ? 'is-active' : '' ?>"
                           href="<?= e($href) ?>"><?= e($label) ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <div class="nav__actions">
                <?php if (cart_enabled()): ?>
                    <button class="nav__cart" type="button" data-cart-open aria-label="Abrir carrinho">
                        <?= icon('cart') ?>
                        <span class="nav__cart-count" data-cart-count hidden>0</span>
                    </button>
                <?php endif; ?>
                <a class="btn btn--primary btn--sm nav__cta" href="<?= e(wa_link()) ?>"
                   target="_blank" rel="noopener">Pedir</a>
                <button class="nav__toggle" type="button" aria-expanded="false"
                        aria-controls="nav-drawer" aria-label="Abrir menu">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </nav>

        <div class="nav__drawer" id="nav-drawer">
            <?php foreach ($links as $key => [$label, $href]): ?>
                <a class="nav__link" href="<?= e($href) ?>"><?= e($label) ?></a>
            <?php endforeach; ?>
            <a class="btn btn--primary btn--block" href="<?= e(wa_link()) ?>"
               target="_blank" rel="noopener">Fazer pedido no WhatsApp</a>
        </div>
    </header>
    <?php
}

/**
 * Footer.
 */
function site_footer(): void
{
    $brand = cfg('brand_name', 'Di Bútcher');
    [$b1, $b2] = explode(' ', $brand . ' ', 2);
    $insta = cfg('instagram_url');
    $tiktok = cfg('tiktok_url');
    $cnpj = cfg('cnpj');
    ?>
    <footer class="site-footer">
        <div class="container footer__grid">
            <div class="footer__col">
                <p class="footer__brand"><?= e(trim($b1)) ?> <span class="accent"><?= e(trim($b2)) ?></span></p>
                <p class="footer__tagline"><?= e(cfg('slogan_main')) ?></p>
                <?php if ($insta || $tiktok): ?>
                    <div class="footer__socials">
                        <?php if ($insta): ?>
                            <a class="footer__social" href="<?= e($insta) ?>" target="_blank" rel="noopener" aria-label="Instagram"><?= icon('instagram') ?></a>
                        <?php endif; ?>
                        <?php if ($tiktok): ?>
                            <a class="footer__social" href="<?= e($tiktok) ?>" target="_blank" rel="noopener" aria-label="TikTok"><?= icon('tiktok') ?></a>
                        <?php endif; ?>
                        <a class="footer__social" href="<?= e(wa_link()) ?>" target="_blank" rel="noopener" aria-label="WhatsApp"><?= icon('whatsapp') ?></a>
                    </div>
                <?php endif; ?>
            </div>

            <div class="footer__col">
                <h4 class="footer__title">Navegação</h4>
                <a href="/">Início</a>
                <a href="/cardapio">Cardápio</a>
                <a href="/sobre">Sobre</a>
                <a href="/contato">Contato</a>
            </div>

            <div class="footer__col">
                <h4 class="footer__title">Contato</h4>
                <?php if (cfg('whatsapp_display')): ?><p><?= e(cfg('whatsapp_display')) ?></p><?php endif; ?>
                <?php if (cfg('address_city')): ?>
                    <p><?= e(trim(cfg('address_street'))) ?><?= cfg('address_district') ? ', ' . e(cfg('address_district')) : '' ?></p>
                    <p><?= e(cfg('address_city')) ?> — <?= e(cfg('address_state')) ?></p>
                <?php endif; ?>
                <?php if (cfg('hours_week')): ?><p><?= e(cfg('hours_week')) ?></p><?php endif; ?>
            </div>
        </div>

        <div class="container footer__bottom">
            <p><?= e(cfg('slogan_main')) ?></p>
            <p class="footer__copy">
                &copy; <?= date('Y') ?> <?= e(cfg('brand_full_name')) ?>.
                <?php if ($cnpj): ?> · CNPJ <?= e($cnpj) ?><?php endif; ?>
            </p>
        </div>
    </footer>
    <?php
}

/**
 * Card de item do cardápio (com imagem e botão de adicionar).
 */
function menu_card(array $item): void
{
    $badgeClass = match (strtoupper((string) ($item['badge'] ?? ''))) {
        'SIGNATURE'   => 'badge--signature',
        'PREMIUM'     => 'badge--premium',
        'MAIS PEDIDO' => 'badge--mais',
        'FAVORITO'    => 'badge--favorito',
        default       => 'badge--default',
    };
    $hasImg = !empty($item['imagem']);
    $waMsg  = sprintf('Olá! Quero pedir: %s - %s', $item['nome'], $item['preco']);
    ?>
    <article class="menu-card <?= $hasImg ? 'has-img' : '' ?>">
        <?php if ($hasImg): ?>
            <div class="menu-card__media">
                <img src="<?= e($item['imagem']) ?>" alt="<?= e($item['nome']) ?>" loading="lazy">
                <?php if (!empty($item['badge'])): ?>
                    <span class="badge <?= $badgeClass ?>"><?= e($item['badge']) ?></span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <div class="menu-card__body">
            <div class="menu-card__head">
                <h3 class="menu-card__name"><?= e($item['nome']) ?></h3>
                <?php if (!$hasImg && !empty($item['badge'])): ?>
                    <span class="badge <?= $badgeClass ?>"><?= e($item['badge']) ?></span>
                <?php endif; ?>
            </div>
            <p class="menu-card__desc"><?= e($item['descricao']) ?></p>
            <div class="menu-card__foot">
                <span class="menu-card__price"><?= e($item['preco']) ?></span>
                <?php if (cart_enabled()): ?>
                    <button class="btn btn--add" type="button"
                            data-add-to-cart
                            data-id="<?= (int) $item['id'] ?>"
                            data-nome="<?= e($item['nome']) ?>"
                            data-preco="<?= (int) $item['preco_centavos'] ?>"
                            data-img="<?= e($item['imagem'] ?? '') ?>"
                            data-desc="<?= e($item['descricao'] ?? '') ?>">
                        <?= icon('plus') ?> Adicionar
                    </button>
                <?php else: ?>
                    <a class="btn btn--add" href="<?= e(wa_link($waMsg)) ?>" target="_blank" rel="noopener">
                        <?= icon('whatsapp') ?> Pedir
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </article>
    <?php
}

/**
 * Barra de navegação inferior (mobile · estilo app).
 */
function mobile_nav(string $active = ''): void
{
    $items = [
        ['', '/', 'home', 'Início'],
        ['cardapio', '/cardapio', 'menu', 'Cardápio'],
        ['sobre', '/sobre', 'cleaver', 'Sobre'],
        ['contato', '/contato', 'phone', 'Contato'],
    ];
    ?>
    <nav class="mobile-nav" aria-label="Navegação rápida">
        <a class="mobile-nav__item <?= $active === '' ? 'is-active' : '' ?>" href="/"><?= icon('home') ?><span>Início</span></a>
        <a class="mobile-nav__item <?= $active === 'cardapio' ? 'is-active' : '' ?>" href="/cardapio"><?= icon('menu') ?><span>Cardápio</span></a>
        <?php if (cart_enabled()): ?>
            <button class="mobile-nav__item mobile-nav__cart" type="button" data-cart-open aria-label="Abrir carrinho">
                <span class="mobile-nav__cart-wrap"><?= icon('cart') ?><span class="mobile-nav__count" data-cart-count hidden>0</span></span>
                <span>Carrinho</span>
            </button>
        <?php endif; ?>
        <a class="mobile-nav__item <?= $active === 'sobre' ? 'is-active' : '' ?>" href="/sobre"><?= icon('cleaver') ?><span>Sobre</span></a>
        <a class="mobile-nav__item <?= $active === 'contato' ? 'is-active' : '' ?>" href="/contato"><?= icon('phone') ?><span>Contato</span></a>
    </nav>
    <?php
}

/**
 * Drawer lateral do carrinho (preenchido via JS).
 */
function cart_drawer(): void
{
    ?>
    <div class="cart-overlay" data-cart-overlay hidden></div>
    <aside class="cart" id="cart" aria-label="Carrinho" aria-hidden="true">
        <header class="cart__head">
            <h2>Seu pedido</h2>
            <button class="cart__close" type="button" data-cart-close aria-label="Fechar carrinho"><?= icon('close') ?></button>
        </header>
        <div class="cart__items" data-cart-items></div>
        <div class="cart__empty" data-cart-empty>
            <p>Seu carrinho está vazio.</p>
            <a class="btn btn--ghost" href="/cardapio" data-cart-close>Ver cardápio</a>
        </div>
        <footer class="cart__foot" data-cart-foot hidden>
            <div class="cart__total">
                <span>Subtotal</span>
                <strong data-cart-subtotal>R$ 0,00</strong>
            </div>
            <a class="btn btn--primary btn--block" href="/checkout">Finalizar pedido</a>
        </footer>
    </aside>
    <?php
}
