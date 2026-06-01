# 🚀 Guia de Configuração: Sistema de Login e-SGP

## 📋 Resumo

Sistema de autenticação completo adaptado à estrutura real do seu banco de dados, com suporte a tipos de usuário (admin, gestor, usuario).

---

## 🔧 Passo 1: Preparar o Banco de Dados

### 1.1 Criar o banco e as tabelas

Entre no seu MySQL e execute o arquivo de banco de dados:

```bash
mysql -h 200.131.251.11 -P 3341 -u 2026Iventario -p'Inventa@2026' < sql/banco.sql
```

Ou use uma ferramenta SQL como MySQL Workbench e importe o arquivo `sql/banco.sql`.

### 1.2 Inserir dados iniciais

Após criar o banco, execute os dados de teste:

```bash
mysql -h 200.131.251.11 -P 3341 -u 2026Iventario -p'Inventa@2026' 2026ProjetoInv < sql/dados-iniciais.sql
```

Ou importe `sql/dados-iniciais.sql` via MySQL Workbench.

---

## 👥 Usuários Padrão para Teste

Após executar os scripts SQL, você terá os seguintes usuários:

| Email | Senha | Perfil | Uso |
|-------|-------|--------|-----|
| `admin@gmail.com` | `admin123` | Admin | Acesso total ao sistema |
| `gestor@example.com` | `gestor123` | Gestor | Criar usuários e estruturas |
| `usuario@example.com` | `usuario123` | Usuário | Cadastrar itens |

⚠️ **Importante**: Estes são usuários de **teste**. Em produção, altere as senhas!

---

## 🐍 Passo 2: Configurar Python e Flask

### 2.1 Instalar dependências

```bash
pip install -r requirements.txt
```

Certifique-se de que tem instalado:
- Flask
- Flask-SQLAlchemy
- flask-cors
- mysql-connector-python
- werkzeug

### 2.2 Verificar arquivo app.py

Confirme que o arquivo `app.py` contém:

```python
# Modelos atualizados
class TipoUsuario(db.Model):
    __tablename__ = 'tipo_usuario'
    # ... com relacionamento com Usuario

class Usuario(db.Model):
    __tablename__ = 'usuario'
    tipoUsuarioId = db.Column(db.Integer, db.ForeignKey('tipo_usuario.id'))
    # ... relação com tipo de usuário
```

---

## 🌐 Passo 3: Iniciar o Servidor

```bash
python app.py
```

Você deve ver:

```
 * Running on http://0.0.0.0:5000
 * WARNING in app.run_simple: This is a development server...
```

---

## 🧪 Passo 4: Testar o Sistema

### 4.1 Via Script Python

Execute o teste automatizado:

```bash
python test_login.py
```

Deve exibir:

```
✓ HEALTHCHECK
✓ LOGIN VÁLIDO
✓ LOGIN INVÁLIDO
✓ CAMPOS VAZIOS
✓ LOGOUT
✓ TODOS OS TESTES PASSARAM!
```

### 4.2 Via Browser

Abra no navegador:

```
http://localhost:5000/index.html
```

Faça login com:
- **Email**: `admin@gmail.com`
- **Senha**: `admin123`

Se bem-sucedido, será redirecionado ao dashboard.

### 4.3 Via cURL

Teste o endpoint de login:

```bash
curl -X POST http://localhost:5000/api/login_simple \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@gmail.com","senha":"admin123"}'
```

Resposta esperada:

```json
{
  "success": true,
  "message": "Login realizado com sucesso!",
  "usuario": {
    "id": 1,
    "nome": "Isabella",
    "email": "admin@gmail.com",
    "perfil": "admin",
    "tipoUsuarioId": 1
  }
}
```

---

## 📊 Estrutura do Banco de Dados (Resumed)

```
┌─ tipo_usuario
│  ├── id (PK)
│  ├── nome (admin, gestor, usuario)
│  └── descricao
│
├─ usuario (FK: tipoUsuarioId)
│  ├── id (PK)
│  ├── nome
│  ├── email
│  ├── senha
│  └── tipoUsuarioId
│
├─ responsavel
│  ├── id (PK)
│  ├── usuarioId (FK)
│  ├── nome
│  └── cargo
│
├─ secretaria
├─ unidade
├─ departamento
├─ localizacao
├─ item
├─ movimentacao
├─ termo_responsabilidade
├─ inventario
├─ inventario_item
├─ baixa
└─ historico
```

---

## 🎯 Fluxo de Login

```
1. Usuário acessa /index.html
2. Preenche email e senha
3. JavaScript envia requisição POST para /api/login_simple
4. Backend valida credenciais no banco
5. Se válido:
   → Retorna dados do usuário (nome, perfil, tipo)
   → JavaScript armazena em sessionStorage
   → Redireciona para dashboard.html
6. Se inválido:
   → Retorna erro 401
   → JavaScript exibe mensagem de erro
```

---

## 🔐 Segurança

✅ **Implementado:**
- Validação de entrada (email e senha obrigatórios)
- Suporte a senhas em texto plano (compatível com seu banco)
- Suporte a hash de senha (werkzeug) para futuro
- CORS habilitado para frontend
- sessionStorage para tokens de sessão
- Proteção de rotas (redirecionamento ao login)
- Logs de tentativas de login

---

## 🐛 Troubleshooting

### Erro: "E-mail ou senha incorretos!" mesmo com credenciais corretas

**Solução:**
1. Verifique se o banco foi criado corretamente
2. Verifique se os dados iniciais foram inseridos
3. Confira o email e senha exatos no banco:
   ```sql
   SELECT email, senha FROM usuario;
   ```

### Erro: "Erro ao processar login"

**Solução:**
1. Verifique o console do Flask para logs detalhados
2. Certifique-se de que a conexão MySQL está funcionando
3. Execute `python test_db_connection.py` para verificar banco

### Erro: CORS

**Solução:**
CORS já está configurado em `app.py`:
```python
CORS(app)  # Ativa o CORS para todo o projeto
```

Se ainda receber erro, verifique se o Flask está rodando com CORS habilitado.

### Erro: "Banco de dados offline"

**Solução:**
1. Verifique credenciais de conexão em `app.py`
2. Execute `python test_db_connection.py`
3. Confirme que o MySQL está rodando

---

## 📱 Próximos Passos

1. ✅ Login funcionando
2. ⬜ Integrar com páginas do dashboard
3. ⬜ Criar endpoints para gerenciar itens
4. ⬜ Implementar histórico de ações
5. ⬜ Adicionar campos de busca e filtros

---

## 📞 Suporte

Se tiver problemas:

1. Verifique todos os passos acima
2. Consulte os logs do console
3. Execute os testes automatizados
4. Verifique a conectividade com MySQL

---

## 📚 Arquivos Principais

- `app.py` - Backend Flask com endpoints de autenticação
- `auth.js` - Frontend: lógica de login
- `main.js` - Frontend: funções gerais
- `utils.js` - Frontend: utilitários
- `sql/banco.sql` - Criação de tabelas
- `sql/dados-iniciais.sql` - Dados de teste
- `test_login.py` - Testes automatizados
- `test_db_connection.py` - Teste de conexão com banco

---

**Versão:** 1.0  
**Data:** 1 de junho de 2026  
**Sistema:** e-SGP (Sistema de Gestão de Patrimônio)
