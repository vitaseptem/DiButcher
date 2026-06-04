<?php
/**
 * Dashboard do admin — lista e gerencia os itens do cardápio.
 */
declare(strict_types=1);

require_once __DIR__ . '/partials.php';
require_once __DIR__ . '/../lib/menu.php';

auth_boot();

if (!has_admin()) {
    header('Location: /admin/setup.php');
    exit;
}

require_login();

$flash = $_GET['ok'] ?? '';
$items = get_all_items();
$cats  = menu_categories();

$grouped = [];
foreach (array_keys($cats) as $slug) {
    $grouped[$slug] = [];
}
foreach ($items as $item) {
    $grouped[$item['categoria']][] = $item;
}

admin_header('Cardápio', 'cardapio');
?>
<div class="admin-head">
    <h1>Cardápio</h1>
    <a class="btn btn--primary" href="/admin/item-edit.php">+ Novo item</a>
</div>

<?php if ($flash): ?>
    <div class="alert alert--ok">
        <?= match ($flash) {
            'created' => 'Item adicionado com sucesso.',
            'updated' => 'Item atualizado com sucesso.',
            'deleted' => 'Item removido.',
            'toggled' => 'Status do item atualizado.',
            default   => 'Operação concluída.',
        } ?>
    </div>
<?php endif; ?>

<?php if (!$items): ?>
    <div class="card"><div class="empty">Nenhum item cadastrado ainda.</div></div>
<?php else: ?>
    <?php foreach ($grouped as $slug => $rows): ?>
        <?php if (!$rows) { continue; } ?>
        <section class="cat-group">
            <h2 class="cat-group__title"><?= e($cats[$slug] ?? $slug) ?></h2>
            <div class="card">
                <?php foreach ($rows as $item): ?>
                    <div class="item-row <?= $item['ativo'] ? '' : 'is-inactive' ?>">
                        <div class="item-row__main">
                            <?php if (!empty($item['imagem'])): ?>
                                <img class="item-row__thumb" src="<?= e($item['imagem']) ?>" alt="" loading="lazy">
                            <?php else: ?>
                                <span class="item-row__thumb item-row__thumb--empty">—</span>
                            <?php endif; ?>
                            <div class="item-row__text">
                                <div class="item-row__name">
                                    <?= e($item['nome']) ?>
                                    <?php if ($item['badge']): ?>
                                        <span class="tag"><?= e($item['badge']) ?></span>
                                    <?php endif; ?>
                                    <?php if (!$item['ativo']): ?>
                                        <span class="tag tag--off">Oculto</span>
                                    <?php endif; ?>
                                </div>
                                <div class="item-row__desc"><?= e($item['descricao']) ?></div>
                            </div>
                        </div>
                        <div class="item-row__actions">
                            <span class="item-row__price"><?= e(format_price((int) $item['preco_centavos'])) ?></span>
                            <a class="btn btn--ghost btn--sm" href="/admin/item-edit.php?id=<?= (int) $item['id'] ?>">Editar</a>
                            <form method="post" action="/admin/item-toggle.php" style="display:inline">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                <button class="btn btn--ghost btn--sm" type="submit">
                                    <?= $item['ativo'] ? 'Ocultar' : 'Mostrar' ?>
                                </button>
                            </form>
                            <form method="post" action="/admin/item-delete.php" style="display:inline"
                                  onsubmit="return confirm('Remover &quot;<?= e($item['nome']) ?>&quot; definitivamente?');">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                <button class="btn btn--danger btn--sm" type="submit">Excluir</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endforeach; ?>
<?php endif; ?>

<?php admin_footer(); ?>
