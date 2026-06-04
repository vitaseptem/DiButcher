<?php
/**
 * TOTP (RFC 6238) — autenticação de dois fatores compatível com
 * Google Authenticator / Authy / Microsoft Authenticator.
 *
 * PHP puro, sem dependências. Algoritmo: HMAC-SHA1, 6 dígitos, janela 30s.
 */

declare(strict_types=1);

/**
 * Gera um segredo aleatório em Base32 (sem padding).
 */
function totp_generate_secret(int $bytes = 20): string
{
    return base32_encode(random_bytes($bytes));
}

/**
 * Monta o URI otpauth:// (usado por QR codes e pela entrada manual).
 */
function totp_uri(string $secret, string $account, string $issuer): string
{
    $label = rawurlencode($issuer . ':' . $account);
    $params = http_build_query([
        'secret'    => $secret,
        'issuer'    => $issuer,
        'algorithm' => 'SHA1',
        'digits'    => 6,
        'period'    => 30,
    ]);
    return 'otpauth://totp/' . $label . '?' . $params;
}

/**
 * Calcula o código TOTP de 6 dígitos para um dado instante.
 */
function totp_code(string $secret, ?int $timestamp = null, int $period = 30): string
{
    $timestamp = $timestamp ?? time();
    $counter   = intdiv($timestamp, $period);

    $key = base32_decode($secret);
    // Contador em 8 bytes big-endian
    $binCounter = pack('N*', 0) . pack('N*', $counter);

    $hash = hash_hmac('sha1', $binCounter, $key, true);
    $offset = ord($hash[strlen($hash) - 1]) & 0x0F;

    $part = (ord($hash[$offset]) & 0x7F) << 24
          | (ord($hash[$offset + 1]) & 0xFF) << 16
          | (ord($hash[$offset + 2]) & 0xFF) << 8
          | (ord($hash[$offset + 3]) & 0xFF);

    $code = $part % 1000000;
    return str_pad((string) $code, 6, '0', STR_PAD_LEFT);
}

/**
 * Verifica um código informado, tolerando deriva de relógio (±$window períodos).
 */
function totp_verify(string $secret, string $code, int $window = 1, int $period = 30): bool
{
    $code = preg_replace('/\D/', '', $code);
    if (strlen($code) !== 6) {
        return false;
    }
    $now = time();
    for ($i = -$window; $i <= $window; $i++) {
        $candidate = totp_code($secret, $now + ($i * $period), $period);
        if (hash_equals($candidate, $code)) {
            return true;
        }
    }
    return false;
}

/* ─────────────────────────────────────────────────────────────
 * Base32 (RFC 4648) — sem padding
 * ───────────────────────────────────────────────────────────── */

function base32_encode(string $data): string
{
    $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $out = '';
    $buffer = 0;
    $bitsLeft = 0;

    for ($i = 0, $len = strlen($data); $i < $len; $i++) {
        $buffer = ($buffer << 8) | ord($data[$i]);
        $bitsLeft += 8;
        while ($bitsLeft >= 5) {
            $bitsLeft -= 5;
            $out .= $alphabet[($buffer >> $bitsLeft) & 0x1F];
        }
    }
    if ($bitsLeft > 0) {
        $out .= $alphabet[($buffer << (5 - $bitsLeft)) & 0x1F];
    }
    return $out;
}

function base32_decode(string $b32): string
{
    $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $map = array_flip(str_split($alphabet));
    $b32 = strtoupper(preg_replace('/[^A-Z2-7]/', '', $b32));

    $out = '';
    $buffer = 0;
    $bitsLeft = 0;

    for ($i = 0, $len = strlen($b32); $i < $len; $i++) {
        $buffer = ($buffer << 5) | $map[$b32[$i]];
        $bitsLeft += 5;
        if ($bitsLeft >= 8) {
            $bitsLeft -= 8;
            $out .= chr(($buffer >> $bitsLeft) & 0xFF);
        }
    }
    return $out;
}

/**
 * Formata o segredo em grupos de 4 para facilitar a digitação manual.
 */
function totp_format_secret(string $secret): string
{
    return trim(chunk_split($secret, 4, ' '));
}
