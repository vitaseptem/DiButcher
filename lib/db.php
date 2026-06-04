<?php
/**
 * Camada de banco de dados — SQLite (PDO)
 * Di Bútcher Premium Burger
 *
 * SQLite = arquivo único, sem servidor externo, embutido no PHP.
 * O arquivo do banco fica em /data/ (protegido por .htaccess).
 */

declare(strict_types=1);

require_once __DIR__ . '/../config.php';

if (!defined('DB_PATH')) {
    define('DB_PATH', __DIR__ . '/../data/dibutcher.sqlite');
}

/**
 * Conexão PDO singleton com o SQLite.
 * Cria o diretório /data e o schema na primeira execução.
 */
function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dir = dirname(DB_PATH);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $firstRun = !file_exists(DB_PATH);

    $pdo = new PDO('sqlite:' . DB_PATH, null, null, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);

    // Boas práticas para SQLite
    $pdo->exec('PRAGMA journal_mode = WAL;');
    $pdo->exec('PRAGMA foreign_keys = ON;');

    db_migrate($pdo);

    if ($firstRun) {
        db_seed($pdo);
    }

    return $pdo;
}

/**
 * Cria as tabelas caso ainda não existam.
 */
function db_migrate(PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS menu_items (
            id             INTEGER PRIMARY KEY AUTOINCREMENT,
            categoria      TEXT    NOT NULL,
            nome           TEXT    NOT NULL,
            descricao      TEXT    NOT NULL DEFAULT '',
            preco_centavos INTEGER NOT NULL DEFAULT 0,
            badge          TEXT,
            ordem          INTEGER NOT NULL DEFAULT 0,
            ativo          INTEGER NOT NULL DEFAULT 1,
            created_at     TEXT    NOT NULL DEFAULT (datetime('now')),
            updated_at     TEXT    NOT NULL DEFAULT (datetime('now'))
        );
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS admin_users (
            id            INTEGER PRIMARY KEY AUTOINCREMENT,
            username      TEXT    NOT NULL UNIQUE,
            password_hash TEXT    NOT NULL,
            created_at    TEXT    NOT NULL DEFAULT (datetime('now'))
        );
    ");
}

/**
 * Popula o banco com o cardápio inicial vindo do config.php
 * (executa apenas na primeira criação do arquivo).
 */
function db_seed(PDO $pdo): void
{
    $menu = $GLOBALS['menu'] ?? [];
    if (!$menu) {
        return;
    }

    $stmt = $pdo->prepare(
        'INSERT INTO menu_items (categoria, nome, descricao, preco_centavos, badge, ordem)
         VALUES (:categoria, :nome, :descricao, :preco, :badge, :ordem)'
    );

    foreach ($menu as $categoria => $itens) {
        foreach (array_values($itens) as $i => $item) {
            $stmt->execute([
                ':categoria' => $categoria,
                ':nome'      => $item['nome'],
                ':descricao' => $item['descricao'] ?? '',
                ':preco'     => price_to_cents((string) ($item['preco'] ?? '0')),
                ':badge'     => $item['badge'] ?? null,
                ':ordem'     => $i,
            ]);
        }
    }
}

/* ─────────────────────────────────────────────────────────────
 * Helpers de preço (armazenado em centavos, exibido como R$ X,XX)
 * ───────────────────────────────────────────────────────────── */

/**
 * Converte texto de preço ("R$ 32,00", "32,00", "32.00", "32") em centavos.
 */
function price_to_cents(string $input): int
{
    $clean = preg_replace('/[^\d,.]/', '', $input);
    if ($clean === '' || $clean === null) {
        return 0;
    }
    // Normaliza separador decimal: última vírgula/ponto vira decimal
    $clean = str_replace('.', ',', $clean);
    $parts = explode(',', $clean);
    $cents = 0;
    if (count($parts) === 1) {
        $cents = ((int) $parts[0]) * 100;
    } else {
        $dec  = array_pop($parts);
        $int  = implode('', $parts);
        $dec  = str_pad(substr($dec, 0, 2), 2, '0');
        $cents = ((int) $int) * 100 + (int) $dec;
    }
    return max(0, $cents);
}

/**
 * Formata centavos como "R$ 32,00".
 */
function format_price(int $cents): string
{
    return 'R$ ' . number_format($cents / 100, 2, ',', '.');
}
