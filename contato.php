<?php
/**
 * Contato & Delivery — Di Bútcher Premium Burger
 */
declare(strict_types=1);

require_once __DIR__ . '/lib/view.php';

$endereco = trim(implode(', ', array_filter([
    cfg('address_street'),
    cfg('address_district'),
])));
$cidade = trim(cfg('address_city') . ' — ' . cfg('address_state'), ' —');

page_start([
    'active'      => 'contato',
    'path'        => '/contato',
    'title'       => 'Contato & Delivery · ' . cfg('brand_full_name'),
    'description' => 'Peça pelo WhatsApp ou iFood. ' . cfg('delivery_area'),
]);
?>

<section class="page-head">
    <div class="container">
        <span class="section__eyebrow">Peça já</span>
        <h1 class="page-head__title">Contato &amp; Delivery</h1>
    </div>
</section>

<section class="section section--tight">
    <div class="container">
        <div class="contact-grid">
            <a class="contact-card" href="<?= e(wa_link()) ?>" target="_blank" rel="noopener">
                <span class="contact-card__icon"><?= icon('whatsapp') ?></span>
                <h2>WhatsApp</h2>
                <?php if (cfg('whatsapp_display')): ?>
                    <p class="contact-card__detail"><?= e(cfg('whatsapp_display')) ?></p>
                <?php endif; ?>
                <span class="btn btn--whatsapp btn--block">Chamar no WhatsApp</span>
            </a>

            <?php if (cfg('ifood_url')): ?>
            <a class="contact-card" href="<?= e(cfg('ifood_url')) ?>" target="_blank" rel="noopener">
                <span class="contact-card__icon contact-card__icon--ifood">iFood</span>
                <h2>iFood</h2>
                <p class="contact-card__detail">Peça pelo aplicativo</p>
                <span class="btn btn--outline btn--block">Abrir no iFood</span>
            </a>
            <?php endif; ?>
        </div>

        <div class="info-grid">
            <div class="info-block">
                <span class="info-block__icon"><?= icon('clock') ?></span>
                <h3>Horário</h3>
                <?php if (cfg('hours_week')): ?><p><?= e(cfg('hours_week')) ?></p><?php endif; ?>
                <?php if (cfg('hours_weekend')): ?><p><?= e(cfg('hours_weekend')) ?></p><?php endif; ?>
            </div>

            <div class="info-block">
                <span class="info-block__icon"><?= icon('pin') ?></span>
                <h3>Onde estamos</h3>
                <?php if ($endereco): ?><p><?= e($endereco) ?></p><?php endif; ?>
                <?php if ($cidade): ?><p><?= e($cidade) ?></p><?php endif; ?>
                <?php if (cfg('delivery_area')): ?><p class="muted"><?= e(cfg('delivery_area')) ?></p><?php endif; ?>
                <?php if (cfg('maps_url')): ?>
                    <a class="link" href="<?= e(cfg('maps_url')) ?>" target="_blank" rel="noopener">Ver no mapa →</a>
                <?php endif; ?>
            </div>

            <?php if (cfg('cnpj') || cfg('razao_social') || cfg('email')): ?>
            <div class="info-block">
                <span class="info-block__icon">DI</span>
                <h3>Dados</h3>
                <?php if (cfg('razao_social')): ?><p><?= e(cfg('razao_social')) ?></p><?php endif; ?>
                <?php if (cfg('cnpj')): ?><p>CNPJ: <?= e(cfg('cnpj')) ?></p><?php endif; ?>
                <?php if (cfg('email')): ?><p><?= e(cfg('email')) ?></p><?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php page_end(); ?>
