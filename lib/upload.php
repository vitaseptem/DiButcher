<?php
/**
 * Upload de imagens de produtos.
 * Salva em /assets/uploads com validação de tipo/tamanho e nome seguro.
 */

declare(strict_types=1);

const UPLOAD_DIR     = __DIR__ . '/../assets/uploads';
const UPLOAD_WEB     = '/assets/uploads';
const UPLOAD_MAX     = 4 * 1024 * 1024; // 4 MB
const UPLOAD_ALLOWED = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp',
    'image/gif'  => 'gif',
];

/**
 * Processa um arquivo de $_FILES e retorna o caminho web salvo,
 * ou null se nenhum arquivo foi enviado. Lança Exception em erro.
 *
 * @param array $file Entrada de $_FILES['campo']
 */
function handle_image_upload(array $file): ?string
{
    if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null; // nenhum arquivo enviado
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Falha no envio da imagem (código ' . $file['error'] . ').');
    }
    if ($file['size'] > UPLOAD_MAX) {
        throw new RuntimeException('Imagem muito grande (máx. 4 MB).');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($file['tmp_name']);

    if (!isset(UPLOAD_ALLOWED[$mime])) {
        throw new RuntimeException('Formato inválido. Use JPG, PNG, WEBP ou GIF.');
    }
    if (!getimagesize($file['tmp_name'])) {
        throw new RuntimeException('O arquivo não é uma imagem válida.');
    }

    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }

    $ext  = UPLOAD_ALLOWED[$mime];
    $name = bin2hex(random_bytes(8)) . '_' . time() . '.' . $ext;
    $dest = UPLOAD_DIR . '/' . $name;

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        // Fallback para ambiente de teste (built-in server)
        if (!@rename($file['tmp_name'], $dest)) {
            throw new RuntimeException('Não foi possível salvar a imagem.');
        }
    }
    @chmod($dest, 0644);

    return UPLOAD_WEB . '/' . $name;
}

/**
 * Remove um arquivo de imagem previamente salvo (se for do diretório de uploads).
 */
function delete_image(?string $webPath): void
{
    if (!$webPath || !str_starts_with($webPath, UPLOAD_WEB . '/')) {
        return;
    }
    $name = basename($webPath);
    $full = UPLOAD_DIR . '/' . $name;
    if (is_file($full)) {
        @unlink($full);
    }
}
