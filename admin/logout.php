<?php
/**
 * Logout do painel admin.
 */
declare(strict_types=1);

require_once __DIR__ . '/../lib/auth.php';

auth_boot();
logout();

header('Location: /admin/login.php');
exit;
