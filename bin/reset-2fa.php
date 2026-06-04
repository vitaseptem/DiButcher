<?php
/**
 * Recuperação de 2FA — uso EXCLUSIVO via linha de comando (SSH/terminal).
 *
 * Desativa a verificação em duas etapas de todos os admins, para o caso
 * de perda do celular E da chave de configuração. Depois de rodar, faça
 * login só com a senha e reative o 2FA em "Minha conta".
 *
 * Uso:
 *   php bin/reset-2fa.php
 *
 * Por segurança, recusa execução via navegador/web.
 */

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("Este script só pode ser executado via linha de comando.\n");
}

require_once __DIR__ . '/../lib/db.php';

$n = (int) db()->query('SELECT COUNT(*) FROM admin_users WHERE totp_enabled = 1')->fetchColumn();
db()->exec('UPDATE admin_users SET totp_enabled = 0, totp_secret = NULL');

echo "2FA desativado para {$n} usuário(s).\n";
echo "Agora entre apenas com a senha e reative o 2FA em Minha conta.\n";
