# e-SGP - Sistema de Gestão de Patrimônio

## 📱 Sobre

Sistema web completo para gestão de patrimônio em instituições, desenvolvido com **Flask** (backend) e **JavaScript/HTML** (frontend).

**Stack Tecnológico:**
- Backend: Python 3.9+ + Flask 3.0
- Frontend: JavaScript puro + HTML5 + CSS3
- Banco de Dados: MySQL 5.7+
- Deploy: Render.com

**Principais Funcionalidades:**
- ✅ Sistema de autenticação com 3 níveis (admin, gestor, usuário)
- ✅ Gestão de patrimônio
- ✅ Rastreamento de itens
- ✅ Controle de secretarias e unidades
- ✅ Dashboard interativo

---

## 🚀 Início Rápido (LOCAL)

### Pré-requisitos

- Python 3.9+
- MySQL 5.7+
- Git
- pip (gerenciador de pacotes Python)

### Instalação

```bash
# 1. Clonar repositório
git clone https://github.com/seu-usuario/e-sgp-site.git
cd e-sgp-site

# 2. Criar ambiente virtual
python -m venv venv
source venv/bin/activate  # No Windows: venv\Scripts\activate

# 3. Instalar dependências
pip install -r requirements.txt

# 4. Configurar banco de dados
mysql -h 200.131.251.11 -P 3341 -u 2026Iventario -p'Inventa@2026' < sql/banco.sql
mysql -h 200.131.251.11 -P 3341 -u 2026Iventario -p'Inventa@2026' 2026ProjetoInv < sql/dados-iniciais.sql

# 5. Iniciar servidor
python app.py

# 6. Acessar no navegador
# URL: http://localhost:5000/index.html
# Login: admin@gmail.com / admin123
```

---

## 🌐 Deploy no Render

Para deploy em **produção** na plataforma Render.com, leia: [DEPLOY_RENDER.md](DEPLOY_RENDER.md)

**Verificar se está pronto:**
```bash
python check_render_ready.py
```

---

## 📁 Estrutura de Arquivos

```
e-sgp-site/
├── 🔧 CONFIGURAÇÃO
│   ├── app.py                    # Backend Flask (API)
│   ├── requirements.txt          # Dependências Python
│   ├── Procfile                  # Render
│   ├── render.yaml              # Render pipeline
│   ├── .env.example             # Variáveis de ambiente
│   └── .gitignore               # Git ignore
│
├── 🌐 FRONTEND
│   ├── index.html               # Login
│   ├── dashboard.html           # Dashboard
│   ├── secretarias.html         # Secretarias
│   ├── unidades.html            # Unidades
│   ├── itens.html               # Itens patrimoniais
│   ├── auth.js                  # Autenticação
│   ├── main.js                  # App principal
│   └── utils.js                 # Utilitários
│
├── 🎨 ESTILOS
│   └── css/
│       ├── main.css
│       ├── login.css
│       ├── dashboard.css
│       └── ...
│
├── 💾 BANCO DE DADOS
│   └── sql/
│       ├── banco.sql            # Estrutura (criar tabelas)
│       └── dados-iniciais.sql   # Dados de teste
│
├── 📋 DOCUMENTAÇÃO
│   ├── README.md                # Este arquivo
│   ├── DEPLOY_RENDER.md         # Guia deploy Render
│   ├── SETUP_LOGIN.md           # Setup do login
│   └── AUTENTICACAO.md          # Detalhes autenticação
│
└── 🧪 TESTES
    ├── test_login.py            # Teste autenticação
    ├── test_diagnostico.py      # Diagnóstico completo
    ├── test_db_connection.py    # Teste banco de dados
    └── check_render_ready.py    # Verificar prontidão Render
```

---

## 🔐 Credenciais de Teste

Após executar os scripts SQL, use:

```
Email: admin@gmail.com
Senha: admin123
Perfil: Admin
```

**Outras contas de teste:**
- `gestor@example.com` / `gestor123` (Gestor)
- `usuario@example.com` / `usuario123` (Usuário)

---

## 🌍 Variáveis de Ambiente

Copie `.env.example` para `.env` e configure conforme seu ambiente:

