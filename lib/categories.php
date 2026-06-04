<?php
/**
 * Gestão de categorias do cardápio.
 */

declare(strict_types=1);

require_once __DIR__ . '/db.php';

/**
 * Todas as categorias ordenadas.
 */
function get_categories(): array
{
    return db()->query('SELECT * FROM categories ORDER BY ordem ASC, id ASC')->fetchAll();
}

/**
 * Mapa slug => nome (formato usado pelo site/menu).
 */
function categories_map(): array
{
    $map = [];
    foreach (get_categories() as $c) {
        $map[$c['slug']] = $c['nome'];
    }
    return $map;
}

function get_category(int $id): ?array
{
    $stmt = db()->prepare('SELECT * FROM categories WHERE id = :id');
    $stmt->execute([':id' => $id]);
    return $stmt->fetch() ?: null;
}

/**
 * Gera um slug seguro a partir do nome.
 */
function slugify(string $text): string
{
    $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text) ?: $text;
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim((string) $text, '-') ?: 'cat-' . substr(md5($text . microtime()), 0, 6);
}

/**
 * Cria categoria. Retorna o ID.
 */
function create_category(string $nome, int $ordem = 0): int
{
    $slug = slugify($nome);
    // Garante unicidade do slug
    $base = $slug;
    $n = 2;
    while (category_slug_exists($slug)) {
        $slug = $base . '-' . $n++;
    }
    $stmt = db()->prepare('INSERT INTO categories (slug, nome, ordem) VALUES (:s, :n, :o)');
    $stmt->execute([':s' => $slug, ':n' => $nome, ':o' => $ordem]);
    return (int) db()->lastInsertId();
}

function category_slug_exists(string $slug): bool
{
    $stmt = db()->prepare('SELECT 1 FROM categories WHERE slug = :s');
    $stmt->execute([':s' => $slug]);
    return (bool) $stmt->fetchColumn();
}

/**
 * Atualiza nome/ordem (o slug é mantido para não quebrar referências).
 */
function update_category(int $id, string $nome, int $ordem): void
{
    $stmt = db()->prepare('UPDATE categories SET nome = :n, ordem = :o WHERE id = :id');
    $stmt->execute([':n' => $nome, ':o' => $ordem, ':id' => $id]);
}

/**
 * Quantos itens usam esta categoria (por slug).
 */
function category_item_count(string $slug): int
{
    $stmt = db()->prepare('SELECT COUNT(*) FROM menu_items WHERE categoria = :s');
    $stmt->execute([':s' => $slug]);
    return (int) $stmt->fetchColumn();
}

/**
 * Remove categoria (bloqueada se houver itens vinculados).
 */
function delete_category(int $id): bool
{
    $cat = get_category($id);
    if (!$cat) {
        return false;
    }
    if (category_item_count($cat['slug']) > 0) {
        return false; // bloqueia para não deixar itens órfãos
    }
    $stmt = db()->prepare('DELETE FROM categories WHERE id = :id');
    $stmt->execute([':id' => $id]);
    return true;
}
