<?php
/**
 * Camada de acesso ao cardápio (menu_items)
 * Di Bútcher Premium Burger
 */

declare(strict_types=1);

require_once __DIR__ . '/db.php';

/**
 * Categorias válidas (slug => rótulo exibido).
 */
function menu_categories(): array
{
    return [
        'smashes'         => 'Smashes',
        'acompanhamentos' => 'Acompanhamentos',
        'bebidas'         => 'Bebidas',
    ];
}

/**
 * Badges disponíveis para seleção no admin.
 */
function menu_badges(): array
{
    return ['MAIS PEDIDO', 'PREMIUM', 'SIGNATURE', 'FAVORITO'];
}

/**
 * Cardápio agrupado por categoria, no formato esperado pelo site público:
 *   ['smashes' => [ ['nome','descricao','preco','badge'], ... ], ...]
 *
 * @param bool $onlyActive Retorna apenas itens ativos (site público).
 */
function get_menu_grouped(bool $onlyActive = true): array
{
    $sql = 'SELECT categoria, nome, descricao, preco_centavos, badge
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
            'nome'      => $row['nome'],
            'descricao' => $row['descricao'],
            'preco'     => format_price((int) $row['preco_centavos']),
            'badge'     => $row['badge'],
        ];
    }

    return $grouped;
}

/**
 * Todos os itens (inclui inativos) — para o painel admin.
 */
function get_all_items(): array
{
    return db()->query(
        'SELECT * FROM menu_items ORDER BY categoria ASC, ordem ASC, id ASC'
    )->fetchAll();
}

/**
 * Um item por ID.
 */
function get_item(int $id): ?array
{
    $stmt = db()->prepare('SELECT * FROM menu_items WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

/**
 * Cria um item. Retorna o ID inserido.
 *
 * @param array{categoria:string,nome:string,descricao:string,preco:string,badge:?string,ordem?:int,ativo?:int} $data
 */
function create_item(array $data): int
{
    $stmt = db()->prepare(
        'INSERT INTO menu_items (categoria, nome, descricao, preco_centavos, badge, ordem, ativo)
         VALUES (:categoria, :nome, :descricao, :preco, :badge, :ordem, :ativo)'
    );
    $stmt->execute([
        ':categoria' => $data['categoria'],
        ':nome'      => $data['nome'],
        ':descricao' => $data['descricao'],
        ':preco'     => price_to_cents($data['preco']),
        ':badge'     => $data['badge'] ?: null,
        ':ordem'     => (int) ($data['ordem'] ?? 0),
        ':ativo'     => (int) ($data['ativo'] ?? 1),
    ]);
    return (int) db()->lastInsertId();
}

/**
 * Atualiza um item existente.
 */
function update_item(int $id, array $data): void
{
    $stmt = db()->prepare(
        "UPDATE menu_items
         SET categoria = :categoria, nome = :nome, descricao = :descricao,
             preco_centavos = :preco, badge = :badge, ordem = :ordem, ativo = :ativo,
             updated_at = datetime('now')
         WHERE id = :id"
    );
    $stmt->execute([
        ':categoria' => $data['categoria'],
        ':nome'      => $data['nome'],
        ':descricao' => $data['descricao'],
        ':preco'     => price_to_cents($data['preco']),
        ':badge'     => $data['badge'] ?: null,
        ':ordem'     => (int) ($data['ordem'] ?? 0),
        ':ativo'     => (int) ($data['ativo'] ?? 1),
        ':id'        => $id,
    ]);
}

/**
 * Remove um item.
 */
function delete_item(int $id): void
{
    $stmt = db()->prepare('DELETE FROM menu_items WHERE id = :id');
    $stmt->execute([':id' => $id]);
}

/**
 * Alterna o status ativo/inativo de um item.
 */
function toggle_item(int $id): void
{
    $stmt = db()->prepare(
        "UPDATE menu_items SET ativo = CASE ativo WHEN 1 THEN 0 ELSE 1 END,
         updated_at = datetime('now') WHERE id = :id"
    );
    $stmt->execute([':id' => $id]);
}
