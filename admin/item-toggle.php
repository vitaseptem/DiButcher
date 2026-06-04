<?php
/**
 * Alterna a visibilidade de um item (POST + CSRF).
 */
declare(strict_types=1);

require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/menu.php';

auth_boot();
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin/');
    exit;
}

csrf_check();

$id = (int) ($_POST['id'] ?? 0);
if ($id > 0) {
    toggle_item($id);
}

header('Location: /admin/?ok=toggled');
exit;
