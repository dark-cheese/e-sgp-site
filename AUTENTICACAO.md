# Sistema de Autenticação - e-SGP

## 📋 Resumo

Sistema de login completo com JavaScript (frontend) e Python/Flask (backend) para a aplicação e-SGP.

---

## 🔧 Backend (Python/Flask)

### Dependências
```
Flask==2.3.0
Flask-SQLAlchemy==3.1.1
flask-cors==4.0.1
mysql-connector-python==8.3.0
```

### Endpoints disponíveis

#### 1. **POST /api/login_simple**
Realiza o login do usuário.

**Request:**
```json
{
    "email": "user@example.com",
    "senha": "password"
}
```

**Response (Sucesso - 200):**
```json
{
    "success": true,
    "message": "Login realizado com sucesso!",
    "usuario": {
        "id": 1,
        "nome": "João Silva",
        "email": "joao@example.com",
        "perfil": "admin"
    }
}
```

**Response (Erro - 401):**
```json
{
    "success": false,
    "message": "E-mail ou senha incorretos!"
}
```

#### 2. **GET /api/usuario**
Obtém dados do usuário atual.

**Headers:**
```
X-User-ID: 1
```

**Response:**
```json
{
    "success": true,
    "usuario": {
        "id": 1,
        "nome": "João Silva",
        "email": "joao@example.com"
    }
}
```

#### 3. **POST /api/logout**
Endpoint para logout (limpeza de sessão no cliente).

**Response:**
```json
{
    "success": true,
    "message": "Logout realizado com sucesso!"
}
```

#### 4. **GET /api/healthcheck**
Verifica saúde da aplicação e conexão com banco.

**Response:**
```json
{
    "server": "online",
    "database": "online"
}
```

---

## 🎨 Frontend (JavaScript)

### Arquivos criados

#### **auth.js** - Autenticação
- `toggleSenha()` - Alterna visibilidade da senha (ícone na tela de login)
- `login(event)` - Processa submissão do formulário de login
- `logout()` - Desloga o usuário
- `verificarLogin()` - Valida se usuário está autenticado

**Fluxo de login:**
1. Usuário preenche email e senha
2. Clica em "Entrar"
3. JavaScript valida os dados
4. Envia requisição POST para `/api/login_simple`
5. Se sucesso: armazena usuário em `sessionStorage` e redireciona ao dashboard
6. Se erro: exibe mensagem de erro

#### **main.js** - Funções gerais
- `getUsuarioLogado()` - Recupera dados da sessão
- `mostrarData()` - Formata data (DD/MM/YYYY)
- `saudacao()` - Saudação por hora (Bom dia / Boa tarde / Boa noite)
- `inicializarFiltros()` - Setup de filtros de tabela
- `inicializarPaginacao()` - Setup de paginação
- Verificação automática de autenticação ao carregar páginas

#### **utils.js** - Utilitários
- `mostrarNotificacao(msg, tipo)` - Notificações animadas na tela
- `validarEmail(email)` - Valida formato de email
- `fazerRequisicaoGET/POST/PUT/DELETE()` - Funções para requisições HTTP
- `formatarData()` - Formata datas
- `selecionarTodos(checkbox)` - Seleciona todos checkboxes

---

## 🚀 Como usar

### 1. Instalar dependências Python
```bash
pip install -r requirements.txt
```

### 2. Iniciar servidor Flask
```bash
python app.py
```

O servidor rodará em `http://localhost:5000`

### 3. Acessar a aplicação
Abra no navegador: `http://localhost:5000` ou `http://localhost:5000/index.html`

### 4. Fazer login
- Email: (qualquer email no banco de dados)
- Senha: (senha armazenada)

---

## 🔒 Segurança

✅ **CORS habilitado** para requisições de frontend
✅ **Validação de entrada** (email e senha obrigatórios)
✅ **Suporte a hash de senha** (werkzeug.security)
✅ **Compatibilidade com senhas em texto plano** (para testes)
✅ **sessionStorage** para armazenar token do usuário
✅ **Proteção de rotas** (redireciona ao login se não autenticado)

---

## 📱 Fluxo de Navegação

```
index.html (Login)
    ↓ (sucesso)
dashboard.html (Main)
    ↓
Outras páginas (secretarias, itens, etc)
    ↓ (logout)
index.html (Login)
```

---

## 🧪 Teste rápido

**Testar healthcheck:**
```bash
curl http://localhost:5000/api/healthcheck
```

**Testar login:**
```bash
curl -X POST http://localhost:5000/api/login_simple \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","senha":"password"}'
```

---

## 📝 Estrutura do banco de dados esperada

Tabela `usuario`:
```sql
CREATE TABLE usuario (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100),
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(100) NOT NULL
);
```

---

## ⚙️ Configuração de produção

Para deployar no Render ou similar:

1. Definir variável de ambiente `DATABASE_URL` com a string de conexão
2. O Flask usará essa URL automaticamente
3. Certifique-se de que CORS está habilitado

---

## 🐛 Troubleshooting

**"Erro ao conectar com o servidor"**
- Verifique se o servidor Flask está rodando
- Verifique se a porta está correta (padrão: 5000)
- Verifique o console do navegador (F12) para mais detalhes

**"E-mail ou senha incorretos"**
- Verifique credenciais no banco de dados
- Certifique-se de que o usuário existe

**"CORS error"**
- CORS já está habilitado no app.py
- Se ainda receber erro, verifique headers da requisição

---

## 📚 Recursos

- [Flask Documentation](https://flask.palletsprojects.com/)
- [Flask-SQLAlchemy](https://flask-sqlalchemy.palletsprojects.com/)
- [Werkzeug Security](https://werkzeug.palletsprojects.com/en/2.3.x/security/)

