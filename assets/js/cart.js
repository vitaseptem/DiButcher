/* ════════════════════════════════════════════════════════════════
   DI BÚTCHER — cart.js
   Carrinho 100% client-side (localStorage). No checkout, monta a
   mensagem e abre o WhatsApp da loja. Nenhum dado do cliente é salvo.
   ════════════════════════════════════════════════════════════════ */
(function () {
    'use strict';

    var CFG = window.DIBUTCHER || {};
    if (CFG.cartEnabled === false) return;

    var KEY = 'dibutcher_cart_v2';

    // Placeholder (cutelos) para itens sem foto
    var PLACEHOLDER = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 80 80'%3E%3Crect width='80' height='80' fill='%231f1f1f'/%3E%3Cpath d='M40 18l4 4-10 10 3 3 10-10c2.5-2.5 2.5-6.5 0-9s-6.5-2.5-9 0L28 26l12 12-3 3-15-15 4-4z' fill='%23C9A84C' opacity='.5'/%3E%3C/svg%3E";

    /* ---------- estado ---------- */
    function load() {
        try { return JSON.parse(localStorage.getItem(KEY)) || []; }
        catch (e) { return []; }
    }
    function save(items) {
        localStorage.setItem(KEY, JSON.stringify(items));
        render();
    }
    var cart = load();

    function find(id) { return cart.find(function (i) { return i.id === id; }); }
    function count() { return cart.reduce(function (n, i) { return n + i.qty; }, 0); }
    function subtotal() { return cart.reduce(function (s, i) { return s + i.preco * i.qty; }, 0); }

    function add(item) {
        var it = find(item.id);
        if (it) { it.qty += 1; }
        else { cart.push({ id: item.id, nome: item.nome, preco: item.preco, img: item.img || '', desc: item.desc || '', qty: 1 }); }
        save(cart);
        openDrawer();
    }
    function setQty(id, qty) {
        var it = find(id);
        if (!it) return;
        it.qty = qty;
        if (it.qty <= 0) cart = cart.filter(function (i) { return i.id !== id; });
        save(cart);
    }

    /* ---------- formatação ---------- */
    function brl(cents) {
        return 'R$ ' + (cents / 100).toLocaleString('pt-BR', {
            minimumFractionDigits: 2, maximumFractionDigits: 2
        });
    }
    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    /* ---------- drawer ---------- */
    var drawer  = document.getElementById('cart');
    var overlay = document.querySelector('[data-cart-overlay]');

    function openDrawer() {
        if (!drawer) return;
        drawer.classList.add('is-open');
        drawer.setAttribute('aria-hidden', 'false');
        if (overlay) overlay.hidden = false;
        document.body.style.overflow = 'hidden';
    }
    function closeDrawer() {
        if (!drawer) return;
        drawer.classList.remove('is-open');
        drawer.setAttribute('aria-hidden', 'true');
        if (overlay) overlay.hidden = true;
        document.body.style.overflow = '';
    }
    document.querySelectorAll('[data-cart-open]').forEach(function (b) { b.addEventListener('click', openDrawer); });
    document.querySelectorAll('[data-cart-close]').forEach(function (b) { b.addEventListener('click', closeDrawer); });
    if (overlay) overlay.addEventListener('click', closeDrawer);
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeDrawer(); });

    /* ---------- botões "Adicionar" ---------- */
    document.querySelectorAll('[data-add-to-cart]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            add({
                id: parseInt(btn.dataset.id, 10),
                nome: btn.dataset.nome,
                preco: parseInt(btn.dataset.preco, 10),
                img: btn.dataset.img,
                desc: btn.dataset.desc
            });
            btn.classList.add('added');
            setTimeout(function () { btn.classList.remove('added'); }, 700);
        });
    });

    /* ---------- linha compacta (drawer) ---------- */
    function drawerLineHTML(i) {
        return '<div class="cart-line">' +
            '<div class="cart-line__info"><span class="cart-line__name">' + esc(i.nome) + '</span>' +
            '<span class="cart-line__price">' + brl(i.preco) + '</span></div>' +
            '<div class="cart-line__qty">' +
            '<button type="button" data-dec="' + i.id + '" aria-label="Diminuir">−</button>' +
            '<span>' + i.qty + '</span>' +
            '<button type="button" data-inc="' + i.id + '" aria-label="Aumentar">+</button>' +
            '</div>' +
            '<span class="cart-line__sub">' + brl(i.preco * i.qty) + '</span>' +
            '</div>';
    }

    /* ---------- card visual (checkout) ---------- */
    function checkoutCardHTML(i) {
        var img = i.img ? esc(i.img) : PLACEHOLDER;
        return '<article class="ocard">' +
            '<div class="ocard__media"><img src="' + img + '" alt="' + esc(i.nome) + '" loading="lazy"></div>' +
            '<div class="ocard__body">' +
                '<h3 class="ocard__name">' + esc(i.nome) + '</h3>' +
                (i.desc ? '<p class="ocard__desc">' + esc(i.desc) + '</p>' : '') +
                '<div class="ocard__row">' +
                    '<div class="qstep">' +
                        '<button type="button" data-dec="' + i.id + '" aria-label="Diminuir">−</button>' +
                        '<span>' + i.qty + '</span>' +
                        '<button type="button" data-inc="' + i.id + '" aria-label="Aumentar">+</button>' +
                    '</div>' +
                    '<span class="ocard__calc">' + brl(i.preco) + ' × ' + i.qty +
                        ' = <strong>' + brl(i.preco * i.qty) + '</strong></span>' +
                '</div>' +
            '</div>' +
            '<button type="button" class="ocard__remove" data-rm="' + i.id + '" aria-label="Remover">×</button>' +
            '</article>';
    }

    function bindQtyButtons(scope) {
        scope.querySelectorAll('[data-inc]').forEach(function (b) {
            b.onclick = function () { var it = find(parseInt(b.dataset.inc, 10)); if (it) setQty(it.id, it.qty + 1); };
        });
        scope.querySelectorAll('[data-dec]').forEach(function (b) {
            b.onclick = function () { var it = find(parseInt(b.dataset.dec, 10)); if (it) setQty(it.id, it.qty - 1); };
        });
        scope.querySelectorAll('[data-rm]').forEach(function (b) {
            b.onclick = function () { setQty(parseInt(b.dataset.rm, 10), 0); };
        });
    }

    /* ---------- render ---------- */
    function render() {
        document.querySelectorAll('[data-cart-count]').forEach(function (el) {
            var n = count(); el.textContent = n; el.hidden = n === 0;
        });

        var box  = document.querySelector('[data-cart-items]');
        var empt = document.querySelector('[data-cart-empty]');
        var foot = document.querySelector('[data-cart-foot]');
        if (box) {
            if (cart.length) {
                box.innerHTML = cart.map(drawerLineHTML).join('');
                bindQtyButtons(box);
                if (empt) empt.hidden = true;
                if (foot) foot.hidden = false;
            } else {
                box.innerHTML = '';
                if (empt) empt.hidden = false;
                if (foot) foot.hidden = true;
            }
        }
        var sub = document.querySelector('[data-cart-subtotal]');
        if (sub) sub.textContent = brl(subtotal());

        renderCheckout();
    }

    /* ---------- checkout ---------- */
    function appliedFee() {
        var fee = CFG.deliveryFeeCents || 0;
        var entrega = document.querySelector('[data-delivery-toggle]:checked');
        var isDelivery = !entrega || entrega.value === 'entrega';
        return isDelivery ? fee : 0;
    }

    function renderCheckout() {
        var box = document.querySelector('[data-checkout-items]');
        if (!box) return;

        var empt   = document.querySelector('[data-checkout-empty]');
        var totals = document.querySelector('[data-checkout-totals]');
        var form   = document.querySelector('[data-checkout-form]');
        var title  = document.querySelector('[data-order-count]');

        if (title) title.textContent = count();

        if (!cart.length) {
            box.innerHTML = '';
            if (empt) empt.hidden = false;
            if (totals) totals.hidden = true;
            if (form) form.style.display = 'none';
            return;
        }
        if (empt) empt.hidden = true;
        if (totals) totals.hidden = false;
        if (form) form.style.display = '';

        box.innerHTML = cart.map(checkoutCardHTML).join('');
        bindQtyButtons(box);

        var fee = appliedFee();
        var sub = subtotal();
        var subEl  = document.querySelector('[data-sum-subtotal]');
        var totEl  = document.querySelector('[data-sum-total]');
        var feeEl  = document.querySelector('[data-sum-fee]');
        var feeRow = document.querySelector('[data-fee-row]');
        if (subEl) subEl.textContent = brl(sub);
        if (feeEl) feeEl.textContent = fee > 0 ? brl(fee) : 'Grátis';
        if (totEl) totEl.textContent = brl(sub + fee);
        if (feeRow) feeRow.hidden = !((CFG.deliveryFeeCents || 0) > 0);

        var min = CFG.minOrderCents || 0;
        var minNote = document.querySelector('[data-min-note]');
        if (minNote) minNote.hidden = !(min > 0 && sub < min);
    }

    function wireCheckoutFields() {
        var addr = document.querySelector('[data-address-block]');
        document.querySelectorAll('[data-delivery-toggle]').forEach(function (r) {
            r.addEventListener('change', function () {
                if (addr) addr.style.display = (r.value === 'retirada') ? 'none' : '';
                renderCheckout();
            });
        });
        document.querySelectorAll('.delivery-pick').forEach(function (lbl) {
            lbl.addEventListener('click', function () {
                document.querySelectorAll('.delivery-pick').forEach(function (l) { l.classList.remove('is-on'); });
                lbl.classList.add('is-on');
            });
        });
        var pay = document.querySelector('[data-payment-select]');
        var troco = document.querySelector('[data-troco-block]');
        if (pay && troco) {
            var upd = function () { troco.hidden = pay.value !== 'Dinheiro'; };
            pay.addEventListener('change', upd);
            upd();
        }
    }

    function buildMessage() {
        var L = [];
        L.push('*Novo pedido — ' + (CFG.brand || 'Di Bútcher') + '*');
        L.push('');
        L.push('*Itens:*');
        cart.forEach(function (i) {
            L.push('• ' + i.qty + 'x ' + i.nome + ' — ' + brl(i.preco) + ' = ' + brl(i.preco * i.qty));
        });
        L.push('');

        var fee = appliedFee();
        var sub = subtotal();
        var entrega = document.querySelector('[data-delivery-toggle]:checked');
        var isDelivery = !entrega || entrega.value === 'entrega';

        L.push('Subtotal: ' + brl(sub));
        if ((CFG.deliveryFeeCents || 0) > 0) L.push('Entrega: ' + (fee > 0 ? brl(fee) : 'Grátis'));
        L.push('*Total: ' + brl(sub + fee) + '*');
        L.push('');

        var g = function (sel) { var el = document.querySelector(sel); return el ? el.value.trim() : ''; };
        L.push('*Cliente:* ' + g('[name="nome"]'));
        L.push('*WhatsApp:* ' + g('[name="telefone"]'));

        if (isDelivery) {
            var end = g('[name="rua"]');
            if (g('[name="bairro"]')) end += ', ' + g('[name="bairro"]');
            if (g('[name="complemento"]')) end += ' (' + g('[name="complemento"]') + ')';
            L.push('*Entrega:* ' + end);
            if (g('[name="referencia"]')) L.push('*Referência:* ' + g('[name="referencia"]'));
        } else {
            L.push('*Retirada no local*');
        }

        var pgto = g('[name="pagamento"]');
        if (pgto === 'Dinheiro' && g('[name="troco"]')) pgto += ' (troco para ' + g('[name="troco"]') + ')';
        L.push('*Pagamento:* ' + pgto);

        if (g('[name="observacoes"]')) L.push('*Obs:* ' + g('[name="observacoes"]'));
        return L.join('\n');
    }

    function setupCheckoutForm() {
        var form = document.querySelector('[data-checkout-form]');
        if (!form) return;
        wireCheckoutFields();

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var err = document.querySelector('[data-checkout-error]');
            function fail(msg) { if (err) { err.textContent = msg; err.hidden = false; err.scrollIntoView({ behavior: 'smooth', block: 'center' }); } }
            if (err) err.hidden = true;

            if (!cart.length) return fail('Seu carrinho está vazio.');
            var nome = document.querySelector('[name="nome"]').value.trim();
            var fone = document.querySelector('[name="telefone"]').value.trim();
            if (!nome) return fail('Informe seu nome.');
            if (!fone) return fail('Informe seu telefone.');

            var entrega = document.querySelector('[data-delivery-toggle]:checked');
            var isDelivery = !entrega || entrega.value === 'entrega';
            if (isDelivery && !document.querySelector('[name="rua"]').value.trim()) {
                return fail('Informe o endereço de entrega (ou escolha "Retirar no local").');
            }
            var min = CFG.minOrderCents || 0;
            if (min > 0 && subtotal() < min) return fail('O pedido mínimo é ' + brl(min) + '.');

            window.open('https://wa.me/' + (CFG.waNumber || '') + '?text=' + encodeURIComponent(buildMessage()), '_blank');
        });
    }

    document.addEventListener('DOMContentLoaded', function () { setupCheckoutForm(); render(); });
    render();
})();
