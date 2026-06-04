<?php
/**
 * Configurações do site (tabela settings, chave/valor).
 * Tudo que o dono edita pelo admin: marca, contato, CNPJ, endereço,
 * horários, redes, taxa de entrega, SEO, etc.
 */

declare(strict_types=1);

require_once __DIR__ . '/db.php';

/*
 * settings_defaults() e settings_ensure_defaults() vivem em db.php
 * (são usados no seed) para evitar dependência circular.
 */

/**
 * Carrega todas as configurações (com cache estático).
 */
function all_settings(bool $fresh = false): array
{
    static $cache = null;
    if ($cache !== null && !$fresh) {
        return $cache;
    }
    $cache = settings_defaults();
    try {
        $rows = db()->query('SELECT key, value FROM settings')->fetchAll();
        foreach ($rows as $r) {
            $cache[$r['key']] = $r['value'];
        }
    } catch (Throwable $e) {
        // mantém defaults
    }
    return $cache;
}

/**
 * Lê uma configuração (com fallback ao padrão).
 */
function cfg(string $key, string $default = ''): string
{
    $all = all_settings();
    $val = $all[$key] ?? $default;
    return $val === '' && $default !== '' ? $default : (string) $val;
}

/**
 * Grava uma configuração.
 */
function set_setting(string $key, string $value): void
{
    $stmt = db()->prepare(
        'INSERT INTO settings (key, value) VALUES (:k, :v)
         ON CONFLICT(key) DO UPDATE SET value = excluded.value'
    );
    $stmt->execute([':k' => $key, ':v' => $value]);
    all_settings(true); // invalida cache
}

/**
 * Grava várias configurações de uma vez (apenas chaves conhecidas).
 */
function save_settings(array $data): void
{
    $allowed = array_keys(settings_defaults());
    $pdo = db();
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare(
            'INSERT INTO settings (key, value) VALUES (:k, :v)
             ON CONFLICT(key) DO UPDATE SET value = excluded.value'
        );
        foreach ($data as $k => $v) {
            if (in_array($k, $allowed, true)) {
                $stmt->execute([':k' => $k, ':v' => (string) $v]);
            }
        }
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
    all_settings(true);
}

/* ─────────────────────────────────────────────────────────────
 * Helpers derivados
 * ───────────────────────────────────────────────────────────── */

/** Número de WhatsApp só com dígitos. */
function wa_number(): string
{
    return preg_replace('/\D+/', '', cfg('whatsapp_number'));
}

/** Link wa.me com mensagem URL-encoded. */
function wa_link(string $message = ''): string
{
    if ($message === '') {
        $message = 'Olá! Vim pelo site da ' . cfg('brand_name') . ' e quero fazer um pedido. 🔥';
    }
    return 'https://wa.me/' . wa_number() . '?text=' . rawurlencode($message);
}

/** Taxa de entrega em centavos (0 = grátis/não exibe). */
function delivery_fee_cents(): int
{
    return price_to_cents(cfg('delivery_fee', '0'));
}

/** Pedido mínimo em centavos. */
function min_order_cents(): int
{
    return price_to_cents(cfg('min_order', '0'));
}

/** Carrinho habilitado? */
function cart_enabled(): bool
{
    return cfg('enable_cart', '1') === '1';
}
