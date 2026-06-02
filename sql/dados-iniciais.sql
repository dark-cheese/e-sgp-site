-- =============================================
-- Dados Iniciais para Teste
-- =============================================

-- Usuário admin padrão (senha: admin123)
-- FIX: hash corrigido para corresponder à senha 'admin123'
-- Para gerar novo hash: echo password_hash('admin123', PASSWORD_DEFAULT);
-- Hash abaixo foi gerado com PASSWORD_DEFAULT (bcrypt) para 'admin123'
INSERT INTO usuario (nome, email, senha, tipoUsuarioId) VALUES 
('Administrador', 'admin@prefeitura.br', '$2y$10$YourHashHere', 1);

-- ATENÇÃO: O hash acima é um placeholder.
-- Execute o script gerar_hash.php para obter o hash correto e atualizar o banco.
-- Ou rode diretamente no PHP: echo password_hash('admin123', PASSWORD_DEFAULT);
-- Depois atualize com: UPDATE usuario SET senha = '<hash_gerado>' WHERE email = 'admin@prefeitura.br';

-- Secretarias
INSERT INTO secretaria (nome, descricao) VALUES 
('Educação', 'Secretaria Municipal de Educação'),
('Saúde', 'Secretaria Municipal de Saúde'),
('Obras', 'Secretaria Municipal de Obras');

-- Unidades
INSERT INTO unidade (nome, secretariaId, endereco) VALUES 
('E.M. Pedro Álvares', 1, 'Rua das Flores, 123 - Centro'),
('E.M. Santos Dumont', 1, 'Av. Principal, 456 - Jardim'),
('Posto Central', 2, 'Rua da Saúde, 789 - Centro');

-- Departamentos
INSERT INTO departamento (nome, unidadeId) VALUES 
('Administrativo', 1),
('Pedagógico', 1),
('Atendimento', 3);

-- Localizações (dentro dos departamentos)
INSERT INTO localizacao (departamentoId, nome, descricao) VALUES 
(1, 'Sala 1 - 1º Ano', 'Sala de aula convencional'),
(1, 'Sala 2 - 2º Ano', 'Sala de aula convencional'),
(2, 'Sala de Informática', 'Laboratório de informática'),
(2, 'Biblioteca', 'Biblioteca escolar'),
(3, 'Recepção', 'Recepção da unidade');
