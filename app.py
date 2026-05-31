# ============================================================
# e-SGP (Sistema de Gestão de Patrimônio)
# Backend em Python com Flask
# ============================================================

from datetime import date
from flask import Flask, jsonify, request, send_from_directory
import mysql.connector
from mysql.connector import Error
from config import DB_CONFIG

# Inicializar a aplicação Flask
# static_folder='.' permite servir arquivos HTML/CSS/JS do mesmo diretório
app = Flask(__name__, static_folder='.', static_url_path='')



# ============================================================
# FUNÇÕES DE CONEXÃO E MANIPULAÇÃO DE BANCO DE DADOS
# ============================================================

def get_db_connection():
    """
    Estabelece conexão com o banco de dados MySQL.
    Retorna: objeto de conexão ou None em caso de erro
    """
    try:
        return mysql.connector.connect(
            host=DB_CONFIG['host'],
            port=DB_CONFIG['port'],
            user=DB_CONFIG['user'],
            password=DB_CONFIG['password'],
            database=DB_CONFIG['database'],
            charset=DB_CONFIG['charset'],
            use_unicode=DB_CONFIG['use_unicode'],
        )
    except Error as exc:
        app.logger.error(f'Erro de conexão: {exc}')
        return None


def query_all(conn, query, params=None):
    """
    Executa uma consulta SQL e retorna TODOS os resultados como lista de dicionários.
    Parâmetros: conn (conexão), query (SQL), params (argumentos da query)
    """
    cursor = conn.cursor(dictionary=True)
    cursor.execute(query, params or ())
    rows = cursor.fetchall()
    cursor.close()
    return rows


def query_one(conn, query, params=None):
    """
    Executa uma consulta SQL e retorna apenas o PRIMEIRO resultado.
    Parâmetros: conn (conexão), query (SQL), params (argumentos da query)
    """
    cursor = conn.cursor(dictionary=True)
    cursor.execute(query, params or ())
    row = cursor.fetchone()
    cursor.close()
    return row


def execute_insert(conn, query, params=None):
    """
    Executa um INSERT/UPDATE/DELETE e faz commit automático.
    Retorna: o ID da última linha inserida (lastrowid)
    """
    cursor = conn.cursor()
    cursor.execute(query, params or ())
    conn.commit()
    last_id = cursor.lastrowid
    cursor.close()
    return last_id


def register_history(conn, action, table_target, record_id, description):
    """
    Registra uma ação no histórico do sistema.
    Parâmetros: ação (CRIAR/EDITAR/DELETAR), tabela afetada, ID do registro, descrição
    """
    try:
        query = '''
            INSERT INTO historico (usuarioId, acao, tabelaAlvo, registroId, descricao, dataRegistro)
            VALUES (%s, %s, %s, %s, %s, %s)
        '''
        execute_insert(conn, query, (
            1,
            action,
            table_target,
            record_id,
            description,
            date.today().isoformat()
        ))
    except Error as exc:
        app.logger.error(f'Erro ao registrar histórico: {exc}')



# ============================================================
# MIDDLEWARE - CORS (Cross-Origin Resource Sharing)
# ============================================================

@app.after_request
def add_cors_headers(response):
    """
    Adiciona headers CORS para permitir requisições de qualquer origem.
    Isso permite que o frontend faça fetch para qualquer domínio.
    """
    response.headers['Access-Control-Allow-Origin'] = '*'
    response.headers['Access-Control-Allow-Headers'] = 'Content-Type'
    response.headers['Access-Control-Allow-Methods'] = 'GET,POST,OPTIONS'
    return response


@app.route('/api/<path:endpoint>', methods=['OPTIONS'])
def handle_options(endpoint):
    """
    Trata requisições OPTIONS (preflight) do navegador.
    Necessário para CORS funcionar corretamente.
    """
    return '', 200


# ============================================================
# ROTAS DA API
# ============================================================

