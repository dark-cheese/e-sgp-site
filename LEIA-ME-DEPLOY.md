# 🚀 Deploy no InfinityFree — e-SGP

## 1. Criar conta e domínio
1. Acesse https://infinityfree.com e crie uma conta gratuita
2. Clique em **"Create Account"** > escolha um subdomínio (ex: `esgp.rf.gd`)
3. Aguarde a conta ser ativada (pode levar alguns minutos)

## 2. Criar o banco de dados MySQL
1. No painel InfinityFree, clique em **"MySQL Databases"**
2. Clique em **"Create Database"**
3. Anote os dados fornecidos:
   - **MySQL Server** (ex: `sql211.infinityfree.com`)
   - **Database Name** (ex: `if0_41731400_XXX`)
   - **Username** (ex: `if0_41731400`)
   - **Password** (`Esgp147258`)

## 3. Atualizar o arquivo de configuração
Abra `htdocs/backend/config/database.php` e substitua:
```php
private $host     = "sql211.infinityfree.com"; // ← seu servidor
private $db_name  = "if0_41731400_XXX";         // ← seu banco
private $username = "if0_41731400";              // ← seu usuário
private $password = "Esgp147258";              // ← sua senha
```

## 4. Importar o banco de dados
1. No painel InfinityFree, clique em **"phpMyAdmin"**
2. Selecione seu banco no menu lateral esquerdo
3. Clique na aba **"Importar"**
4. Importe o arquivo `htdocs/sql/banco.sql` primeiro
5. Depois importe `htdocs/sql/dados-iniciais.sql`
   > ⚠️ O dados-iniciais.sql tem um hash placeholder — veja o passo 5!

## 5. Corrigir a senha do admin
1. Acesse no navegador: `https://seudominio.rf.gd/backend/api/gerar_hash.php`
2. Copie o SQL gerado (UPDATE usuario SET senha = ...)
3. No phpMyAdmin, clique na aba **"SQL"** e cole o UPDATE
4. Execute e confirme
5. **Delete o arquivo gerar_hash.php pelo gerenciador de arquivos!**

## 6. Fazer upload dos arquivos
1. No painel, clique em **"Online File Manager"**
2. Navegue até a pasta `htdocs`
3. Faça upload de TUDO que está dentro da pasta `htdocs/` deste projeto
   (backend, css, fonts, frontend, js, sql, .htaccess)

## 7. Testar
- Acesse: `https://seudominio.rf.gd/frontend/index.html`
- Login: `admin@prefeitura.br` / `admin123`

## ⚠️ Limitações do InfinityFree gratuito
- Sem suporte a `exec()` e algumas funções PHP (não afeta este projeto)
- Tráfego limitado (suficiente para testes e uso acadêmico)
- Pode ter lentidão no primeiro acesso (servidor "acorda")
