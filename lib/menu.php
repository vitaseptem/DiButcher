<?php
/**
 * Camada de acesso ao cardápio (menu_items).
 */

declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/categories.php';

/**
 * Categorias válidas (slug => rótulo). Lê da tabela categories.
 */
function menu_categories(): array
{
    return categories_map();
}

/**
 * Badges disponíveis para seleção no admin.
 */
function menu_badges(): array
{
    return ['MAIS PEDIDO', 'PREMIUM', 'SIGNATURE', 'FAVORITO'];
}

/**
 * Cardápio agrupado por categoria, no formato do site público:
 *   ['smashes' => [ ['id','nome','descricao','preco','preco_centavos','badge','imagem'], ... ]]
 */
function get_menu_grouped(bool $onlyActive = true): array
{
    $sql = 'SELECT id, categoria, nome, descricao, preco_centavos, badge, imagem
            FROM menu_items'
        . ($onlyActive ? ' WHERE ativo = 1' : '')
        . ' ORDER BY ordem ASC, id ASC';

    $rows = db()->query($sql)->fetchAll();

    $grouped = [];
    foreach (array_keys(menu_categories()) as $slug) {
        $grouped[$slug] = [];
    }

    foreach ($rows as $row) {
        $cat = $row['categoria'];
        if (!isset($grouped[$cat])) {
            $grouped[$cat] = [];
        }
        $grouped[$cat][] = [
            'id'             => (int) $row['id'],
            'nome'           => $row['nome'],
            'descricao'      => $row['descricao'],
            'preco'          => format_price((int) $row['preco_centavos']),
            'preco_centavos' => (int) $row['preco_centavos'],
            'badge'          => $row['badge'],
            'imagem'         => $row['imagem'],
        ];
    }

    return $grouped;
}

/**
 * Todos os itens (inclui inativos) — admin.
 */
function get_all_items(): array
{
    return db()->query(
        'SELECT * FROM menu_items ORDER BY categoria ASC, ordem ASC, id ASC'
    )->fetchAll();
}

function get_item(int $id): ?array
{
    $stmt = db()->prepare('SELECT * FROM menu_items WHERE id = :id');
    $stmt->execute([':id' => $id]);
    return $stmt->fetch() ?: null;
}

function create_item(array $data): int
{
    $stmt = db()->prepare(
        'INSERT INTO menu_items (categoria, nome, descricao, preco_centavos, badge, imagem, ordem, ativo)
         VALUES (:categoria, :nome, :descricao, :preco, :badge, :imagem, :ordem, :ativo)'
    );
    $stmt->execute([
        ':categoria' => $data['categoria'],
        ':nome'      => $data['nome'],
        ':descricao' => $data['descricao'],
        ':preco'     => price_to_cents((string) $data['preco']),
        ':badge'     => ($data['badge'] ?? '') ?: null,
        ':imagem'    => ($data['imagem'] ?? '') ?: null,
        ':ordem'     => (int) ($data['ordem'] ?? 0),
        ':ativo'     => (int) ($data['ativo'] ?? 1),
    ]);
    return (int) db()->lastInsertId();
}

function update_item(int $id, array $data): void
{
    $stmt = db()->prepare(
        "UPDATE menu_items
         SET categoria = :categoria, nome = :nome, descricao = :descricao,
             preco_centavos = :preco, badge = :badge, imagem = :imagem,
             ordem = :ordem, ativo = :ativo, updated_at = datetime('now')
         WHERE id = :id"
    );
    $stmt->execute([
        ':categoria' => $data['categoria'],
        ':nome'      => $data['nome'],
        ':descricao' => $data['descricao'],
        ':preco'     => price_to_cents((string) $data['preco']),
        ':badge'     => ($data['badge'] ?? '') ?: null,
        ':imagem'    => ($data['imagem'] ?? '') ?: null,
        ':ordem'     => (int) ($data['ordem'] ?? 0),
        ':ativo'     => (int) ($data['ativo'] ?? 1),
        ':id'        => $id,
    ]);
}

function delete_item(int $id): void
{
    $stmt = db()->prepare('DELETE FROM menu_items WHERE id = :id');
    $stmt->execute([':id' => $id]);
}

function toggle_item(int $id): void
{
    $stmt = db()->prepare(
        "UPDATE menu_items SET ativo = CASE ativo WHEN 1 THEN 0 ELSE 1 END,
         updated_at = datetime('now') WHERE id = :id"
    );
    $stmt->execute([':id' => $id]);
}
