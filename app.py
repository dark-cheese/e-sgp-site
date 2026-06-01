import os
from flask import Flask, jsonify, request
from flask_sqlalchemy import SQLAlchemy
from flask_cors import CORS
from werkzeug.security import check_password_hash, generate_password_hash
import logging

# Configurar logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

app = Flask(__name__)
CORS(app)  # Ativa o CORS para todo o projeto automaticamente

# ============================================================
# CONFIGURAÇÃO DO BANCO DE DADOS
# ============================================================

# Prioridade de configuração:
# 1. DATABASE_URL (Render ou outra plataforma)
# 2. Variáveis individuais (local ou Render)
# 3. String padrão (local)

DATABASE_URL = os.environ.get('DATABASE_URL')

if DATABASE_URL:
    # Usar DATABASE_URL fornecido
    app.config['SQLALCHEMY_DATABASE_URI'] = DATABASE_URL
    logger.info("✓ Usando DATABASE_URL do ambiente")
else:
    # Construir URL a partir de variáveis individuais
    db_user = os.environ.get('DB_USER', '2026Iventario')
    db_password = os.environ.get('DB_PASSWORD', 'Inventa@2026')
    db_host = os.environ.get('DB_HOST', '200.131.251.11')
    db_port = os.environ.get('DB_PORT', '3341')
    db_name = os.environ.get('DB_NAME', '2026ProjetoInv')
    
    # Verificar se está rodando no Render (sem MySQL local disponível)
    if os.environ.get('RENDER'):
        logger.warning("⚠️  Rodando no Render, mas DATABASE_URL não definida")
        logger.warning("⚠️  Configure DATABASE_URL nas Environment Variables do Render")
    
    # Tentar usar PyMySQL como fallback
    try:
        db_uri = f'mysql+pymysql://{db_user}:{db_password}@{db_host}:{db_port}/{db_name}'
    except:
        db_uri = f'mysql+mysqlconnector://{db_user}:{db_password}@{db_host}:{db_port}/{db_name}'
    
    app.config['SQLALCHEMY_DATABASE_URI'] = db_uri
    logger.info(f"✓ Conectando ao banco: {db_host}:{db_port}/{db_name}")

app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False
app.config['SQLALCHEMY_ENGINE_OPTIONS'] = {
    'connect_args': {'timeout': 10},
    'pool_pre_ping': True,  # Testa conexão antes de usar
    'pool_recycle': 3600,   # Recicla conexões a cada 1 hora
}
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False

db = SQLAlchemy(app)

# ============================================================
# MODELOS DO BANCO DE DADOS
# ============================================================

class TipoUsuario(db.Model):
    """Modelo para tipo de usuário (admin, gestor, usuario)"""
    __tablename__ = 'tipo_usuario'
    id = db.Column(db.Integer, primary_key=True)
    nome = db.Column(db.String(20), unique=True, nullable=False)
    descricao = db.Column(db.String(255))
    
    # Relacionamento reverso
    usuarios = db.relationship('Usuario', backref='tipo', lazy=True)


class Usuario(db.Model):
    """Modelo para usuário do sistema"""
    __tablename__ = 'usuario'
    id = db.Column(db.Integer, primary_key=True, autoincrement=True)
    nome = db.Column(db.String(50), nullable=False)
    email = db.Column(db.String(64), unique=True, nullable=False)
    senha = db.Column(db.String(255), nullable=False)
    tipoUsuarioId = db.Column(db.Integer, db.ForeignKey('tipo_usuario.id'), nullable=False)
    
    def __repr__(self):
        return f'<Usuario {self.email}>'

# ============================================================
# ROTAS DA API
# ============================================================

@app.route('/api/healthcheck', methods=['GET'])
def health_check():
    try:
        # Tenta fazer uma consulta simples para testar o banco
        db.session.execute(db.text('SELECT 1'))
        return jsonify({'server': 'online', 'database': 'online'})
    except Exception as e:
        return jsonify({'server': 'online', 'database': 'offline', 'error': str(e)}), 500

