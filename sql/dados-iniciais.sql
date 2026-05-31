-- =============================================
-- Dados Iniciais para Teste
-- =============================================

-- Usuário admin padrão (senha: admin123)
INSERT INTO usuario (nome, email, senha, tipoUsuarioId) VALUES 
('Administrador', 'admin@prefeitura.br', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

-- Responsáveis
INSERT INTO responsavel (usuarioId, nome, cargo) VALUES 
(1, 'João Silva', 'Responsável Geral'),
(NULL, 'Maria Santos', 'Responsável de Saúde'),
(NULL, 'Pedro Oliveira', 'Responsável de Obras');

-- Secretarias
INSERT INTO secretaria (nome, descricao, responsavelId) VALUES 
('Educação', 'Secretaria Municipal de Educação', 1),
('Saúde', 'Secretaria Municipal de Saúde', 2),
('Obras', 'Secretaria Municipal de Obras', 3);

-- Unidades
INSERT INTO unidade (nome, secretariaId, endereco, responsavelId) VALUES 
('E.M. Pedro Álvares', 1, 'Rua das Flores, 123 - Centro', 1),
('E.M. Santos Dumont', 1, 'Av. Principal, 456 - Jardim', 2),
('Posto Central', 2, 'Rua da Saúde, 789 - Centro', 3);

-- Departamentos
INSERT INTO departamento (nome, unidadeId, responsavelId) VALUES 
('Administração Escolar', 1, 1),
('Secretaria', 1, 2),
('Apoio Médico', 2, 3);

-- Localizações
INSERT INTO localizacao (departamentoId, nome, descricao) VALUES 
(1, 'Sala 1 - 1º Ano', 'Sala de aula do 1º ano'),
(1, 'Sala 2 - 2º Ano', 'Sala de aula do 2º ano'),
(2, 'Sala de Informática', 'Laboratório de informática'),
(2, 'Biblioteca', 'Biblioteca da unidade'),
(3, 'Recepção', 'Recepção do posto central');
