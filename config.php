<?php
/**
 * Di Bútcher Premium Burger — Central de Constantes
 * Brand Identity Document Oficial · São Luís, MA · 2026
 * PHP 8.2+
 */

declare(strict_types=1);

date_default_timezone_set('America/Fortaleza');

/* ─────────────────────────────────────────────────────────────
 * IDENTIDADE DE MARCA
 * ───────────────────────────────────────────────────────────── */
const BRAND_NAME        = 'Di Bútcher';
const BRAND_FULL_NAME   = 'Di Bútcher Premium Burger';
const BRAND_YEAR        = '2026';
const BRAND_CITY        = 'São Luís';
const BRAND_STATE       = 'MA';

const BRAND_SLOGAN_MAIN = 'Forjado na brasa. Entregue com honra.';
const BRAND_SLOGAN_ALT1 = 'Carne de respeito. Ponto final.';
const BRAND_SLOGAN_ALT2 = 'O smash que São Luís estava esperando.';
const BRAND_SLOGAN_ALT3 = 'Feito à mão. Sentido na alma.';
const BRAND_TAGLINE_GEO = 'Di Bútcher · São Luís, MA · Est. 2026';
const BRAND_PROMISE     = 'O melhor smash burger que você vai comer em São Luís.';

/* ─────────────────────────────────────────────────────────────
 * CONTATO & CANAIS
 * ───────────────────────────────────────────────────────────── */
const WHATSAPP_NUMBER      = '5598981010101'; // formato: 5598XXXXXXXXX
const WHATSAPP_DISPLAY     = '(98) 98101-0101';
const WHATSAPP_DEFAULT_MSG = 'Olá! Vim pelo site da Di Bútcher e quero fazer um pedido. 🔥';

const IFOOD_URL     = 'https://www.ifood.com.br/';
const INSTAGRAM_URL = 'https://www.instagram.com/dibutcher';
const TIKTOK_URL    = 'https://www.tiktok.com/@dibutcher';

/**
 * Gera um link wa.me com mensagem URL-encoded.
 */
function whatsapp_link(string $message = WHATSAPP_DEFAULT_MSG): string
{
    return 'https://wa.me/' . WHATSAPP_NUMBER . '?text=' . rawurlencode($message);
}

/* ─────────────────────────────────────────────────────────────
 * SISTEMA / HORÁRIOS
 * ───────────────────────────────────────────────────────────── */
const SITE_URL              = 'https://dibutcher.com.br';
const TIMEZONE              = 'America/Fortaleza';
const HORARIO_SEMANA        = 'Terça a Domingo: 18h – 23h';
const HORARIO_FIM_DE_SEMANA = 'Segunda-feira: Fechado';
const DELIVERY_AREA         = 'Entregamos em toda São Luís — MA';

/* ─────────────────────────────────────────────────────────────
 * SEO
 * ───────────────────────────────────────────────────────────── */
const SEO_TITLE       = 'Di Bútcher Premium Burger · Smash Burger Artesanal em São Luís — MA';
const SEO_DESCRIPTION = 'O melhor smash burger que você vai comer em São Luís. Blends artesanais smashados na brasa, entregues com honra. Peça pelo WhatsApp ou iFood.';
const SEO_KEYWORDS    = 'smash burger são luís, hamburgueria artesanal maranhão, di bútcher, delivery hambúrguer são luís, melhor burger ma, premium burger são luís';

const OG_TITLE        = 'Di Bútcher Premium Burger · Forjado na brasa';
const OG_DESCRIPTION  = 'Smash burger artesanal premium em São Luís — MA. Carne de respeito. Ponto final.';
const OG_IMAGE_URL    = SITE_URL . '/assets/img/og-image.jpg';
const CANONICAL_URL   = SITE_URL . '/';

/* ─────────────────────────────────────────────────────────────
 * CARDÁPIO (estrutura de dados inline — sem banco nesta fase)
 * ───────────────────────────────────────────────────────────── */
$GLOBALS['menu'] = [
    'smashes' => [
        [
            'nome'      => 'The Butcher Classic',
            'descricao' => 'Blend 180g smashado · queijo americano duplo · maionese artesanal · pickles · cebola roxa',
            'preco'     => 'R$ 32,00',
            'badge'     => 'MAIS PEDIDO',
        ],
        [
            'nome'      => 'Di Noir',
            'descricao' => 'Blend 180g · pão black · cheddar defumado · bacon crocante · molho barbecue artesanal',
            'preco'     => 'R$ 38,00',
            'badge'     => 'PREMIUM',
        ],
        [
            'nome'      => 'Bútcher Smash Duplo',
            'descricao' => 'Dois blends 120g smashados · queijo duplo · alface americana · tomate · molho especial Di Bútcher',
            'preco'     => 'R$ 42,00',
            'badge'     => 'SIGNATURE',
        ],
    ],
    'acompanhamentos' => [
        [
            'nome'      => 'Fritas Rústicas',
            'descricao' => 'Batata palito com pele · sal grosso · alecrim',
            'preco'     => 'R$ 16,00',
            'badge'     => null,
        ],
        [
            'nome'      => 'Onion Rings',
            'descricao' => 'Anéis de cebola empanados crocantes · molho ranch',
            'preco'     => 'R$ 18,00',
            'badge'     => null,
        ],
        [
            'nome'      => 'Fritas com Cheddar',
            'descricao' => 'Fritas rústicas cobertas com cheddar cremoso',
            'preco'     => 'R$ 22,00',
            'badge'     => 'FAVORITO',
        ],
    ],
    'bebidas' => [
        [
            'nome'      => 'Refrigerante Lata',
            'descricao' => '350ml · Coca-Cola · Guaraná Antarctica',
            'preco'     => 'R$ 7,00',
            'badge'     => null,
        ],
        [
            'nome'      => 'Suco Natural',
            'descricao' => 'Maracujá · Laranja · Manga · 500ml',
            'preco'     => 'R$ 12,00',
            'badge'     => null,
        ],
        [
            'nome'      => 'Água Mineral',
            'descricao' => '500ml com ou sem gás',
            'preco'     => 'R$ 5,00',
            'badge'     => null,
        ],
    ],
];