@app.route('/api/login_simple', methods=['POST'])
def login_simple():
    """
    Endpoint de login
    Espera: {"email": "user@example.com", "senha": "password"}
    Retorna: {"success": true/false, "message": "...", "usuario": {...}}
    Testa com: admin@gmail.com / admin123
    """
    try:
        data = request.get_json(silent=True) or {}
        email = data.get('email', '').strip()
        senha = data.get('senha', '')

        # Validações
        if not email or not senha:
            return jsonify({
                'success': False, 
                'message': 'E-mail e senha são obrigatórios!'
            }), 400

        # Buscar usuário no banco de dados (case-insensitive)
        usuario = db.session.query(Usuario).join(TipoUsuario).filter(
            db.func.lower(Usuario.email) == db.func.lower(email)
        ).first()

        if not usuario:
            print(f'Login falhou: Usuário {email} não encontrado')
            return jsonify({
                'success': False, 
                'message': 'E-mail ou senha incorretos!'
            }), 401

        # Comparar senha (suporta hash ou texto plano para compatibilidade)
        senha_valida = False
        if usuario.senha.startswith('pbkdf2:'):  # É um hash do werkzeug
            senha_valida = check_password_hash(usuario.senha, senha)
        else:
            # Compatibilidade com senhas em texto plano (como no banco atual)
            senha_valida = usuario.senha == senha

        if not senha_valida:
            print(f'Login falhou: Senha incorreta para {email}')
            return jsonify({
                'success': False, 
                'message': 'E-mail ou senha incorretos!'
            }), 401

        # Login bem-sucedido
        tipo_perfil = usuario.tipo.nome if usuario.tipo else 'usuario'
        
        print(f'Login bem-sucedido: {usuario.email} ({tipo_perfil})')
        
        return jsonify({
            'success': True,
            'message': 'Login realizado com sucesso!',
            'usuario': {
                'id': usuario.id,
                'nome': usuario.nome,
                'email': usuario.email,
                'perfil': tipo_perfil,
                'tipoUsuarioId': usuario.tipoUsuarioId
            }
        }), 200

    except Exception as e:
        print(f'Erro no login: {str(e)}')
        import traceback
        traceback.print_exc()
        return jsonify({
            'success': False,
            'message': 'Erro ao processar login. Tente novamente.'
        }), 500

@app.route('/api/usuario', methods=['GET'])
def get_usuario_atual():
    """
    Obter dados do usuário atual (para verificar sessão)
    Esperado: Header 'X-User-ID' com o id do usuário
    """
    try:
        user_id = request.headers.get('X-User-ID')
        if not user_id:
            return jsonify({'success': False, 'message': 'Não autenticado'}), 401

        usuario = Usuario.query.get(int(user_id))
        if not usuario:
            return jsonify({'success': False, 'message': 'Usuário não encontrado'}), 404

        tipo_perfil = usuario.tipo.nome if usuario.tipo else 'usuario'

        return jsonify({
            'success': True,
            'usuario': {
                'id': usuario.id,
                'nome': usuario.nome,
                'email': usuario.email,
                'perfil': tipo_perfil,
                'tipoUsuarioId': usuario.tipoUsuarioId
            }
        }), 200
    except Exception as e:
        print(f'Erro ao buscar usuário: {str(e)}')
        return jsonify({'success': False, 'message': str(e)}), 500


@app.route('/api/logout', methods=['POST'])
def logout():
    """
    Endpoint de logout (limpa sessão no cliente via JS)
    """
    return jsonify({
        'success': True,
        'message': 'Logout realizado com sucesso!'
    }), 200


if __name__ == "__main__":
    port = int(os.environ.get("PORT", 5000))
    debug = os.environ.get("FLASK_ENV") == "development"
    
    logger.info(f"🚀 Iniciando servidor e-SGP na porta {port}")
    logger.info(f"🔧 Debug: {debug}")
    
    app.run(
        host="0.0.0.0",
        port=port,
        debug=debug,
        threaded=True
    )

