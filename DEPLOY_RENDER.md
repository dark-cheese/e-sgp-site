# 🚀 GUIA COMPLETO - DEPLOY NO RENDER.COM

## 📋 Pré-requisitos

- [x] Conta no GitHub com o repositório
- [x] Conta no Render.com
- [x] Banco de dados MySQL externo(seu banco atual funciona perfeitamente)

---

## ✅ Checklist Antes do Deploy

- [ ] Arquivo `requirements.txt` atualizado ✓
- [ ] `Procfile` configurado ✓
- [ ] `render.yaml` criado ✓
- [ ] `.env.example` criado ✓
- [ ] `.gitignore` configurado ✓
- [ ] `app.py` compatível com Render ✓
- [ ] Respositório no GitHub ✓

---

## 🎯 PASSO A PASSO PARA DEPLOY

### **Passo 1: Preparar o GitHub**

```bash
# 1. Inicializar git (se não estiver)
git init

# 2. Adicionar todos os arquivos
git add .

# 3. Fazer commit
git commit -m "Preparar para deploy no Render"

# 4. Fazer push para o GitHub
git push origin main
```

**Importante:** Certifique-se de que você subiu:
- ✓ app.py (atualizado)
- ✓ requirements.txt (com todas as dependências)
- ✓ Procfile
- ✓ render.yaml
- ✓ .env.example
- ✓ .gitignore
- ✓ Todos os arquivos HTML, CSS, JS

---

### **Passo 2: Conectar GitHub ao Render**

1. Acesse: https://render.com
2. Faça login com sua conta
3. Clique em **"New +"** → **"Web Service"**
4. Selecione **"Deploy an existing repository"**
5. Procure por seu repositório do GitHub
6. Selecione o repositório e clique em **"Connect"**

---

### **Passo 3: Configurar o Web Service**

Na tela de configuração:

**Nome da aplicação:**
```
e-sgp-site
```

**Ambiente:**
```
Docker
```

**Região:**
```
Ohio (us-east) ou a mais próxima
```

**Ramo:**
```
main (ou a branch principal)
```

**Comando de build:**
```
pip install -r requirements.txt
```

**Comando de inicialização:**
```
gunicorn app:app
```

---

### **Passo 4: Definir Variáveis de Ambiente**

Clique em **"Advanced"** e depois em **"Add Environment Variable"**

Adicione TODAS essas variáveis:

| Chave | Valor | Exemplo |
|-------|-------|---------|
| `DATABASE_URL` | String de conexão MySQL | `mysql+pymysql://user:pass@host:3341/db` |
| `FLASK_ENV` | production | `production` |
| `DB_USER` | Usuário MySQL | `2026Iventario` |
| `DB_PASSWORD` | Senha MySQL | `Inventa@2026` |
| `DB_HOST` | Host MySQL | `200.131.251.11` |
| `DB_PORT` | Porta MySQL | `3341` |
| `DB_NAME` | Nome do banco | `2026ProjetoInv` |

**Opção A: Com DATABASE_URL (RECOMENDADO)**

Se seu banco suporta MySQL remoto, use:

```
DATABASE_URL=mysql+pymysql://2026Iventario:Inventa@2026@200.131.251.11:3341/2026ProjetoInv
```

**Opção B: Com variáveis individuais**

Se DATABASE_URL não funcionar, as variáveis individuais serão usadas como fallback.

---

### **Passo 5: Deploy**

1. Clique em **"Create Web Service"**
2. Aguarde o build (2-5 minutos)
3. Quando terminar, você verá: ✓ **"Your service is live"**
4. Copie a URL fornecida (ex: https://e-sgp-site.onrender.com)

---

## ✅ Validar o Deploy

Após o deployment:

### 1. Testar Health Check

```bash
curl https://seu-app.onrender.com/api/healthcheck
```

Deve retornar:
```json
{"server": "online", "database": "online"}
```

### 2. Testar Login

```bash
curl -X POST https://seu-app.onrender.com/api/login_simple \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@gmail.com","senha":"admin123"}'
```

Deve retornar:
```json
{
  "success": true,
  "usuario": {
    "id": 1,
    "nome": "Isabella",
    "email": "admin@gmail.com",
    "perfil": "admin",
    "tipoUsuarioId": 1
  }
}
```

### 3. Acessar no Navegador

```
https://seu-app.onrender.com/index.html
```

Login com:
- Email: `admin@gmail.com`
- Senha: `admin123`

---

## 🔧 Como Atualizar Após Deploy

Sempre que fizer mudanças:

```bash
# 1. Fazer as alterações no código
# 2. Commit
git add .
git commit -m "Descrição das mudanças"

# 3. Push para GitHub
git push origin main

# 4. Render fará o deploy automaticamente
```

---

## 🐛 Troubleshooting

### Erro: "JSON.parse: unexpected end of data"

**Causa:** DATABASE_URL não definida ou banco desconectado

**Solução:**
1. Verificar em Render → Environment Variables
2. Confirmar que DATABASE_URL está correto
3. Testar conexão com banco direto

### Erro: "Application failed to start"

**Causa:** Dependência faltando em requirements.txt

**Solução:**
1. Verificar logs em Render → Logs
2. Adicionar dependência em requirements.txt
3. Fazer push novamente

### Erro: "Cannot connect to database"

**Causa:** Banco de dados inacessível

**Solução:**
```bash
# Verificar se banco está ativo:
mysql -h 200.131.251.11 -u 2026Iventario -p -e "SELECT 1"

# Se banco não responde, contate suporte da instituição
```

### Refresh infinito na página

**Causa:** main.js carregado em index.html

**Solução:** ✓ Já corrigido no arquivo `index.html`

---

## 📊 Monitorar o Deploy

No Render dashboard:

- **Logs:** Ver saída do servidor
- **Metrics:** CPU, memória, requisições
- **Health:** Status do serviço
- **Settings:** Modificar variáveis, falhar deploy

---

## 🎉 Próximas Etapas

Após deploy bem-sucedido:

1. [ ] Testar login funciona
2. [ ] Criar outros endpoints (secretarias, itens, etc)
3. [ ] Implementar CRUD completo
4. [ ] Adicionar autenticação JWT
5. [ ] Implementar permissões por tipo de usuário
6. [ ] Criar documentação API (Swagger)

---

## 📞 Links Úteis

- Render Docs: https://render.com/docs
- Flask Docs: https://flask.palletsprojects.com
- SQLAlchemy: https://docs.sqlalchemy.org
- Gunicorn: https://gunicorn.org

---

**Versão:** 1.0  
**Data:** 1 de junho de 2026  
**Status:** ✅ PRONTO PARA DEPLOY
