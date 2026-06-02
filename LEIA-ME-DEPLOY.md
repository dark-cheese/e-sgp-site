# 🚀 Deploy no Railway — e-SGP

O Railway permite conexões de saída para IPs externos, então
funciona com seu banco em 200.131.251.11:3341.

## Passo a passo

### 1. Criar conta no Railway
- Acesse https://railway.app e entre com sua conta GitHub

### 2. Subir o projeto no GitHub
- Crie um repositório no GitHub (pode ser privado)
- Suba todos os arquivos desta pasta para o repositório

### 3. Criar projeto no Railway
- No Railway, clique em **"New Project"**
- Escolha **"Deploy from GitHub repo"**
- Selecione o repositório que você criou
- O Railway detecta o Dockerfile automaticamente e faz o build

### 4. Gerar domínio público
- Dentro do projeto no Railway, clique no serviço
- Vá em **"Settings" > "Networking" > "Generate Domain"**
- Você receberá uma URL tipo: `esgp-production.up.railway.app`

### 5. Testar
- Acesse: `https://esgp-production.up.railway.app/frontend/index.html`
- Login: `admin@prefeitura.br` / `admin123`

> ⚠️ Se a senha não funcionar, rode o gerar_hash.php uma vez para
> corrigir o hash no banco, depois delete o arquivo.

## Credenciais do banco (já configuradas)
- Host: 200.131.251.11
- Port: 3341
- Database: 2026ProjetoInv
- User: 2026Iventario
