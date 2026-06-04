<?php
/**
 * Minha conta — trocar senha e gerenciar 2FA.
 */
declare(strict_types=1);

require_once __DIR__ . '/partials.php';

auth_boot();
require_login();

$id = (int) $_SESSION['admin_id'];
$ok = $_GET['ok'] ?? '';
$pwError = '';
$twoError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = $_POST['action'] ?? '';

    if ($action === 'password') {
        $cur  = (string) ($_POST['current'] ?? '');
        $new  = (string) ($_POST['new'] ?? '');
        $new2 = (string) ($_POST['new2'] ?? '');
        $me   = get_admin($id);

        if (!$me || !password_verify($cur, $me['password_hash'])) {
            $pwError = 'Senha atual incorreta.';
        } elseif (strlen($new) < 8) {
            $pwError = 'A nova senha precisa ter pelo menos 8 caracteres.';
        } elseif ($new !== $new2) {
            $pwError = 'A confirmação não confere.';
        } else {
            change_admin_password($id, $new);
            header('Location: /admin/account.php?ok=pw');
            exit;
        }
    } elseif ($action === 'disable_2fa') {
        $me = get_admin($id);
        // Exige um código válido para desativar (evita desativação indevida)
        if ($me && !empty($me['totp_secret']) && totp_verify($me['totp_secret'], (string) ($_POST['code'] ?? ''))) {
            disable_admin_totp($id);
            header('Location: /admin/account.php?ok=2faoff');
            exit;
        }
        $twoError = 'Código incorreto. O 2FA não foi desativado.';
    }
}

$me   = get_admin($id);
$has2fa = !empty($me['totp_enabled']);

admin_header('Minha conta', 'conta');
?>
<div class="admin-head">
    <h1>Minha conta</h1>
</div>

<?php if ($ok): ?>
    <div class="alert alert--ok"><?= match ($ok) {
        'pw'     => 'Senha alterada com sucesso.',
        '2faoff' => 'Verificação em duas etapas desativada.',
        default  => 'Pronto.',
    } ?></div>
<?php endif; ?>

<!-- 2FA -->
<div class="settings-group">
    <legend style="display:inline-block;padding:0 .4rem;">Verificação em duas etapas (2FA)</legend>
    <?php if ($has2fa): ?>
        <p style="color:var(--c-gold);margin:.5rem 0 1rem;">✓ Ativada — seu login pede o código do app.</p>
        <?php if ($twoError): ?><div class="alert alert--error"><?= e($twoError) ?></div><?php endif; ?>
        <form method="post" class="inline-form">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="disable_2fa">
            <input type="text" name="code" inputmode="numeric" pattern="[0-9]*" maxlength="6"
                   placeholder="Código do app" required style="max-width:160px;letter-spacing:.2em;text-align:center;">
            <button class="btn btn--danger" type="submit">Desativar 2FA</button>
        </form>
        <p class="field__hint" style="margin-top:.6rem;">Para reconfigurar (novo celular), desative e ative novamente.</p>
    <?php else: ?>
        <p style="color:var(--c-muted);margin:.5rem 0 1rem;">Não está ativa. Recomendamos ativar.</p>
        <a class="btn btn--primary" href="/admin/enroll.php">Ativar 2FA</a>
    <?php endif; ?>
</div>

<!-- Senha -->
<div class="settings-group">
    <legend style="display:inline-block;padding:0 .4rem;">Trocar senha</legend>
    <?php if ($pwError): ?><div class="alert alert--error"><?= e($pwError) ?></div><?php endif; ?>
    <form method="post" class="form" style="margin-top:.5rem;">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="password">
        <div class="field">
            <label for="current">Senha atual</label>
            <input id="current" name="current" type="password" required autocomplete="current-password">
        </div>
        <div class="field-row">
            <div class="field">
                <label for="new">Nova senha (mín. 8)</label>
                <input id="new" name="new" type="password" required minlength="8" autocomplete="new-password">
            </div>
            <div class="field">
                <label for="new2">Confirmar nova senha</label>
                <input id="new2" name="new2" type="password" required minlength="8" autocomplete="new-password">
            </div>
        </div>
        <div class="form-actions">
            <button class="btn btn--primary" type="submit">Salvar nova senha</button>
        </div>
    </form>
</div>

<?php admin_footer(); ?>
