<?php
/**
 * Configurações do site — todos os dados do negócio.
 */
declare(strict_types=1);

require_once __DIR__ . '/partials.php';

auth_boot();
require_login();

$saved = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    try {
        $data = $_POST;
        // checkbox: presente = 1, ausente = 0
        $data['enable_cart'] = isset($_POST['enable_cart']) ? '1' : '0';
        save_settings($data);
        $saved = true;
    } catch (Throwable $ex) {
        $error = 'Erro ao salvar: ' . $ex->getMessage();
    }
}

$s = all_settings(true);

/** Helper de campo de texto. */
function field(string $key, string $label, array $s, string $hint = '', string $type = 'text'): void
{
    echo '<div class="field"><label for="' . e($key) . '">' . e($label) . '</label>';
    echo '<input id="' . e($key) . '" name="' . e($key) . '" type="' . e($type) . '" value="' . e($s[$key] ?? '') . '">';
    if ($hint) {
        echo '<p class="field__hint">' . e($hint) . '</p>';
    }
    echo '</div>';
}

admin_header('Configurações', 'config');
?>
<div class="admin-head">
    <h1>Configurações</h1>
    <a class="btn btn--ghost" href="/" target="_blank" rel="noopener">Ver site ↗</a>
</div>

<?php if ($saved): ?><div class="alert alert--ok">Configurações salvas com sucesso.</div><?php endif; ?>
<?php if ($error): ?><div class="alert alert--error"><?= e($error) ?></div><?php endif; ?>

<form class="form form--wide" method="post" action="/admin/settings.php">
    <?= csrf_field() ?>

    <fieldset class="settings-group">
        <legend>Marca</legend>
        <?php
        field('brand_name', 'Nome curto', $s, 'Ex.: Di Bútcher');
        field('brand_full_name', 'Nome completo', $s, 'Ex.: Di Bútcher Premium Burger');
        field('slogan_main', 'Slogan principal', $s);
        field('promise', 'Promessa / frase de impacto', $s);
        ?>
    </fieldset>

    <fieldset class="settings-group">
        <legend>Contato &amp; Redes</legend>
        <div class="field-row">
            <?php
            field('whatsapp_number', 'WhatsApp (só números, com DDI)', $s, 'Ex.: 5598981010101');
            field('whatsapp_display', 'WhatsApp (exibição)', $s, 'Ex.: (98) 98101-0101');
            ?>
        </div>
        <div class="field-row">
            <?php
            field('phone', 'Telefone fixo (opcional)', $s);
            field('email', 'E-mail (opcional)', $s, '', 'email');
            ?>
        </div>
        <?php
        field('ifood_url', 'Link do iFood', $s, '', 'url');
        field('instagram_url', 'Instagram', $s, '', 'url');
        field('tiktok_url', 'TikTok', $s, '', 'url');
        ?>
    </fieldset>

    <fieldset class="settings-group">
        <legend>Dados da empresa (opcional)</legend>
        <?php
        field('razao_social', 'Razão social', $s);
        field('cnpj', 'CNPJ', $s, 'Aparece no rodapé e na página de contato, se preenchido.');
        ?>
    </fieldset>

    <fieldset class="settings-group">
        <legend>Endereço</legend>
        <?php field('address_street', 'Rua e número', $s); ?>
        <div class="field-row">
            <?php
            field('address_district', 'Bairro', $s);
            field('address_cep', 'CEP', $s);
            ?>
        </div>
        <div class="field-row">
            <?php
            field('address_city', 'Cidade', $s);
            field('address_state', 'Estado (UF)', $s);
            ?>
        </div>
        <?php field('maps_url', 'Link do Google Maps', $s, '', 'url'); ?>
    </fieldset>

    <fieldset class="settings-group">
        <legend>Operação &amp; Delivery</legend>
        <?php
        field('hours_week', 'Horário (semana)', $s, 'Ex.: Terça a Domingo: 18h – 23h');
        field('hours_weekend', 'Horário (observação)', $s, 'Ex.: Segunda-feira: Fechado');
        field('delivery_area', 'Área de entrega', $s, 'Ex.: Entregamos em toda São Luís — MA');
        ?>
        <div class="field-row">
            <?php
            field('delivery_fee', 'Taxa de entrega (R$)', $s, '0 = grátis/ocultar. Ex.: 5,00');
            field('min_order', 'Pedido mínimo (R$)', $s, '0 = sem mínimo');
            ?>
        </div>
        <div class="field check">
            <input id="enable_cart" name="enable_cart" type="checkbox" value="1" <?= ($s['enable_cart'] ?? '1') === '1' ? 'checked' : '' ?>>
            <label for="enable_cart">Ativar carrinho de compras no site</label>
        </div>
    </fieldset>

    <fieldset class="settings-group">
        <legend>SEO</legend>
        <?php
        field('seo_title', 'Título (aba do navegador / Google)', $s);
        ?>
        <div class="field">
            <label for="seo_description">Descrição (Google / redes)</label>
            <textarea id="seo_description" name="seo_description" maxlength="300"><?= e($s['seo_description'] ?? '') ?></textarea>
        </div>
        <?php field('site_url', 'URL do site', $s, 'Ex.: https://dibutcher.com.br', 'url'); ?>
    </fieldset>

    <div class="form-actions form-actions--sticky">
        <button class="btn btn--primary" type="submit">Salvar configurações</button>
    </div>
</form>

<?php admin_footer(); ?>