@app.route('/api/login_simple', methods=['POST'])
def login_simple():
    data = request.get_json(silent=True) or {}
    email = data.get('email', '').strip()
    senha = data.get('senha', '')

    if not email or not senha:
        return jsonify({'success': False, 'message': 'E-mail e senha são obrigatórios!'}), 400

    conn = get_db_connection()
    if not conn:
        return jsonify({'success': False, 'message': 'Falha ao conectar com o banco de dados.'}), 500

    try:
        # Busca o usuário no banco e resolve o nível dinamicamente através do tipo_usuario
        query = '''
            SELECT u.id, u.nome, u.email, u.senha, tu.nome AS nivel
            FROM usuario u
            JOIN tipo_usuario tu ON u.tipoUsuarioId = tu.id
            WHERE u.email = %s 
            LIMIT 1
        '''
        usuario = query_one(conn, query, (email,))

        # Se o usuário existir e a senha informada for idêntica à do banco
        if usuario and usuario['senha'] == senha:
            return jsonify({
                'success': True,
                'message': 'Login realizado com sucesso!',
                'usuario': {
                    'id': usuario['id'],
                    'nome': usuario['nome'],
                    'email': usuario['email'],
                    'nivel': usuario['nivel']
                }
            })

        return jsonify({'success': False, 'message': 'E-mail ou senha incorretos!'}), 401

    except Error as exc:
        return jsonify({'success': False, 'message': f'Erro interno ao processar login: {exc}'}), 500
    finally:
        conn.close()


@app.route('/api/secretarias', methods=['GET', 'POST'])
def secretarias():
    conn = get_db_connection()
    if not conn:
        return jsonify({'success': False, 'message': 'Falha ao conectar com o banco de dados.'}), 500

    try:
        if request.method == 'GET':
            query = '''
                SELECT s.id, s.nome, s.descricao, s.responsavelId, r.nome AS responsavel,
                    (SELECT COUNT(*) FROM unidade u WHERE u.secretariaId = s.id) AS unidades,
                    (SELECT COUNT(*) FROM item i
                        JOIN departamento d ON i.departamentoId = d.id
                        JOIN unidade u2 ON d.unidadeId = u2.id
                        WHERE u2.secretariaId = s.id) AS itens
                FROM secretaria s
                LEFT JOIN responsavel r ON s.responsavelId = r.id
                ORDER BY s.nome
            '''
            secretarias = query_all(conn, query)
            return jsonify({'success': True, 'data': secretarias})

        data = request.get_json(silent=True) or {}
        nome = (data.get('nome') or '').strip()
        descricao = (data.get('descricao') or '').strip() or None
        responsavel_id = data.get('responsavelId')
        responsavel_id = int(responsavel_id) if responsavel_id not in (None, '', []) else None

        if nome == '':
            return jsonify({'success': False, 'message': 'O nome da secretaria é obrigatório.'}), 400
        if responsavel_id is None:
            return jsonify({'success': False, 'message': 'O responsável é obrigatório.'}), 400

        query = '''
            INSERT INTO secretaria (nome, descricao, responsavelId)
            VALUES (%s, %s, %s)
        '''
        insert_id = execute_insert(conn, query, (nome, descricao, responsavel_id))
        register_history(conn, 'CRIAR', 'secretaria', insert_id, f'Cadastrou nova secretaria: {nome}')
        return jsonify({'success': True, 'message': 'Secretaria cadastrada com sucesso.', 'id': insert_id})
    except Error as exc:
        return jsonify({'success': False, 'message': f'Erro ao processar solicitação de secretarias: {exc}'}), 500
    finally:
        conn.close()


@app.route('/api/unidades', methods=['GET', 'POST'])
def unidades():
    conn = get_db_connection()
    if not conn:
        return jsonify({'success': False, 'message': 'Falha ao conectar com o banco de dados.'}), 500

    try:
        if request.method == 'GET':
            query = '''
                SELECT u.id, u.nome, u.endereco, u.secretariaId, u.responsavelId,
                    s.nome AS secretaria,
                    r.nome AS responsavel,
                    (SELECT COUNT(*) FROM departamento d WHERE d.unidadeId = u.id) AS departamentos,
                    (SELECT COUNT(*) FROM item i JOIN departamento d ON i.departamentoId = d.id WHERE d.unidadeId = u.id) AS itens
                FROM unidade u
                LEFT JOIN secretaria s ON u.secretariaId = s.id
                LEFT JOIN responsavel r ON u.responsavelId = r.id
                ORDER BY u.nome
            '''
            data = query_all(conn, query)
            return jsonify({'success': True, 'data': data})

        data = request.get_json(silent=True) or {}
        nome = (data.get('nome') or '').strip()
        secretaria_id = int(data.get('secretariaId') or 0)
        endereco = (data.get('endereco') or '').strip() or None
        responsavel_id = data.get('responsavelId')
        responsavel_id = int(responsavel_id) if responsavel_id not in (None, '', []) else None

        if nome == '':
            return jsonify({'success': False, 'message': 'O nome da unidade é obrigatório.'}), 400
        if secretaria_id <= 0:
            return jsonify({'success': False, 'message': 'A secretaria é obrigatória.'}), 400

        query = '''
            INSERT INTO unidade (nome, secretariaId, endereco, responsavelId)
            VALUES (%s, %s, %s, %s)
        '''
        insert_id = execute_insert(conn, query, (nome, secretaria_id, endereco, responsavel_id))
        register_history(conn, 'CRIAR', 'unidade', insert_id, f'Cadastrou nova unidade: {nome}')
        return jsonify({'success': True, 'message': 'Unidade cadastrada com sucesso.', 'id': insert_id})
    except Error as exc:
        return jsonify({'success': False, 'message': f'Erro ao processar solicitação de unidades: {exc}'}), 500
    finally:
        conn.close()


