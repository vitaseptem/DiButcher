<?php
/**
 * Gestão de categorias do cardápio.
 */
declare(strict_types=1);

require_once __DIR__ . '/partials.php';
require_once __DIR__ . '/../lib/categories.php';

auth_boot();
require_login();

$error = '';
$ok    = $_GET['ok'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $nome = trim((string) ($_POST['nome'] ?? ''));
        if ($nome === '') {
            $error = 'Informe o nome da categoria.';
        } else {
            create_category($nome, (int) ($_POST['ordem'] ?? 0));
            header('Location: /admin/categories.php?ok=created');
            exit;
        }
    } elseif ($action === 'update') {
        $cid  = (int) ($_POST['id'] ?? 0);
        $nome = trim((string) ($_POST['nome'] ?? ''));
        if ($cid && $nome !== '') {
            update_category($cid, $nome, (int) ($_POST['ordem'] ?? 0));
            header('Location: /admin/categories.php?ok=updated');
            exit;
        }
        $error = 'Dados inválidos para atualização.';
    } elseif ($action === 'delete') {
        $cid = (int) ($_POST['id'] ?? 0);
        if ($cid && delete_category($cid)) {
            header('Location: /admin/categories.php?ok=deleted');
            exit;
        }
        $error = 'Não foi possível excluir: a categoria possui itens vinculados.';
    }
}

$categories = get_categories();

admin_header('Categorias', 'categorias');
?>
<div class="admin-head">
    <h1>Categorias</h1>
</div>

<?php if ($error): ?><div class="alert alert--error"><?= e($error) ?></div><?php endif; ?>
<?php if ($ok): ?>
    <div class="alert alert--ok"><?= match ($ok) {
        'created' => 'Categoria criada.',
        'updated' => 'Categoria atualizada.',
        'deleted' => 'Categoria removida.',
        default   => 'Pronto.',
    } ?></div>
<?php endif; ?>

<div class="card" style="padding:1.25rem; margin-bottom:1.5rem;">
    <h2 class="cat-group__title">Nova categoria</h2>
    <form method="post" class="inline-form">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="create">
        <input type="text" name="nome" placeholder="Nome (ex.: Combos)" required>
        <input type="number" name="ordem" value="<?= count($categories) ?>" min="0" style="max-width:90px" title="Ordem">
        <button class="btn btn--primary" type="submit">Adicionar</button>
    </form>
</div>

<?php if (!$categories): ?>
    <div class="card"><div class="empty">Nenhuma categoria.</div></div>
<?php else: ?>
    <div class="card">
        <?php foreach ($categories as $c): ?>
            <?php $count = category_item_count($c['slug']); ?>
            <div class="item-row">
                <form method="post" class="inline-form" style="flex:1; min-width:0;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?= (int) $c['id'] ?>">
                    <input type="text" name="nome" value="<?= e($c['nome']) ?>" required style="flex:1;">
                    <input type="number" name="ordem" value="<?= (int) $c['ordem'] ?>" min="0" style="max-width:80px" title="Ordem">
                    <button class="btn btn--ghost btn--sm" type="submit">Salvar</button>
                </form>
                <div class="item-row__actions">
                    <span class="muted" style="font-size:.8rem;"><?= $count ?> item(ns) · <code><?= e($c['slug']) ?></code></span>
                    <form method="post" style="display:inline"
                          onsubmit="return confirm('Excluir a categoria &quot;<?= e($c['nome']) ?>&quot;?');">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= (int) $c['id'] ?>">
                        <button class="btn btn--danger btn--sm" type="submit" <?= $count > 0 ? 'disabled title="Remova ou mova os itens primeiro"' : '' ?>>Excluir</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <p class="field__hint" style="margin-top:.75rem;">O endereço interno (slug) é fixado na criação para não quebrar itens já vinculados.</p>
<?php endif; ?>

<?php admin_footer(); ?>
