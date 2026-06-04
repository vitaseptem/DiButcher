<?php
/**
 * Sobre / História — Di Bútcher Premium Burger
 */
declare(strict_types=1);

require_once __DIR__ . '/lib/view.php';

page_start([
    'active'      => 'sobre',
    'path'        => '/sobre',
    'title'       => 'Sobre · ' . cfg('brand_full_name'),
    'description' => 'A história da ' . cfg('brand_name') . ' — nascida em São Luís, MA. Vende o ritual da carne.',
]);
?>

<section class="page-head">
    <div class="container">
        <span class="section__eyebrow">Origin Story · São Luís 2026</span>
        <h1 class="page-head__title">A História</h1>
    </div>
</section>

<section class="section section--tight">
    <div class="container manifesto">
        <p>São Luís, 2026. Uma cidade que carrega séculos de história, azulejos coloniais, o calor do
           Nordeste e uma cultura gastronômica que mistura força e sabor. Foi nesse contexto que nasceu
           <strong><?= e(cfg('brand_name')) ?></strong> — não como mais uma hamburgueria, mas como uma declaração.</p>

        <p>O nome carrega intenção. <strong>“Di”</strong> — uma partícula que soa italiana, aristocrática,
           como quem assina uma obra de arte. <strong>“Bútcher”</strong> — o açougueiro. Aquele que domina
           a carne com maestria, que conhece cada corte, que transforma o bruto em algo extraordinário.</p>

        <p><?= e(cfg('brand_name')) ?> não vende hambúrguer. Vende o ritual da carne. Nasceu em casa, nas
           mãos de quem acredita que comida de verdade não precisa de palco grandioso — precisa de obsessão
           com o produto.</p>

        <blockquote class="manifesto__quote">
            “Começamos em casa. Crescemos com propósito.”
            <cite><?= e(cfg('brand_name')) ?> · Manifesto da Marca · 2026</cite>
        </blockquote>

        <p>Cada blend desenvolvido com precisão. Cada smash executado com técnica. Cada entrega como se
           fosse a última. Uma marca construída para crescer — do fogão de casa para a cidade.
           Da cidade para o mundo.</p>

        <div class="manifesto__cta">
            <a class="btn btn--primary" href="/cardapio">Conhecer o cardápio</a>
        </div>
    </div>
</section>

<?php page_end(); ?>
