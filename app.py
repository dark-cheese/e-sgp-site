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
        # Busca o usuário e traz também o nome do nível/tipo de usuário através do JOIN
        query = '''
            SELECT u.id, u.nome, u.email, u.senha, tu.nome AS nivel
            FROM usuario u
            JOIN tipo_usuario tu ON u.tipoUsuarioId = tu.id
            WHERE u.email = %s 
            LIMIT 1
        '''
        usuario = query_one(conn, query, (email,))

        # Se o usuário existir e a senha informada for igual à do banco
        if usuario and usuario['senha'] == senha:
            return jsonify({
                'success': True,
                'message': 'Login realizado com sucesso!',
                'usuario': {
                    'id': usuario['id'],
                    'nome': usuario['nome'],
                    'email': usuario['email'],
                    'nivel': usuario['nivel']  # Retorna 'admin', 'gestor', etc.
                }
            })

        # Por segurança, usamos a mesma mensagem caso o e-mail não exista ou a senha esteja errada
        return jsonify({'success': False, 'message': 'E-mail ou senha incorretos!'}), 401

    except Error as exc:
        return jsonify({'success': False, 'message': f'Erro interno no servidor: {exc}'}), 500
    finally:
        conn.close()