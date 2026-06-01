import os
from flask import Flask, jsonify, request
from flask_sqlalchemy import SQLAlchemy
from flask_cors import CORS

app = Flask(__name__)
CORS(app)  # Ativa o CORS para todo o projeto automaticamente

# Configuração automática do Banco de Dados usando SQLAlchemy
# Se não encontrar a variável no Render, usa o seu banco institucional padrão
app.config['SQLALCHEMY_DATABASE_URI'] = os.environ.get(
    'DATABASE_URL', 
    'mysql+mysqlconnector://2026Iventario:Inventa%402026@200.131.251.11:3341/2026ProjetoInv'
)
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False

db = SQLAlchemy(app)

# ============================================================
# MODELO DO BANCO DE DADOS (Substitui as queries manuais)
# ============================================================
class Usuario(db.Model):
    __tablename__ = 'usuario'
    id = db.Column(db.Integer, primary_key=True)
    nome = db.Column(db.String(100))
    email = db.Column(db.String(100), unique=True)
    senha = db.Column(db.String(100))

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
    data = request.get_json(silent=True) or {}
    email = data.get('email', '').strip()
    senha = data.get('senha', '')

    if not email or not senha:
        return jsonify({'success': False, 'message': 'E-mail e senha são obrigatórios!'}), 400

    # Busca o usuário de forma simples, sem escrever SQL
    usuario = Usuario.query.filter_by(email=email).first()

    if usuario and usuario.senha == senha:
        return jsonify({
            'success': True,
            'message': 'Login realizado com sucesso!',
            'usuario': {
                'id': usuario.id,
                'nome': usuario.nome,
                'email': usuario.email
            }
        })

    return jsonify({'success': False, 'message': 'E-mail ou senha incorretos!'}), 401

if __name__ == "__main__":
    port = int(os.environ.get("PORT", 5000))
    app.run(host="0.0.0.0", port=port)
