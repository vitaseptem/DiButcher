/* ════════════════════════════════════════════════════════════════
   DI BÚTCHER — app.js · navegação, header e tabs (vanilla, sem deps)
   ════════════════════════════════════════════════════════════════ */
(function () {
    'use strict';

    /* Header sticky */
    var header = document.querySelector('.site-header');
    var ticking = false;
    function onScroll() {
        if (ticking) return;
        ticking = true;
        requestAnimationFrame(function () {
            if (header) header.classList.toggle('scrolled', window.scrollY > 40);
            ticking = false;
        });
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    /* Menu mobile */
    var toggle = document.querySelector('.nav__toggle');
    var drawer = document.querySelector('.nav__drawer');
    function closeNav() {
        if (!toggle || !drawer) return;
        toggle.setAttribute('aria-expanded', 'false');
        drawer.classList.remove('is-open');
    }
    if (toggle && drawer) {
        toggle.addEventListener('click', function () {
            var open = toggle.getAttribute('aria-expanded') === 'true';
            toggle.setAttribute('aria-expanded', String(!open));
            drawer.classList.toggle('is-open', !open);
        });
        drawer.querySelectorAll('a').forEach(function (a) {
            a.addEventListener('click', closeNav);
        });
    }

    /* Smooth scroll para âncoras */
    document.querySelectorAll('a[href^="#"]').forEach(function (a) {
        a.addEventListener('click', function (e) {
            var id = a.getAttribute('href');
            if (id.length < 2) return;
            var t = document.querySelector(id);
            if (!t) return;
            e.preventDefault();
            t.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });

    /* Tabs do cardápio */
    var tabs = document.querySelectorAll('.menu-tab');
    var cats = document.querySelectorAll('.menu-category');
    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            var filter = tab.dataset.filter;
            tabs.forEach(function (t) { t.classList.toggle('is-active', t === tab); });
            cats.forEach(function (c) {
                c.hidden = !(filter === 'all' || c.dataset.category === filter);
            });
        });
    });
})();
