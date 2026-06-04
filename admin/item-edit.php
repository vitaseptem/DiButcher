<?php
/**
 * Formulário de criação / edição de item do cardápio.
 */
declare(strict_types=1);

require_once __DIR__ . '/partials.php';
require_once __DIR__ . '/../lib/menu.php';

auth_boot();
require_login();

$cats   = menu_categories();
$badges = menu_badges();

$id   = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$item = $id ? get_item($id) : null;

if ($id && !$item) {
    http_response_code(404);
    exit('Item não encontrado.');
}

$error = '';

// Valores do formulário (item existente ou padrões)
$form = [
    'categoria' => $item['categoria'] ?? array_key_first($cats),
    'nome'      => $item['nome'] ?? '',
    'descricao' => $item['descricao'] ?? '',
    'preco'     => $item ? format_price((int) $item['preco_centavos']) : '',
    'badge'     => $item['badge'] ?? '',
    'ordem'     => $item['ordem'] ?? 0,
    'ativo'     => $item['ativo'] ?? 1,
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $form = [
        'categoria' => (string) ($_POST['categoria'] ?? ''),
        'nome'      => trim((string) ($_POST['nome'] ?? '')),
        'descricao' => trim((string) ($_POST['descricao'] ?? '')),
        'preco'     => trim((string) ($_POST['preco'] ?? '')),
        'badge'     => (string) ($_POST['badge'] ?? ''),
        'ordem'     => (int) ($_POST['ordem'] ?? 0),
        'ativo'     => isset($_POST['ativo']) ? 1 : 0,
    ];

    if ($form['nome'] === '') {
        $error = 'Informe o nome do item.';
    } elseif (!isset($cats[$form['categoria']])) {
        $error = 'Categoria inválida.';
    } elseif ($form['preco'] === '' || price_to_cents($form['preco']) <= 0) {
        $error = 'Informe um preço válido (ex.: 32,00).';
    } elseif ($form['badge'] !== '' && !in_array($form['badge'], $badges, true)) {
        $error = 'Selo inválido.';
    } else {
        if ($id) {
            update_item($id, $form);
            header('Location: /admin/?ok=updated');
        } else {
            create_item($form);
            header('Location: /admin/?ok=created');
        }
        exit;
    }
}

admin_header($id ? 'Editar item' : 'Novo item');
?>
<div class="admin-head">
    <h1><?= $id ? 'Editar item' : 'Novo item' ?></h1>
    <a class="btn btn--ghost" href="/admin/">← Voltar</a>
</div>

<?php if ($error): ?>
    <div class="alert alert--error"><?= e($error) ?></div>
<?php endif; ?>

<form class="form" method="post"
      action="/admin/item-edit.php<?= $id ? '?id=' . $id : '' ?>">
    <?= csrf_field() ?>

    <div class="field">
        <label for="nome">Nome</label>
        <input id="nome" name="nome" type="text" required maxlength="120"
               value="<?= e($form['nome']) ?>">
    </div>

    <div class="field">
        <label for="descricao">Descrição</label>
        <textarea id="descricao" name="descricao" maxlength="400"><?= e($form['descricao']) ?></textarea>
        <p class="field__hint">Ex.: Blend 180g smashado · queijo americano duplo · pickles</p>
    </div>

    <div class="field-row">
        <div class="field">
            <label for="categoria">Categoria</label>
            <select id="categoria" name="categoria">
                <?php foreach ($cats as $slug => $label): ?>
                    <option value="<?= e($slug) ?>" <?= $form['categoria'] === $slug ? 'selected' : '' ?>>
                        <?= e($label) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="field">
            <label for="preco">Preço</label>
            <input id="preco" name="preco" type="text" required inputmode="decimal"
                   placeholder="32,00" value="<?= e($form['preco']) ?>">
            <p class="field__hint">Digite só o valor (ex.: 32,00). O "R$" é adicionado sozinho.</p>
        </div>
    </div>

    <div class="field-row">
        <div class="field">
            <label for="badge">Selo (opcional)</label>
            <select id="badge" name="badge">
                <option value="">— Sem selo —</option>
                <?php foreach ($badges as $b): ?>
                    <option value="<?= e($b) ?>" <?= $form['badge'] === $b ? 'selected' : '' ?>>
                        <?= e($b) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="field">
            <label for="ordem">Ordem de exibição</label>
            <input id="ordem" name="ordem" type="number" min="0" step="1"
                   value="<?= (int) $form['ordem'] ?>">
            <p class="field__hint">Menor número aparece primeiro.</p>
        </div>
    </div>

    <div class="field check">
        <input id="ativo" name="ativo" type="checkbox" value="1" <?= $form['ativo'] ? 'checked' : '' ?>>
        <label for="ativo">Item visível no site</label>
    </div>

    <div class="form-actions">
        <button class="btn btn--primary" type="submit">
            <?= $id ? 'Salvar alterações' : 'Adicionar item' ?>
        </button>
        <a class="btn btn--ghost" href="/admin/">Cancelar</a>
    </div>
</form>

<?php admin_footer(); ?>
