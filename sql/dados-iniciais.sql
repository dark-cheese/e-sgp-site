-- ============================================================
-- DADOS INICIAIS PARA O SISTEMA e-SGP
-- ============================================================
-- Execute este arquivo APÓS executar banco.sql para popular dados padrão
-- ============================================================

-- Usuários de teste
INSERT INTO usuario (nome, email, senha, tipoUsuarioId) 
VALUES 
    ('Isabella', 'admin@gmail.com', 'admin123', 1),
    ('Maria Gestor', 'gestor@example.com', 'gestor123', 2),
    ('João Usuário', 'usuario@example.com', 'usuario123', 3)
ON DUPLICATE KEY UPDATE 
    nome = VALUES(nome),
    senha = VALUES(senha);

-- Responsáveis de teste
INSERT INTO responsavel (usuarioId, nome, cargo) 
VALUES 
    (1, 'Isabella', 'Administrador do sistema'),
    (2, 'Maria Gestor', 'Gestor de Patrimônio'),
    (3, 'João Usuário', 'Operador de Sistema')
ON DUPLICATE KEY UPDATE 
    nome = VALUES(nome),
    cargo = VALUES(cargo);

-- Secretarias
INSERT INTO secretaria (nome, descricao, responsavelId) 
VALUES 
    ('Secretaria de Educação', 'Responsável por toda gestão educacional', 2),
    ('Secretaria de Saúde', 'Responsável por toda gestão de saúde', 2),
    ('Secretaria de Obras', 'Responsável por infraestrutura', 2)
ON DUPLICATE KEY UPDATE 
    descricao = VALUES(descricao),
    responsavelId = VALUES(responsavelId);

-- Unidades (vinculadas às secretarias)
INSERT INTO unidade (nome, secretariaId, endereco, responsavelId) 
VALUES 
    ('Escola Municipal Sete de Setembro', 1, 'Rua A, nº 100 - Centro', 3),
    ('Posto de Saúde Central', 2, 'Rua B, nº 200 - Centro', 3),
    ('Departamento de Obras', 3, 'Rua C, nº 300 - Centro', 3)
ON DUPLICATE KEY UPDATE 
    endereco = VALUES(endereco),
    responsavelId = VALUES(responsavelId);

-- Departamentos dentro das unidades
INSERT INTO departamento (nome, unidadeId, responsavelId) 
VALUES 
    ('Direção', 1, 3),
    ('Almoxarifado', 1, 3),
    ('Atendimento', 2, 3),
    ('Administração', 3, 3)
ON DUPLICATE KEY UPDATE 
    responsavelId = VALUES(responsavelId);

-- Localizações dentro dos departamentos
INSERT INTO localizacao (departamentoId, nome, descricao) 
VALUES 
    (1, 'Sala Direção', 'Sala principal de direção'),
    (2, 'Almoxarifado Área A', 'Primeira área do almoxarifado'),
    (2, 'Almoxarifado Área B', 'Segunda área do almoxarifado'),
    (3, 'Recepção', 'Recepção do posto de saúde')
ON DUPLICATE KEY UPDATE 
    descricao = VALUES(descricao);

-- Itens de patrimônio
INSERT INTO item (numeroPatrimonio, descricao, marca, modelo, numeroSerie, estado, dataAquisicao, valor, tipoMaterialId, departamentoId, localizacaoId, responsavelId) 
VALUES 
    ('PAT-001-2024', 'Computador Desktop', 'Dell', 'Optiplex 3070', 'SN12345', 'bom', '2024-01-15', 3500.00, 1, 1, 1, 3),
    ('PAT-002-2024', 'Impressora Laser', 'HP', 'LaserJet Pro', 'SN67890', 'bom', '2024-02-20', 1200.00, 1, 2, 2, 3),
    ('PAT-003-2024', 'Ar Condicionado', 'Consul', '12000 BTU', 'SN11111', 'otimo', '2024-03-10', 2500.00, 1, 3, 4, 3),
    ('PAT-004-2024', 'Cadeira Gamer', 'Evolut', 'Storm', 'SN22222', 'bom', '2024-01-05', 450.00, 1, 1, 1, 3),
    ('PAT-005-2024', 'Mesa de Escritório', 'Itapeva', 'MDF', 'SN33333', 'ruim', '2023-06-12', 300.00, 1, 1, 1, 3)
ON DUPLICATE KEY UPDATE 
    descricao = VALUES(descricao),
    estado = VALUES(estado);

-- Movimentações de itens
INSERT INTO movimentacao (itemId, tipo, departamentoOrigemId, departamentoDestinoId, observacao, dataMovimentacao) 
VALUES 
    (1, 'criacao', 1, 1, 'Item criado no sistema', CURDATE()),
    (2, 'criacao', 2, 2, 'Item criado no sistema', CURDATE()),
    (3, 'criacao', 3, 3, 'Item criado no sistema', CURDATE())
ON DUPLICATE KEY UPDATE 
    observacao = VALUES(observacao);

-- Inventários anuais
INSERT INTO inventario (ano, nome, dataInicio, status, unidadeId) 
VALUES 
    (2024, 'Inventário 2024 - Educação', '2024-06-01', 'ABERTO', 1),
    (2024, 'Inventário 2024 - Saúde', '2024-06-01', 'ABERTO', 2)
ON DUPLICATE KEY UPDATE 
    status = VALUES(status);

-- ============================================================
-- VERIFICAÇÃO FINAL
-- ============================================================
SELECT 'Dados iniciais carregados com sucesso!' AS status;
SELECT COUNT(*) as total_usuarios FROM usuario;
SELECT COUNT(*) as total_itens FROM item;
SELECT COUNT(*) as total_secretarias FROM secretaria;
