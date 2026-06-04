<?php
/**
 * Checkout — revisão do pedido + dados do cliente.
 *
 * Importante: NENHUM dado do cliente é gravado no banco. O formulário
 * apenas monta a mensagem que será enviada ao WhatsApp da loja.
 */
declare(strict_types=1);

require_once __DIR__ . '/lib/view.php';

// Se o carrinho estiver desativado, manda pro cardápio.
if (!cart_enabled()) {
    header('Location: /cardapio');
    exit;
}

$fee = delivery_fee_cents();
$min = min_order_cents();

page_start([
    'active'      => 'cardapio',
    'path'        => '/checkout',
    'title'       => 'Finalizar pedido · ' . cfg('brand_full_name'),
    'description' => 'Revise seu pedido e envie pelo WhatsApp.',
]);
?>

<section class="page-head">
    <div class="container">
        <span class="section__eyebrow">Quase lá</span>
        <h1 class="page-head__title">Finalizar pedido</h1>
        <p class="page-head__sub">Seus dados vão direto para o WhatsApp da loja — não ficam salvos aqui.</p>
    </div>
</section>

<section class="section section--tight">
    <div class="container checkout">
        <!-- Resumo do pedido (preenchido pelo carrinho) -->
        <div class="checkout__summary">
            <h2>Seu pedido</h2>
            <div class="checkout__items" data-checkout-items></div>
            <div class="checkout__empty" data-checkout-empty hidden>
                <p>Seu carrinho está vazio.</p>
                <a class="btn btn--primary" href="/cardapio">Ver cardápio</a>
            </div>
            <dl class="checkout__totals" data-checkout-totals hidden>
                <div><dt>Subtotal</dt><dd data-sum-subtotal>R$ 0,00</dd></div>
                <?php if ($fee > 0): ?>
                    <div data-fee-row hidden><dt>Entrega</dt><dd><?= e(format_price($fee)) ?></dd></div>
                <?php endif; ?>
                <div class="checkout__grand"><dt>Total</dt><dd data-sum-total>R$ 0,00</dd></div>
            </dl>
            <?php if ($min > 0): ?>
                <p class="checkout__min" data-min-note hidden>
                    Pedido mínimo: <?= e(format_price($min)) ?>
                </p>
            <?php endif; ?>
        </div>

        <!-- Dados do cliente -->
        <form class="checkout__form" data-checkout-form novalidate>
            <h2>Seus dados</h2>

            <div class="field">
                <label for="c_nome">Nome *</label>
                <input id="c_nome" name="nome" type="text" required autocomplete="name">
            </div>
            <div class="field">
                <label for="c_fone">Telefone (WhatsApp) *</label>
                <input id="c_fone" name="telefone" type="tel" required inputmode="tel"
                       autocomplete="tel" placeholder="(98) 9XXXX-XXXX">
            </div>

            <fieldset class="field field--inline" role="radiogroup" aria-label="Tipo de pedido">
                <label class="radio">
                    <input type="radio" name="entrega" value="entrega" checked data-delivery-toggle> Entrega
                </label>
                <label class="radio">
                    <input type="radio" name="entrega" value="retirada" data-delivery-toggle> Retirar no local
                </label>
            </fieldset>

            <div data-address-block>
                <div class="field">
                    <label for="c_rua">Endereço *</label>
                    <input id="c_rua" name="rua" type="text" autocomplete="street-address"
                           placeholder="Rua e número">
                </div>
                <div class="field-row">
                    <div class="field">
                        <label for="c_bairro">Bairro</label>
                        <input id="c_bairro" name="bairro" type="text">
                    </div>
                    <div class="field">
                        <label for="c_comp">Complemento</label>
                        <input id="c_comp" name="complemento" type="text" placeholder="Apto, bloco...">
                    </div>
                </div>
                <div class="field">
                    <label for="c_ref">Ponto de referência</label>
                    <input id="c_ref" name="referencia" type="text">
                </div>
            </div>

            <div class="field">
                <label for="c_pgto">Forma de pagamento *</label>
                <select id="c_pgto" name="pagamento" data-payment-select>
                    <option value="Pix">Pix</option>
                    <option value="Dinheiro">Dinheiro</option>
                    <option value="Cartão (na entrega)">Cartão (na entrega)</option>
                </select>
            </div>
            <div class="field" data-troco-block hidden>
                <label for="c_troco">Troco para quanto?</label>
                <input id="c_troco" name="troco" type="text" inputmode="decimal" placeholder="Ex.: 50,00">
            </div>

            <div class="field">
                <label for="c_obs">Observações</label>
                <textarea id="c_obs" name="observacoes" rows="3"
                          placeholder="Ex.: sem cebola, ponto da carne..."></textarea>
            </div>

            <p class="checkout__error" data-checkout-error hidden></p>

            <button class="btn btn--whatsapp btn--block btn--lg" type="submit">
                <?= icon('whatsapp') ?> Enviar pedido pelo WhatsApp
            </button>
            <p class="checkout__note">Ao enviar, abrimos o WhatsApp da loja com o resumo do seu pedido.</p>
        </form>
    </div>
</section>

<?php page_end(); ?>
