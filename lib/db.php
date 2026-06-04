<?php
/**
 * Camada de banco de dados — SQLite (PDO)
 * Di Bútcher Premium Burger
 *
 * SQLite = arquivo único, sem servidor externo, embutido no PHP.
 * O arquivo do banco fica em /data (protegido por .htaccess).
 *
 * Migrações são idempotentes: rodam a cada conexão, mas só aplicam o
 * que falta. Seeds de cardápio/categorias só ocorrem se as tabelas
 * estiverem vazias; settings preenchem apenas as chaves ausentes.
 */

declare(strict_types=1);

require_once __DIR__ . '/../config.php';

if (!defined('DB_PATH')) {
    define('DB_PATH', __DIR__ . '/../data/dibutcher.sqlite');
}

/**
 * Conexão PDO singleton com o SQLite.
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

    $pdo = new PDO('sqlite:' . DB_PATH, null, null, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);

    $pdo->exec('PRAGMA journal_mode = WAL;');
    $pdo->exec('PRAGMA foreign_keys = ON;');

    db_migrate($pdo);
    db_seed($pdo);

    return $pdo;
}

/**
 * Cria tabelas e colunas que ainda não existem.
 */
function db_migrate(PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS categories (
            id         INTEGER PRIMARY KEY AUTOINCREMENT,
            slug       TEXT    NOT NULL UNIQUE,
            nome       TEXT    NOT NULL,
            ordem      INTEGER NOT NULL DEFAULT 0,
            created_at TEXT    NOT NULL DEFAULT (datetime('now'))
        );
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS menu_items (
            id             INTEGER PRIMARY KEY AUTOINCREMENT,
            categoria      TEXT    NOT NULL,
            nome           TEXT    NOT NULL,
            descricao      TEXT    NOT NULL DEFAULT '',
            preco_centavos INTEGER NOT NULL DEFAULT 0,
            badge          TEXT,
            imagem         TEXT,
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

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS settings (
            key   TEXT PRIMARY KEY,
            value TEXT NOT NULL DEFAULT ''
        );
    ");

    // Coluna de imagem (para bancos criados antes desta versão)
    db_ensure_column($pdo, 'menu_items', 'imagem', 'TEXT');

    // Colunas de 2FA (TOTP) no usuário admin
    db_ensure_column($pdo, 'admin_users', 'totp_secret', 'TEXT');
    db_ensure_column($pdo, 'admin_users', 'totp_enabled', 'INTEGER NOT NULL DEFAULT 0');
}

/**
 * Adiciona uma coluna caso ainda não exista.
 */
function db_ensure_column(PDO $pdo, string $table, string $column, string $definition): void
{
    $cols = $pdo->query("PRAGMA table_info({$table})")->fetchAll();
    foreach ($cols as $c) {
        if ($c['name'] === $column) {
            return;
        }
    }
    $pdo->exec("ALTER TABLE {$table} ADD COLUMN {$column} {$definition}");
}

/**
 * Popula dados iniciais (idempotente).
 */
function db_seed(PDO $pdo): void
{
    // Categorias — apenas se vazio
    $hasCats = (int) $pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn() > 0;
    if (!$hasCats) {
        $defaults = [
            ['smashes', 'Smashes', 0],
            ['acompanhamentos', 'Acompanhamentos', 1],
            ['bebidas', 'Bebidas', 2],
        ];
        $stmt = $pdo->prepare('INSERT INTO categories (slug, nome, ordem) VALUES (?, ?, ?)');
        foreach ($defaults as $row) {
            $stmt->execute($row);
        }
    }

    // Cardápio — apenas se vazio
    $hasItems = (int) $pdo->query('SELECT COUNT(*) FROM menu_items')->fetchColumn() > 0;
    if (!$hasItems && !empty($GLOBALS['menu'])) {
        $stmt = $pdo->prepare(
            'INSERT INTO menu_items (categoria, nome, descricao, preco_centavos, badge, ordem)
             VALUES (:categoria, :nome, :descricao, :preco, :badge, :ordem)'
        );
        foreach ($GLOBALS['menu'] as $categoria => $itens) {
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

    // Settings — garante que cada chave padrão exista (sem sobrescrever)
    settings_ensure_defaults($pdo);
}

/**
 * Valores padrão das configurações (puxa dos constantes do config.php).
 * Definido aqui (e não em settings.php) para evitar dependência circular
 * durante o seed do banco.
 */
function settings_defaults(): array
{
    return [
        // Marca
        'brand_name'        => defined('BRAND_NAME') ? BRAND_NAME : 'Di Bútcher',
        'brand_full_name'   => defined('BRAND_FULL_NAME') ? BRAND_FULL_NAME : 'Di Bútcher Premium Burger',
        'slogan_main'       => defined('BRAND_SLOGAN_MAIN') ? BRAND_SLOGAN_MAIN : '',
        'promise'           => defined('BRAND_PROMISE') ? BRAND_PROMISE : '',

        // Contato / canais
        'whatsapp_number'   => defined('WHATSAPP_NUMBER') ? WHATSAPP_NUMBER : '',
        'whatsapp_display'  => defined('WHATSAPP_DISPLAY') ? WHATSAPP_DISPLAY : '',
        'phone'             => '',
        'email'             => '',
        'ifood_url'         => defined('IFOOD_URL') ? IFOOD_URL : '',
        'instagram_url'     => defined('INSTAGRAM_URL') ? INSTAGRAM_URL : '',
        'tiktok_url'        => defined('TIKTOK_URL') ? TIKTOK_URL : '',

        // Empresa
        'cnpj'              => '',
        'razao_social'      => '',

        // Endereço
        'address_street'    => '',
        'address_district'  => '',
        'address_city'      => defined('BRAND_CITY') ? BRAND_CITY : 'São Luís',
        'address_state'     => defined('BRAND_STATE') ? BRAND_STATE : 'MA',
        'address_cep'       => '',
        'maps_url'          => '',

        // Operação / delivery
        'hours_week'        => defined('HORARIO_SEMANA') ? HORARIO_SEMANA : '',
        'hours_weekend'     => defined('HORARIO_FIM_DE_SEMANA') ? HORARIO_FIM_DE_SEMANA : '',
        'delivery_area'     => defined('DELIVERY_AREA') ? DELIVERY_AREA : '',
        'delivery_fee'      => '0',
        'min_order'         => '0',
        'enable_cart'       => '1',

        // SEO
        'seo_title'         => defined('SEO_TITLE') ? SEO_TITLE : '',
        'seo_description'   => defined('SEO_DESCRIPTION') ? SEO_DESCRIPTION : '',
        'site_url'          => defined('SITE_URL') ? SITE_URL : '',
    ];
}

/**
 * Insere chaves padrão ausentes (não sobrescreve valores existentes).
 */
function settings_ensure_defaults(PDO $pdo): void
{
    $stmt = $pdo->prepare('INSERT OR IGNORE INTO settings (key, value) VALUES (:k, :v)');
    foreach (settings_defaults() as $k => $v) {
        $stmt->execute([':k' => $k, ':v' => (string) $v]);
    }
}

/* ─────────────────────────────────────────────────────────────
 * Helpers de preço (centavos <-> R$ X,XX)
 * ───────────────────────────────────────────────────────────── */

function price_to_cents(string $input): int
{
    $clean = preg_replace('/[^\d,.]/', '', $input);
    if ($clean === '' || $clean === null) {
        return 0;
    }
    $clean = str_replace('.', ',', $clean);
    $parts = explode(',', $clean);
    if (count($parts) === 1) {
        return max(0, ((int) $parts[0]) * 100);
    }
    $dec = array_pop($parts);
    $int = implode('', $parts);
    $dec = str_pad(substr($dec, 0, 2), 2, '0');
    return max(0, ((int) $int) * 100 + (int) $dec);
}

function format_price(int $cents): string
{
    return 'R$ ' . number_format($cents / 100, 2, ',', '.');
}
