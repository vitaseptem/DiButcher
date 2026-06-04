/* ════════════════════════════════════════════════════════════════
   DI BÚTCHER PREMIUM BURGER — app.js
   Vanilla JS puro · zero dependências
   ════════════════════════════════════════════════════════════════ */

(function () {
    'use strict';

    /* ───────────────────────────────────────────────
       1. Header sticky — classe .scrolled após 80px
       ─────────────────────────────────────────────── */
    const header = document.querySelector('.site-header');
    const SCROLL_THRESHOLD = 80;
    let ticking = false;

    function onScroll() {
        if (!ticking) {
            window.requestAnimationFrame(function () {
                if (header) {
                    header.classList.toggle('scrolled', window.scrollY > SCROLL_THRESHOLD);
                }
                ticking = false;
            });
            ticking = true;
        }
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    /* ───────────────────────────────────────────────
       2. Menu mobile (hamburguer)
       ─────────────────────────────────────────────── */
    const toggle = document.querySelector('.nav__toggle');
    const drawer = document.querySelector('.nav__drawer');

    function closeDrawer() {
        if (!toggle || !drawer) return;
        toggle.setAttribute('aria-expanded', 'false');
        drawer.classList.remove('is-open');
        document.body.style.removeProperty('overflow');
    }

    if (toggle && drawer) {
        toggle.addEventListener('click', function () {
            const open = toggle.getAttribute('aria-expanded') === 'true';
            toggle.setAttribute('aria-expanded', String(!open));
            drawer.classList.toggle('is-open', !open);
            document.body.style.overflow = !open ? 'hidden' : '';
        });

        // Fecha ao clicar em qualquer link do drawer
        drawer.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', closeDrawer);
        });

        // Fecha com a tecla Escape
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeDrawer();
        });
    }

    /* ───────────────────────────────────────────────
       3. Smooth scroll para âncoras internas
          (fallback p/ navegadores sem scroll-behavior)
       ─────────────────────────────────────────────── */
    document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
        anchor.addEventListener('click', function (e) {
            const id = anchor.getAttribute('href');
            if (id === '#' || id.length < 2) return;
            const target = document.querySelector(id);
            if (!target) return;
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            // Atualiza hash sem salto
            history.pushState(null, '', id);
        });
    });

    /* ───────────────────────────────────────────────
       4. buildWhatsAppLink — gera URL codificada
       ─────────────────────────────────────────────── */
    function buildWhatsAppLink(item, preco) {
        const number = document.body.dataset.waNumber || '';
        const msg = 'Olá! Quero pedir: ' + item + ' - ' + preco;
        return 'https://wa.me/' + number + '?text=' + encodeURIComponent(msg);
    }
    // Exposto para uso/teste externo
    window.buildWhatsAppLink = buildWhatsAppLink;

    /* ───────────────────────────────────────────────
       5. Tabs do cardápio (filtragem de categorias)
       ─────────────────────────────────────────────── */
    const tabs = document.querySelectorAll('.menu-tab');
    const categories = document.querySelectorAll('.menu-category');

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            const filter = tab.dataset.filter;

            tabs.forEach(function (t) {
                const active = t === tab;
                t.classList.toggle('is-active', active);
                t.setAttribute('aria-selected', String(active));
            });

            categories.forEach(function (cat) {
                const show = filter === 'all' || cat.dataset.category === filter;
                cat.hidden = !show;
            });
        });
    });

    /* ───────────────────────────────────────────────
       6. Intersection Observer — fade-in das seções
       ─────────────────────────────────────────────── */
    const revealEls = document.querySelectorAll('.reveal');

    if ('IntersectionObserver' in window && revealEls.length) {
        const io = new IntersectionObserver(function (entries, observer) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -10% 0px' });

        revealEls.forEach(function (el) { io.observe(el); });
    } else {
        // Sem suporte: revela tudo
        revealEls.forEach(function (el) { el.classList.add('is-visible'); });
    }
})();
