/* ════════════════════════════════════════════════════════════════
   DI BÚTCHER — cart.js
   Carrinho 100% client-side (localStorage). No checkout, monta a
   mensagem e abre o WhatsApp da loja. Nenhum dado do cliente é salvo.
   ════════════════════════════════════════════════════════════════ */
(function () {
    'use strict';

    var CFG = window.DIBUTCHER || {};
    if (CFG.cartEnabled === false) return;

    var KEY = 'dibutcher_cart_v1';

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

    function add(id, nome, preco) {
        var it = find(id);
        if (it) { it.qty += 1; }
        else { cart.push({ id: id, nome: nome, preco: preco, qty: 1 }); }
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

    document.querySelectorAll('[data-cart-open]').forEach(function (b) {
        b.addEventListener('click', openDrawer);
    });
    document.querySelectorAll('[data-cart-close]').forEach(function (b) {
        b.addEventListener('click', closeDrawer);
    });
    if (overlay) overlay.addEventListener('click', closeDrawer);
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeDrawer(); });

    /* ---------- botões "Adicionar" ---------- */
    document.querySelectorAll('[data-add-to-cart]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            add(parseInt(btn.dataset.id, 10), btn.dataset.nome, parseInt(btn.dataset.preco, 10));
            btn.classList.add('added');
            setTimeout(function () { btn.classList.remove('added'); }, 700);
        });
    });

    /* ---------- render genérico ---------- */
    function itemRowHTML(i, compact) {
        return '<div class="cart-line" data-line="' + i.id + '">' +
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

    function esc(s) {
        return String(s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    function bindQtyButtons(scope) {
        scope.querySelectorAll('[data-inc]').forEach(function (b) {
            b.onclick = function () { var it = find(parseInt(b.dataset.inc, 10)); if (it) setQty(it.id, it.qty + 1); };
        });
        scope.querySelectorAll('[data-dec]').forEach(function (b) {
            b.onclick = function () { var it = find(parseInt(b.dataset.dec, 10)); if (it) setQty(it.id, it.qty - 1); };
        });
    }

    function render() {
        // Badge
        document.querySelectorAll('[data-cart-count]').forEach(function (el) {
            var n = count();
            el.textContent = n;
            el.hidden = n === 0;
        });

        // Drawer
        var box  = document.querySelector('[data-cart-items]');
        var empt = document.querySelector('[data-cart-empty]');
        var foot = document.querySelector('[data-cart-foot]');
        if (box) {
            if (cart.length) {
                box.innerHTML = cart.map(function (i) { return itemRowHTML(i); }).join('');
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
    function renderCheckout() {
        var box = document.querySelector('[data-checkout-items]');
        if (!box) return; // não estamos no checkout

        var empt   = document.querySelector('[data-checkout-empty]');
        var totals = document.querySelector('[data-checkout-totals]');
        var form   = document.querySelector('[data-checkout-form]');

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

        box.innerHTML = cart.map(function (i) { return itemRowHTML(i); }).join('');
        bindQtyButtons(box);

        var fee = CFG.deliveryFeeCents || 0;
        var entrega = document.querySelector('[data-delivery-toggle]:checked');
        var isDelivery = !entrega || entrega.value === 'entrega';
        var appliedFee = isDelivery ? fee : 0;

        var sub = subtotal();
        var subEl = document.querySelector('[data-sum-subtotal]');
        var totEl = document.querySelector('[data-sum-total]');
        var feeRow = document.querySelector('[data-fee-row]');
        if (subEl) subEl.textContent = brl(sub);
        if (totEl) totEl.textContent = brl(sub + appliedFee);
        if (feeRow) feeRow.hidden = !(appliedFee > 0);

        // Pedido mínimo
        var min = CFG.minOrderCents || 0;
        var minNote = document.querySelector('[data-min-note]');
        if (minNote) minNote.hidden = !(min > 0 && sub < min);
    }

    /* Mostra/oculta campos de endereço e troco */
    function wireCheckoutFields() {
        var addr = document.querySelector('[data-address-block]');
        document.querySelectorAll('[data-delivery-toggle]').forEach(function (r) {
            r.addEventListener('change', function () {
                if (addr) addr.style.display = (r.value === 'retirada') ? 'none' : '';
                renderCheckout();
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
        var lines = [];
        lines.push('*Novo pedido — ' + (CFG.brand || 'Di Bútcher') + '*');
        lines.push('');
        lines.push('*Itens:*');
        cart.forEach(function (i) {
            lines.push('• ' + i.qty + 'x ' + i.nome + ' — ' + brl(i.preco * i.qty));
        });
        lines.push('');

        var entrega = document.querySelector('[data-delivery-toggle]:checked');
        var isDelivery = !entrega || entrega.value === 'entrega';
        var fee = isDelivery ? (CFG.deliveryFeeCents || 0) : 0;
        var sub = subtotal();

        lines.push('Subtotal: ' + brl(sub));
        if (fee > 0) lines.push('Entrega: ' + brl(fee));
        lines.push('*Total: ' + brl(sub + fee) + '*');
        lines.push('');

        var g = function (sel) { var el = document.querySelector(sel); return el ? el.value.trim() : ''; };
        lines.push('*Cliente:* ' + g('[name="nome"]'));
        lines.push('*Telefone:* ' + g('[name="telefone"]'));

        if (isDelivery) {
            var end = g('[name="rua"]');
            if (g('[name="bairro"]')) end += ', ' + g('[name="bairro"]');
            if (g('[name="complemento"]')) end += ' (' + g('[name="complemento"]') + ')';
            lines.push('*Entrega:* ' + end);
            if (g('[name="referencia"]')) lines.push('*Referência:* ' + g('[name="referencia"]'));
        } else {
            lines.push('*Retirada no local*');
        }

        var pgto = g('[name="pagamento"]');
        if (pgto === 'Dinheiro' && g('[name="troco"]')) {
            pgto += ' (troco para ' + g('[name="troco"]') + ')';
        }
        lines.push('*Pagamento:* ' + pgto);

        if (g('[name="observacoes"]')) lines.push('*Obs:* ' + g('[name="observacoes"]'));

        return lines.join('\n');
    }

    function setupCheckoutForm() {
        var form = document.querySelector('[data-checkout-form]');
        if (!form) return;
        wireCheckoutFields();

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var err = document.querySelector('[data-checkout-error]');
            function fail(msg) {
                if (err) { err.textContent = msg; err.hidden = false; }
            }
            if (err) err.hidden = true;

            if (!cart.length) { return fail('Seu carrinho está vazio.'); }

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
            if (min > 0 && subtotal() < min) {
                return fail('O pedido mínimo é ' + brl(min) + '.');
            }

            var url = 'https://wa.me/' + (CFG.waNumber || '') +
                      '?text=' + encodeURIComponent(buildMessage());
            window.open(url, '_blank');
        });
    }

    /* ---------- init ---------- */
    document.addEventListener('DOMContentLoaded', function () {
        setupCheckoutForm();
        render();
    });
    render();
})();
