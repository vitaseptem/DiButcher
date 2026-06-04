<?php
/**
 * Footer — Di Bútcher Premium Burger
 */
if (!defined('BRAND_NAME')) {
    require_once __DIR__ . '/../config.php';
}
?>
<footer class="site-footer">
    <div class="container">
        <img class="footer__logo" src="/assets/img/logo.png"
             alt="<?= htmlspecialchars(BRAND_FULL_NAME, ENT_QUOTES, 'UTF-8') ?>"
             width="72" height="72" loading="lazy">

        <p class="footer__brand">Di <span class="accent">Bútcher</span></p>
        <p class="footer__geo"><?= htmlspecialchars(BRAND_TAGLINE_GEO, ENT_QUOTES, 'UTF-8') ?></p>

        <ul class="footer__socials">
            <li>
                <a class="footer__social" href="<?= htmlspecialchars(INSTAGRAM_URL, ENT_QUOTES, 'UTF-8') ?>"
                   target="_blank" rel="noopener" aria-label="Instagram da Di Bútcher">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                        <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                        <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                    </svg>
                </a>
            </li>
            <li>
                <a class="footer__social" href="<?= htmlspecialchars(TIKTOK_URL, ENT_QUOTES, 'UTF-8') ?>"
                   target="_blank" rel="noopener" aria-label="TikTok da Di Bútcher">
                    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M16.6 5.82A4.28 4.28 0 0 1 15.54 3h-3.09v12.4a2.59 2.59 0 0 1-2.59 2.5 2.59 2.59 0 0 1 0-5.18c.27 0 .53.04.77.12v-3.2a5.78 5.78 0 0 0-.77-.05A5.78 5.78 0 1 0 15.64 15.4V9.01a7.5 7.5 0 0 0 4.36 1.39V7.31a4.28 4.28 0 0 1-3.4-1.49z"/>
                    </svg>
                </a>
            </li>
            <li>
                <a class="footer__social" href="<?= htmlspecialchars(whatsapp_link(), ENT_QUOTES, 'UTF-8') ?>"
                   target="_blank" rel="noopener" aria-label="WhatsApp da Di Bútcher">
                    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38a9.9 9.9 0 0 0 4.79 1.22h.01c5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2zm5.8 14.16c-.24.68-1.42 1.31-1.96 1.36-.5.05-.97.24-3.27-.68-2.75-1.08-4.52-3.86-4.66-4.04-.14-.18-1.12-1.49-1.12-2.84 0-1.35.71-2.01.96-2.29.24-.27.53-.34.71-.34l.51.01c.16.01.38-.06.6.46.24.55.81 1.9.88 2.04.07.14.12.3.02.48-.09.18-.14.3-.27.46-.14.16-.29.36-.41.48-.14.14-.28.29-.12.57.16.27.71 1.17 1.53 1.9 1.05.94 1.94 1.23 2.21 1.37.27.14.43.12.59-.07.16-.18.68-.79.86-1.06.18-.27.36-.23.6-.14.25.09 1.58.75 1.85.89.27.14.45.2.52.32.07.11.07.66-.17 1.34z"/>
                    </svg>
                </a>
            </li>
        </ul>

        <p class="footer__slogan"><?= htmlspecialchars(BRAND_SLOGAN_MAIN, ENT_QUOTES, 'UTF-8') ?></p>

        <p class="footer__copy">
            &copy; <?= date('Y') ?> <?= htmlspecialchars(BRAND_FULL_NAME, ENT_QUOTES, 'UTF-8') ?>.
            Todos os direitos reservados.
        </p>
    </div>
</footer>