@app.route('/api/locais', methods=['GET', 'POST'])
def locais():
    conn = get_db_connection()
    if not conn:
        return jsonify({'success': False, 'message': 'Falha ao conectar com o banco de dados.'}), 500

    try:
        if request.method == 'GET':
            query = '''
                SELECT d.id, d.nome, d.unidadeId, d.responsavelId,
                    u.nome AS unidade,
                    r.nome AS responsavel,
                    (SELECT COUNT(*) FROM item i WHERE i.departamentoId = d.id) AS itens
                FROM departamento d
                LEFT JOIN unidade u ON d.unidadeId = u.id
                LEFT JOIN responsavel r ON d.responsavelId = r.id
                ORDER BY d.nome
            '''
            data = query_all(conn, query)
            return jsonify({'success': True, 'data': data})

        data = request.get_json(silent=True) or {}
        nome = (data.get('nome') or '').strip()
        unidade_id = int(data.get('unidadeId') or 0)
        responsavel_id = data.get('responsavelId')
        responsavel_id = int(responsavel_id) if responsavel_id not in (None, '', []) else None

        if nome == '':
            return jsonify({'success': False, 'message': 'O nome do departamento é obrigatório.'}), 400
        if unidade_id <= 0:
            return jsonify({'success': False, 'message': 'A unidade é obrigatória.'}), 400

        query = '''
            INSERT INTO departamento (nome, unidadeId, responsavelId)
            VALUES (%s, %s, %s)
        '''
        insert_id = execute_insert(conn, query, (nome, unidade_id, responsavel_id))
        register_history(conn, 'CRIAR', 'departamento', insert_id, f'Cadastrou novo departamento: {nome}')
        return jsonify({'success': True, 'message': 'Departamento cadastrado com sucesso.', 'id': insert_id})
    except Error as exc:
        return jsonify({'success': False, 'message': f'Erro ao processar solicitação de departamentos: {exc}'}), 500
    finally:
        conn.close()


@app.route('/api/responsaveis', methods=['GET', 'POST'])
def responsaveis():
    conn = get_db_connection()
    if not conn:
        return jsonify({'success': False, 'message': 'Falha ao conectar com o banco de dados.'}), 500

    try:
        if request.method == 'GET':
            query = '''
                SELECT r.id, r.nome, r.cargo, u.email AS email
                FROM responsavel r
                LEFT JOIN usuario u ON r.usuarioId = u.id
                ORDER BY r.nome
            '''
            data = query_all(conn, query)
            return jsonify({'success': True, 'data': data})

        data = request.get_json(silent=True) or {}
        nome = (data.get('nome') or '').strip()
        cargo = (data.get('cargo') or '').strip()
        email = (data.get('email') or '').strip() or None
        usuario_id = None

        if nome == '' or cargo == '':
            return jsonify({'success': False, 'message': 'Nome e cargo são obrigatórios para cadastrar o responsável.'}), 400

        if email:
            try:
                user = query_one(conn, 'SELECT id FROM usuario WHERE email = %s LIMIT 1', (email,))
                if user:
                    usuario_id = user['id']
            except Error:
                usuario_id = None

        if usuario_id:
            query = 'INSERT INTO responsavel (nome, cargo, usuarioId) VALUES (%s, %s, %s)'
            params = (nome, cargo, usuario_id)
        else:
            query = 'INSERT INTO responsavel (nome, cargo) VALUES (%s, %s)'
            params = (nome, cargo)

        insert_id = execute_insert(conn, query, params)
        register_history(conn, 'CRIAR', 'responsavel', insert_id, f'Cadastrou novo responsável: {nome}')
        return jsonify({'success': True, 'message': 'Responsável cadastrado com sucesso.', 'id': insert_id})
    except Error as exc:
        return jsonify({'success': False, 'message': f'Erro ao processar solicitação de responsáveis: {exc}'}), 500
    finally:
        conn.close()