```bash
# Banco de dados
DATABASE_URL=mysql+pymysql://usuario:senha@host:porta/banco
DB_USER=2026Iventario
DB_PASSWORD=Inventa@2026
DB_HOST=200.131.251.11
DB_PORT=3341
DB_NAME=2026ProjetoInv

# Flask
FLASK_ENV=development
FLASK_DEBUG=True

# Porta
PORT=5000
```

---

## 📡 API Endpoints

### Autenticação

**POST** `/api/login_simple`
```json
Request:
{
  "email": "admin@gmail.com",
  "senha": "admin123"
}

Response:
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

**POST** `/api/logout`
```json
Response:
{
  "success": true,
  "message": "Logout realizado com sucesso!"
}
```

**GET** `/api/healthcheck`
```json
Response:
{
  "server": "online",
  "database": "online"
}
```

---

## 🧪 Testes

### Testar autenticação

```bash
python test_login.py
```

Resultado esperado:
```
✓ TODOS OS TESTES PASSARAM!
```

### Diagnóstico completo

```bash
python test_diagnostico.py
```

Testa:
1. ✅ Servidor Flask rodando
2. ✅ Banco de dados acessível
3. ✅ Usuários cadastrados
4. ✅ Endpoint de login funcionando

### Verificar banco de dados

```bash
python test_db_connection.py
```

### Verificar prontidão para Render

```bash
python check_render_ready.py
```

---

## 🐛 Troubleshooting

### ❌ Erro: "JSON.parse: unexpected end of data"

**Causa:** Servidor não está retornando JSON válido

**Solução:**
```bash
# Verifique se app.py está rodando
python app.py

# Teste a conexão
curl http://localhost:5000/api/healthcheck
```

### ❌ Erro: "E-mail ou senha incorretos"

**Causa:** Dados não foram inseridos no banco

**Solução:**
```bash
# Verifique se os usuários existem
mysql -h 200.131.251.11 -u 2026Iventario -p'Inventa@2026' 2026ProjetoInv -e "SELECT * FROM usuario;"

# Se não aparecer nada, execute novamente:
mysql -h 200.131.251.11 -u 2026Iventario -p'Inventa@2026' 2026ProjetoInv < sql/dados-iniciais.sql
```

### ❌ Página em refresh infinito

**Solução:** ✓ Já foi corrigido! `main.js` não carrega mais em `index.html`

### ❌ Não consegue conectar ao banco

**Verifique:**
- Host: `200.131.251.11`
- Porta: `3341`
- Usuário: `2026Iventario`
- Senha: `Inventa@2026`

**Teste direto:**
```bash
mysql -h 200.131.251.11 -P 3341 -u 2026Iventario -p
# Digite a senha: Inventa@2026
```

---

## 💡 Desenvolvimento

### Adicionar novo endpoint

```python
# Em app.py

@app.route('/api/secretarias', methods=['GET'])
def listar_secretarias():
    try:
        secretarias = Secretaria.query.all()
        return jsonify({
            'success': True,
            'secretarias': [s.to_dict() for s in secretarias]
        })
    except Exception as e:
        return jsonify({'success': False, 'message': str(e)}), 500
```

### Adicionar nova página

1. Criar arquivo `nova-pagina.html` na raiz
2. Incluir os scripts: `main.js`, `utils.js`
3. Adicionar item no menu de `dashboard.html`

---

## 📝 Documentação Completa

- [DEPLOY_RENDER.md](DEPLOY_RENDER.md) - **Guia passo a passo para deploy no Render**
- [SETUP_LOGIN.md](SETUP_LOGIN.md) - Setup completo do sistema de login
- [AUTENTICACAO.md](AUTENTICACAO.md) - Detalhes técnicos da autenticação

---

## ⚡ Deploy Rápido

```bash
# 1. Fazer commit e push
git add .
git commit -m "Preparar para deploy"
git push origin main

# 2. Acessar: https://render.com
# 3. Conectar repositório
# 4. Configurar Environment Variables
# 5. Deploy automático!
```

**[Ver guia completo de deployment →](DEPLOY_RENDER.md)**

---

## 👥 Desenvolvimento

- **Desenvolvedor:** Isabella Vieira
- **Data:** 01/06/2026
- **Status:** ✅ Pronto para produção

---

## 📄 Licença

Projeto Integrador - IFSudesteMG

---

**Última atualização:** 01/06/2026  
**Versão:** 1.0  
**Status:** ✅ PRONTO PARA DEPLOY
