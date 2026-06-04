<?php
/**
 * Remove um item do cardápio (POST + CSRF) e sua imagem.
 */
declare(strict_types=1);

require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/menu.php';
require_once __DIR__ . '/../lib/upload.php';

auth_boot();
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin/');
    exit;
}

csrf_check();

$id = (int) ($_POST['id'] ?? 0);
if ($id > 0) {
    $item = get_item($id);
    if ($item) {
        delete_item($id);
        if (!empty($item['imagem'])) {
            delete_image($item['imagem']);
        }
    }
}

header('Location: /admin/?ok=deleted');
exit;
