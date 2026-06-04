<?php
/**
 * Checkout — resumo visual do pedido + dados do cliente.
 *
 * Importante: NENHUM dado do cliente é gravado no banco. O formulário
 * apenas monta a mensagem que será enviada ao WhatsApp da loja.
 */
declare(strict_types=1);

require_once __DIR__ . '/lib/view.php';

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

<section class="page-head page-head--checkout">
    <div class="container">
        <span class="section__eyebrow">Quase lá</span>
        <h1 class="page-head__title">Finalizar pedido</h1>
    </div>
</section>

<section class="section section--tight">
    <div class="container checkout">

        <!-- ░░ Seção 1 — Resumo do Pedido ░░ -->
        <div class="checkout__summary">
            <h2 class="ohead"><span class="ohead__icon"><?= icon('cleaver') ?></span>
                Seu Pedido <span class="ohead__count">(<span data-order-count>0</span> itens)</span>
            </h2>

            <div class="ocards" data-checkout-items></div>

            <div class="checkout__empty" data-checkout-empty hidden>
                <p>Seu carrinho está vazio.</p>
                <a class="btn btn--primary" href="/cardapio">Ver cardápio</a>
            </div>

            <div class="ototals" data-checkout-totals hidden>
                <div class="ototals__row"><span>Subtotal</span><span data-sum-subtotal>R$ 0,00</span></div>
                <?php if ($fee > 0): ?>
                    <div class="ototals__row" data-fee-row hidden><span>Taxa de entrega</span><span data-sum-fee><?= e(format_price($fee)) ?></span></div>
                <?php endif; ?>
                <div class="ototals__divider"></div>
                <div class="ototals__row ototals__row--grand"><span>Total</span><span data-sum-total>R$ 0,00</span></div>
                <?php if ($min > 0): ?>
                    <p class="checkout__min" data-min-note hidden>Pedido mínimo: <?= e(format_price($min)) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- ░░ Seção 2 — Seus Dados ░░ -->
        <form class="checkout__form" data-checkout-form novalidate>
            <h2 class="ohead"><span class="ohead__icon"><?= icon('pin') ?></span>
                Dados para envio no WhatsApp
            </h2>

            <!-- Subseção: Cliente -->
            <fieldset class="fgroup">
                <legend class="fgroup__title"><span class="fgroup__icon"><?= icon('user') ?></span> Dados do cliente</legend>
                <div class="field field--cream">
                    <label for="c_nome">Nome completo *</label>
                    <input id="c_nome" name="nome" type="text" required autocomplete="name" placeholder="Seu nome">
                </div>
                <div class="field field--cream field--wa">
                    <label for="c_fone">WhatsApp (telefone) *</label>
                    <input id="c_fone" name="telefone" type="tel" required inputmode="tel"
                           autocomplete="tel" placeholder="(98) 9XXXX-XXXX">
                    <span class="field__wa-badge"><?= icon('whatsapp') ?></span>
                </div>
            </fieldset>

            <!-- Subseção: Endereço -->
            <fieldset class="fgroup">
                <legend class="fgroup__title"><span class="fgroup__icon"><?= icon('pin') ?></span> Endereço de entrega</legend>

                <div class="delivery-toggle">
                    <label class="delivery-pick is-on">
                        <input type="radio" name="entrega" value="entrega" checked data-delivery-toggle>
                        <span class="delivery-pick__icon"><?= icon('truck') ?></span>
                        <span>Entrega</span>
                    </label>
                    <label class="delivery-pick">
                        <input type="radio" name="entrega" value="retirada" data-delivery-toggle>
                        <span class="delivery-pick__icon"><?= icon('plate') ?></span>
                        <span>Retirar no local</span>
                    </label>
                </div>

                <div data-address-block>
                    <div class="field field--cream">
                        <label for="c_rua">Endereço *</label>
                        <input id="c_rua" name="rua" type="text" autocomplete="street-address" placeholder="Rua e número">
                    </div>
                    <div class="field-row">
                        <div class="field field--cream">
                            <label for="c_bairro">Bairro</label>
                            <input id="c_bairro" name="bairro" type="text">
                        </div>
                        <div class="field field--cream">
                            <label for="c_comp">Complemento</label>
                            <input id="c_comp" name="complemento" type="text" placeholder="Apto, bloco...">
                        </div>
                    </div>
                    <div class="field field--cream">
                        <label for="c_ref">Ponto de referência</label>
                        <input id="c_ref" name="referencia" type="text">
                    </div>
                </div>
            </fieldset>

            <!-- Subseção: Pagamento e Notas -->
            <fieldset class="fgroup">
                <legend class="fgroup__title"><span class="fgroup__icon"><?= icon('money') ?></span> Pagamento e observações</legend>
                <div class="field field--cream">
                    <label for="c_pgto">Forma de pagamento *</label>
                    <select id="c_pgto" name="pagamento" data-payment-select>
                        <option value="Pix">Pix</option>
                        <option value="Dinheiro">Dinheiro</option>
                        <option value="Cartão (na entrega)">Cartão (na entrega)</option>
                    </select>
                </div>
                <div class="field field--cream" data-troco-block hidden>
                    <label for="c_troco">Troco para quanto?</label>
                    <input id="c_troco" name="troco" type="text" inputmode="decimal" placeholder="Ex.: 50,00">
                </div>
                <div class="field field--cream">
                    <label for="c_obs"><span class="inline-ico"><?= icon('note') ?></span> Observações</label>
                    <textarea id="c_obs" name="observacoes" rows="3" placeholder="Ex.: sem cebola, ponto da carne..."></textarea>
                </div>
            </fieldset>

            <p class="checkout__error" data-checkout-error hidden></p>

            <button class="btn-wa-finalize" type="submit">
                <?= icon('whatsapp') ?>
                <span>Enviar pedido pelo <strong>WhatsApp</strong></span>
            </button>

            <p class="checkout__safe">
                🔒 Seus dados estão seguros e vão direto para o nosso WhatsApp para
                processamento imediato do seu pedido personalizado.
            </p>
        </form>
    </div>
</section>

<?php page_end(); ?>
