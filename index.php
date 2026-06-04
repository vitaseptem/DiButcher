<?php
/**
 * Início — Di Bútcher Premium Burger
 */
declare(strict_types=1);

require_once __DIR__ . '/lib/view.php';

try {
    $menu = get_menu_grouped(true);
} catch (Throwable $e) {
    $menu = [];
}

// Destaques: itens com selo, senão os primeiros que aparecerem
$destaques = [];
foreach ($menu as $itens) {
    foreach ($itens as $it) {
        if (!empty($it['badge'])) {
            $destaques[] = $it;
        }
    }
}
if (count($destaques) < 3) {
    foreach ($menu as $itens) {
        foreach ($itens as $it) {
            $destaques[$it['id']] = $it;
        }
    }
    $destaques = array_values($destaques);
}
$destaques = array_slice($destaques, 0, 3);

page_start([
    'active' => '',
    'path'   => '/',
]);
?>

<section class="hero">
    <div class="container hero__inner">
        <img class="hero__logo" src="/assets/img/logo.png"
             alt="<?= e(cfg('brand_full_name')) ?>" width="180" height="180" fetchpriority="high">
        <h1 class="hero__title"><?= e(cfg('brand_name')) ?></h1>
        <?php if (cfg('slogan_main')): ?>
            <p class="hero__slogan"><?= e(cfg('slogan_main')) ?></p>
        <?php endif; ?>
        <?php if (cfg('promise')): ?>
            <p class="hero__promise"><?= e(cfg('promise')) ?></p>
        <?php endif; ?>
        <div class="hero__ctas">
            <a class="btn btn--primary" href="/cardapio">Ver cardápio</a>
            <a class="btn btn--outline" href="<?= e(wa_link()) ?>" target="_blank" rel="noopener">Pedir no WhatsApp</a>
        </div>
    </div>
</section>

<?php if ($destaques): ?>
<section class="section">
    <div class="container">
        <div class="section__head">
            <span class="section__eyebrow">Seleção da casa</span>
            <h2 class="section__title">Destaques</h2>
        </div>
        <div class="menu-grid">
            <?php foreach ($destaques as $item) { menu_card($item); } ?>
        </div>
        <div class="section__more">
            <a class="btn btn--ghost" href="/cardapio">Ver cardápio completo</a>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="section section--split">
    <div class="container split">
        <div class="split__media">
            <img src="/assets/img/og-image.jpg" alt="<?= e(cfg('brand_full_name')) ?>" loading="lazy">
        </div>
        <div class="split__text">
            <span class="section__eyebrow">A casa</span>
            <h2 class="section__title">Vende o ritual da carne</h2>
            <p>Nascida em São Luís, a <?= e(cfg('brand_name')) ?> trata cada blend com precisão
               e cada smash com técnica. Comida de verdade não precisa de palco grandioso —
               precisa de obsessão com o produto.</p>
            <a class="btn btn--ghost" href="/sobre">Nossa história</a>
        </div>
    </div>
</section>

<section class="section section--cta">
    <div class="container cta-band">
        <div>
            <h2 class="section__title">Bateu a fome?</h2>
            <p><?= e(cfg('delivery_area') ?: 'Peça já o seu.') ?></p>
        </div>
        <div class="cta-band__actions">
            <a class="btn btn--primary" href="/cardapio">Montar pedido</a>
            <?php if (cfg('ifood_url')): ?>
                <a class="btn btn--outline" href="<?= e(cfg('ifood_url')) ?>" target="_blank" rel="noopener">Pelo iFood</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php page_end(); ?>
