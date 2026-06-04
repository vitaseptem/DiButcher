<?php
/**
 * Cadastro do 2FA (TOTP) — Google Authenticator / Authy / etc.
 * Mostra a chave de configuração e confirma com um código.
 */
declare(strict_types=1);

require_once __DIR__ . '/partials.php';
require_once __DIR__ . '/../lib/totp.php';

auth_boot();
require_login();

$id = (int) $_SESSION['admin_id'];
$u  = get_admin($id);

// Já tem 2FA ativo → não precisa cadastrar de novo.
if ($u && !empty($u['totp_enabled'])) {
    header('Location: /admin/account.php');
    exit;
}

// Garante um segredo pendente (gera uma vez e reutiliza enquanto não ativar).
if (empty($u['totp_secret'])) {
    set_admin_totp_secret($id, totp_generate_secret());
    $u = get_admin($id);
}
$secret = $u['totp_secret'];

$issuer  = cfg('brand_name', 'Di Bútcher');
$account = $u['username'];
$uri     = totp_uri($secret, $account, $issuer);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    if (isset($_POST['skip'])) {
        header('Location: /admin/?ok=skip2fa');
        exit;
    }
    $code = (string) ($_POST['code'] ?? '');
    if (totp_verify($secret, $code)) {
        enable_admin_totp($id);
        header('Location: /admin/?ok=2fa');
        exit;
    }
    $error = 'Código incorreto. Confira no app e tente novamente (o código muda a cada 30s).';
}

admin_header('Ativar 2FA', '');
?>
<div class="admin-head">
    <h1>Proteja sua conta com 2FA</h1>
</div>

<div class="alert alert--ok" style="margin-bottom:1.5rem;">
    Recomendado: ative a verificação em duas etapas. Assim, mesmo que alguém
    descubra sua senha, não entra sem o código do seu celular.
</div>

<?php if ($error): ?><div class="alert alert--error"><?= e($error) ?></div><?php endif; ?>

<div class="enroll">
    <ol class="enroll__steps">
        <li><strong>Abra o Google Authenticator</strong> no seu celular.</li>
        <li>Toque em <strong>+</strong> → <strong>“Inserir chave de configuração”</strong>.</li>
        <li>Em “Nome da conta” coloque <code><?= e($issuer) ?> (<?= e($account) ?>)</code>.</li>
        <li>Em “Sua chave”, digite a chave abaixo e escolha <strong>“Por tempo”</strong>.</li>
    </ol>

    <div class="enroll__key">
        <span class="enroll__key-label">Sua chave de configuração</span>
        <code class="enroll__key-value"><?= e(totp_format_secret($secret)) ?></code>
    </div>

    <details class="enroll__adv">
        <summary>Ver link otpauth:// (avançado)</summary>
        <code class="enroll__uri"><?= e($uri) ?></code>
    </details>

    <div class="enroll__warn">
        ⚠️ <strong>Guarde esta chave em local seguro.</strong> Com ela você reconfigura
        o app caso troque de celular. Sem ela e sem o celular, será preciso resetar o 2FA no servidor.
    </div>

    <form method="post" action="/admin/enroll.php" class="form" style="margin-top:1.5rem;">
        <?= csrf_field() ?>
        <div class="field">
            <label for="code">Digite o código de 6 dígitos do app para confirmar</label>
            <input id="code" name="code" type="text" inputmode="numeric" autocomplete="one-time-code"
                   pattern="[0-9]*" maxlength="6" required autofocus
                   style="max-width:200px; letter-spacing:.4em; font-size:1.3rem; text-align:center;">
        </div>
        <div class="form-actions">
            <button class="btn btn--primary" type="submit">Ativar 2FA</button>
            <button class="btn btn--ghost" type="submit" name="skip" value="1">Pular por enquanto</button>
        </div>
    </form>
</div>

<?php admin_footer(); ?>
