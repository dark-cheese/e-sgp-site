-- sql/banco.sql
-- Script completo para criar o banco de dados do e-SGP

-- Criar o banco de dados
CREATE DATABASE IF NOT EXISTS e_sgp;
USE e_sgp;

-- Tabela de usuários
CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    nivel ENUM('admin', 'gestor', 'usuario') DEFAULT 'usuario',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de secretarias
CREATE TABLE secretarias (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    responsavel VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de unidades (escolas, postos, etc)
CREATE TABLE unidades (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    secretaria_id INT NOT NULL,
    endereco TEXT,
    responsavel VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (secretaria_id) REFERENCES secretarias(id) ON DELETE CASCADE
);

-- Tabela de locais (salas, setores)
CREATE TABLE locais (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    unidade_id INT NOT NULL,
    responsavel_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (unidade_id) REFERENCES unidades(id) ON DELETE CASCADE
);

-- Tabela de itens patrimoniais
CREATE TABLE itens (
    id INT PRIMARY KEY AUTO_INCREMENT,
    numero_patrimonio VARCHAR(20) NOT NULL UNIQUE,
    descricao TEXT NOT NULL,
    marca VARCHAR(50),
    modelo VARCHAR(50),
    numero_serie VARCHAR(50),
    estado ENUM('otimo', 'bom', 'regular', 'ruim', 'inservivel') DEFAULT 'bom',
    data_aquisicao DATE,
    valor DECIMAL(10,2),
    nota_fiscal VARCHAR(50),
    foto VARCHAR(255),
    observacoes TEXT,
    local_id INT NOT NULL,
    responsavel_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (local_id) REFERENCES locais(id) ON DELETE CASCADE
);

-- Tabela de movimentações (histórico)
CREATE TABLE movimentacoes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    item_id INT NOT NULL,
    tipo ENUM('criacao', 'transferencia', 'baixa', 'manutencao') NOT NULL,
    local_origem_id INT,
    local_destino_id INT,
    observacao TEXT,
    data_movimentacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (item_id) REFERENCES itens(id) ON DELETE CASCADE
);

-- Tabela de leilões
CREATE TABLE leiloes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    edital VARCHAR(50) NOT NULL,
    data_leilao DATE,
    comissao TEXT,
    status ENUM('agendado', 'em_andamento', 'concluido') DEFAULT 'agendado',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de itens em leilão
CREATE TABLE itens_leilao (
    id INT PRIMARY KEY AUTO_INCREMENT,
    leilao_id INT NOT NULL,
    item_id INT NOT NULL,
    valor_avaliado DECIMAL(10,2),
    valor_arrematado DECIMAL(10,2),
    arrematante VARCHAR(100),
    status ENUM('avaliado', 'em_leilao', 'arrematado', 'nao_arrematado') DEFAULT 'avaliado',
    FOREIGN KEY (leilao_id) REFERENCES leiloes(id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES itens(id) ON DELETE CASCADE
);

-- Inserir usuário admin padrão (senha: admin123)
INSERT INTO usuarios (nome, email, senha, nivel) VALUES 
('Administrador', 'admin@prefeitura.br', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Inserir dados de exemplo
INSERT INTO secretarias (nome, responsavel) VALUES 
('Educação', 'João Silva'),
('Saúde', 'Maria Santos'),
('Obras', 'Pedro Oliveira');

INSERT INTO unidades (nome, secretaria_id, responsavel) VALUES 
('E.M. Pedro Álvares', 1, 'Diretora Maria'),
('E.M. Santos Dumont', 1, 'Diretor João'),
('Posto Central', 2, 'Dr. Carlos');

INSERT INTO locais (nome, unidade_id) VALUES 
('Sala 1 - 1º Ano', 1),
('Sala 2 - 2º Ano', 1),
('Sala de Informática', 1),
('Sala 1', 2),
('Recepção', 3);