@app.route('/api/itens', methods=['GET', 'POST'])
def itens():
    conn = get_db_connection()
    if not conn:
        return jsonify({'success': False, 'message': 'Falha ao conectar com o banco de dados.'}), 500

    try:
        if request.method == 'GET':
            query = '''
                SELECT i.id, i.numeroPatrimonio, i.descricao, i.marca, i.modelo, i.numeroSerie, i.estado,
                    i.valor, i.notaFiscal, i.dataAquisicao, i.observacoes, i.departamentoId, i.localizacaoId, i.responsavelId, i.tipoMaterialId,
                    d.nome AS departamento,
                    u.nome AS unidade,
                    r.nome AS responsavel,
                    tm.nome AS tipoMaterial
                FROM item i
                LEFT JOIN departamento d ON i.departamentoId = d.id
                LEFT JOIN unidade u ON d.unidadeId = u.id
                LEFT JOIN responsavel r ON i.responsavelId = r.id
                LEFT JOIN tipo_material tm ON i.tipoMaterialId = tm.id
                ORDER BY i.numeroPatrimonio
            '''
            data = query_all(conn, query)
            return jsonify({'success': True, 'data': data})

        data = request.get_json(silent=True) or {}
        descricao = (data.get('descricao') or '').strip()
        tipo_material_id = int(data.get('tipoMaterialId') or 0)
        estado = (data.get('estado') or '').strip()
        departamento_id = int(data.get('departamentoId') or 0)
        localizacao_id = data.get('localizacaoId')
        localizacao_id = int(localizacao_id) if localizacao_id not in (None, '', []) else None
        responsavel_id = data.get('responsavelId')
        responsavel_id = int(responsavel_id) if responsavel_id not in (None, '', []) else None
        marca = (data.get('marca') or '').strip() or None
        modelo = (data.get('modelo') or '').strip() or None
        numero_serie = (data.get('numeroSerie') or '').strip() or None
        data_aquisicao = (data.get('dataAquisicao') or '').strip() or None
        valor = data.get('valor')
        valor = valor if valor not in (None, '') else None
        nota_fiscal = (data.get('notaFiscal') or '').strip() or None
        observacoes = (data.get('observacoes') or '').strip() or None

        if descricao == '':
            return jsonify({'success': False, 'message': 'A descrição do item é obrigatória.'}), 400
        if tipo_material_id <= 0:
            return jsonify({'success': False, 'message': 'O tipo de material é obrigatório.'}), 400
        if estado == '':
            return jsonify({'success': False, 'message': 'O estado do item é obrigatório.'}), 400
        if departamento_id <= 0:
            return jsonify({'success': False, 'message': 'O departamento é obrigatório.'}), 400

        numero_patrimonio = f'PATR{date.today().strftime("%Y%m%d")}{hash(descricao) % 1000:03d}'
        query = '''
            INSERT INTO item (numeroPatrimonio, descricao, marca, modelo, numeroSerie, estado, dataAquisicao, valor, notaFiscal, foto, observacoes, departamentoId, localizacaoId, tipoMaterialId, responsavelId)
            VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
        '''
        insert_id = execute_insert(conn, query, (
            numero_patrimonio,
            descricao,
            marca,
            modelo,
            numero_serie,
            estado,
            data_aquisicao,
            valor,
            nota_fiscal,
            None,
            observacoes,
            departamento_id,
            localizacao_id,
            tipo_material_id,
            responsavel_id
        ))
        register_history(conn, 'CRIAR', 'item', insert_id, f'Cadastrou novo item: {numero_patrimonio} - {descricao}')
        return jsonify({'success': True, 'message': 'Item cadastrado com sucesso.', 'id': insert_id, 'numeroPatrimonio': numero_patrimonio})
    except Error as exc:
        return jsonify({'success': False, 'message': f'Erro ao processar solicitação de itens: {exc}'}), 500
    finally:
        conn.close()


