<?php
/**
 * Cardápio — Di Bútcher Premium Burger
 */
declare(strict_types=1);

require_once __DIR__ . '/lib/view.php';

try {
    $menu = get_menu_grouped(true);
    $cats = menu_categories();
} catch (Throwable $e) {
    $menu = [];
    $cats = [];
}

// Remove categorias vazias
$cats = array_filter($cats, fn ($slug) => !empty($menu[$slug]), ARRAY_FILTER_USE_KEY);

page_start([
    'active'      => 'cardapio',
    'path'        => '/cardapio',
    'title'       => 'Cardápio · ' . cfg('brand_full_name'),
    'description' => 'Confira o cardápio completo da ' . cfg('brand_name') . ': smashes, acompanhamentos e bebidas.',
]);
?>

<section class="page-head">
    <div class="container">
        <span class="section__eyebrow">O corte</span>
        <h1 class="page-head__title">Cardápio</h1>
        <p class="page-head__sub"><?= e(cfg('promise')) ?></p>
    </div>
</section>

<section class="section section--tight">
    <div class="container">
        <?php if (!$cats): ?>
            <p class="empty-note">Cardápio em atualização. Fale com a gente pelo WhatsApp.</p>
        <?php else: ?>
            <div class="menu-tabs" role="tablist" aria-label="Categorias">
                <button class="menu-tab is-active" type="button" role="tab" data-filter="all">Todos</button>
                <?php foreach ($cats as $slug => $label): ?>
                    <button class="menu-tab" type="button" role="tab" data-filter="<?= e($slug) ?>"><?= e($label) ?></button>
                <?php endforeach; ?>
            </div>

            <?php foreach ($cats as $slug => $label): ?>
                <div class="menu-category" data-category="<?= e($slug) ?>">
                    <h2 class="menu-category__title"><?= e($label) ?></h2>
                    <div class="menu-grid">
                        <?php foreach ($menu[$slug] as $item) { menu_card($item); } ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<?php page_end(); ?>
