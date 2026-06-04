# Guia de Deploy — Di Bútcher Premium Burger

Site em **PHP nativo + SQLite**. Não tem build, não tem `npm install`, não tem
dependência externa. Deploy = **subir os arquivos** num servidor com PHP 8.2+.

---

## ⚠️ O detalhe que decide a hospedagem: disco persistente

O banco é um **arquivo** (`/data/dibutcher.sqlite`). Para os dados não sumirem,
o servidor precisa de **disco que persiste** entre reinícios/deploys.

| Tipo de host | Disco persistente? | Serve pra nós? |
|---|---|---|
| Hospedagem compartilhada (cPanel/Apache) | ✅ Sim | **Ideal** |
| Vercel / Netlify | — (nem roda PHP) | ❌ Não |
| Render / Railway (free) | ❌ Efêmero no free | ⚠️ Banco zera no deploy |

> **Regra de ouro:** para PHP + SQLite, use **hospedagem compartilhada Apache**
> (cPanel). É barata, o disco é persistente e o `.htaccess` funciona nativamente.

---

## Opção A — Gratuita: InfinityFree

Melhor opção **100% grátis** compatível com este projeto (PHP 8.x, disco
persistente, `.htaccess`, SQLite).

1. Crie conta em <https://infinityfree.net> e um site novo.
2. Acesse o **File Manager** (ou use FTP — dados em "FTP Accounts").
3. Envie **todo o conteúdo do repositório** para a pasta `htdocs/`.
4. Garanta que a pasta `data/` exista e seja **gravável** (permissão `755`).
5. Acesse `https://seusite.infinityfreeapp.com/` → site no ar.
6. Acesse `/admin/` → crie o usuário admin.

Limitações do plano grátis: limites de CPU/visitas e subdomínio (dá pra apontar
domínio próprio depois).

---

## Opção B — Recomendada: hospedagem paga barata

Mais estável e com domínio `.com.br`. Custo típico: **R$ 8–15/mês**.
Provedores com PHP 8.2+ e cPanel: Hostinger, HostGator, KingHost, Locaweb.

1. Contrate um plano de **Hospedagem Compartilhada** com PHP 8.2+.
2. No cPanel, defina a versão do PHP para **8.2 ou superior**.
3. Suba os arquivos para `public_html/` (File Manager ou FTP/FileZilla).
4. Aponte seu domínio (geralmente já vem configurado).
5. Acesse `/admin/` para o setup inicial.

---

## Passo a passo do FTP (FileZilla)

1. Baixe o [FileZilla](https://filezilla-project.org/).
2. Conecte com os dados de FTP do host (host, usuário, senha, porta 21).
3. Envie estes itens para a raiz pública (`htdocs/` ou `public_html/`):
   ```
   index.php  config.php  .htaccess
   admin/  assets/  components/  data/  lib/
   ```
4. **Não precisa** enviar `.git/`, `DEPLOY.md` nem `*.sqlite` (o banco é criado sozinho).

---

## Checklist pós-deploy

- [ ] Editar `config.php`: `WHATSAPP_NUMBER`, `IFOOD_URL`, `INSTAGRAM_URL`,
      `TIKTOK_URL`, `SITE_URL`/`CANONICAL_URL`, horários.
- [ ] Conferir que `data/` é **gravável** pelo PHP (permissão 755).
- [ ] Abrir `/` e validar o site público.
- [ ] Abrir `/admin/` e criar usuário/senha do admin.
- [ ] Testar: adicionar/editar um item e ver refletir no site.
- [ ] Confirmar HTTPS ativo (a maioria dos hosts oferece SSL grátis — Let's Encrypt).

---

## Requisitos do servidor

- PHP **8.2+** com extensões `pdo_sqlite` e `session` (padrão na maioria dos hosts).
- Apache com `mod_rewrite` e `mod_headers` (para `.htaccess`).
- Pasta `data/` com permissão de escrita.

> **Nginx?** O `.htaccess` é ignorado. É preciso traduzir as regras (clean URLs,
> headers, bloqueio de `/data` e `/lib`) para o `nginx.conf`. Hospedagem
> compartilhada normalmente é Apache, então isso raramente é um problema.

---

## Segurança do admin (2FA)

O painel tem **verificação em duas etapas** (TOTP) compatível com Google
Authenticator, Authy e Microsoft Authenticator.

- No primeiro acesso (`/admin/`), você cria o usuário e é levado direto ao
  cadastro do 2FA. Abra o app → **“Inserir chave de configuração”** → cole a
  chave mostrada na tela → confirme com o código de 6 dígitos.
- **Guarde a chave de configuração** num lugar seguro: com ela você
  reconfigura o app se trocar de celular.
- Depois de criada sua conta, **ninguém consegue cadastrar outro admin** pelo
  site — a tela de criação fica bloqueada permanentemente.

### Perdi o celular E a chave (recuperação)

Rode no servidor, via terminal/SSH, a partir da pasta do site:

```bash
php bin/reset-2fa.php
```

Isso desativa o 2FA. Faça login só com a senha e reative em **Minha conta**.
(O script recusa execução pelo navegador — só funciona por linha de comando.)

## Backup do cardápio

Como o banco é um arquivo só, backup = **baixar `data/dibutcher.sqlite`**.
Para restaurar, basta subir o arquivo de volta.