@app.route('/api/inventarios', methods=['GET', 'POST'])
def inventarios():
    conn = get_db_connection()
    if not conn:
        return jsonify({'success': False, 'message': 'Falha ao conectar com o banco de dados.'}), 500

    try:
        if request.method == 'GET':
            query = '''
                SELECT i.id, i.ano, i.nome, i.dataInicio, i.dataFim, i.status, i.unidadeId,
                    u.nome AS unidade
                FROM inventario i
                LEFT JOIN unidade u ON i.unidadeId = u.id
                ORDER BY i.ano DESC, i.nome
            '''
            data = query_all(conn, query)
            return jsonify({'success': True, 'data': data})

        data = request.get_json(silent=True) or {}
        nome = (data.get('nome') or '').strip()
        ano = int(data.get('ano') or 0)
        unidade_id = int(data.get('unidadeId') or 0)
        data_inicio = (data.get('dataInicio') or '').strip() or None
        status = (data.get('status') or '').strip().upper()
        data_fim = (data.get('dataFim') or '').strip() or None

        if nome == '' or ano <= 0 or unidade_id <= 0 or status == '':
            return jsonify({'success': False, 'message': 'Nome, ano, unidade e status são obrigatórios.'}), 400
        if status not in ['ABERTO', 'CONCLUIDO', 'SUSPENSO']:
            return jsonify({'success': False, 'message': 'Status inválido.'}), 400

        query = '''
            INSERT INTO inventario (ano, nome, dataInicio, dataFim, status, unidadeId)
            VALUES (%s, %s, %s, %s, %s, %s)
        '''
        insert_id = execute_insert(conn, query, (ano, nome, data_inicio, data_fim, status, unidade_id))
        register_history(conn, 'CRIAR', 'inventario', insert_id, f'Cadastrou novo inventário: {nome} ({ano})')
        return jsonify({'success': True, 'message': 'Inventário cadastrado com sucesso.', 'id': insert_id})
    except Error as exc:
        return jsonify({'success': False, 'message': f'Erro ao processar solicitação de inventários: {exc}'}), 500
    finally:
        conn.close()


@app.route('/api/baixas', methods=['GET', 'POST'])
def baixas():
    conn = get_db_connection()
    if not conn:
        return jsonify({'success': False, 'message': 'Falha ao conectar com o banco de dados.'}), 500

    try:
        if request.method == 'GET':
            query = '''
                SELECT b.id, b.itemId, b.tipo, b.dataBaixa, b.justificativa, b.documento,
                    i.numeroPatrimonio AS itemNumero, i.descricao AS itemDescricao
                FROM baixa b
                LEFT JOIN item i ON b.itemId = i.id
                ORDER BY b.dataBaixa DESC
            '''
            data = query_all(conn, query)
            return jsonify({'success': True, 'data': data})

        data = request.get_json(silent=True) or {}
        item_id = int(data.get('itemId') or 0)
        tipo = (data.get('tipo') or '').strip()
        data_baixa = (data.get('dataBaixa') or '').strip()
        justificativa = (data.get('justificativa') or '').strip()
        documento = (data.get('documento') or '').strip() or None

        if item_id <= 0:
            return jsonify({'success': False, 'message': 'O item é obrigatório.'}), 400
        if tipo == '':
            return jsonify({'success': False, 'message': 'O tipo de baixa é obrigatório.'}), 400
        if data_baixa == '':
            return jsonify({'success': False, 'message': 'A data da baixa é obrigatória.'}), 400
        if justificativa == '':
            return jsonify({'success': False, 'message': 'A justificativa é obrigatória.'}), 400

        query = '''
            INSERT INTO baixa (itemId, tipo, dataBaixa, justificativa, documento)
            VALUES (%s, %s, %s, %s, %s)
        '''
        insert_id = execute_insert(conn, query, (item_id, tipo, data_baixa, justificativa, documento))
        register_history(conn, 'CRIAR', 'baixa', insert_id, f'Registrou baixa do item ID: {item_id} tipo: {tipo}')
        return jsonify({'success': True, 'message': 'Baixa registrada com sucesso.', 'id': insert_id})
    except Error as exc:
        return jsonify({'success': False, 'message': f'Erro ao processar solicitação de baixas: {exc}'}), 500
    finally:
        conn.close()


@app.route('/api/historicos', methods=['GET'])
def historicos():
    conn = get_db_connection()
    if not conn:
        return jsonify({'success': False, 'message': 'Falha ao conectar com o banco de dados.'}), 500

    try:
        query = '''
            SELECT h.id, h.usuarioId, h.acao, h.tabelaAlvo, h.registroId, h.descricao, h.dataRegistro,
                u.nome AS usuario
            FROM historico h
            LEFT JOIN usuario u ON h.usuarioId = u.id
            ORDER BY h.dataRegistro DESC
        '''
        data = query_all(conn, query)
        return jsonify({'success': True, 'data': data})
    except Error as exc:
        return jsonify({'success': False, 'message': f'Erro ao buscar histórico: {exc}'}), 500
    finally:
        conn.close()


# ============================================================
# ROTA DE ARQUIVOS ESTÁTICOS (FRONTEND)
# ============================================================

@app.route('/', defaults={'path': 'index.html'})
@app.route('/<path:path>')
def serve_static(path):
    return send_from_directory('.', path)


if __name__ == '__main__':
    app.run(host='0.0.0.0', port=8000, debug=True)