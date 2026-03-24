-- ============================================
-- BANCO DE DADOS DO e-SGP
-- Sistema de Gestão de Patrimônio Público
-- ============================================

CREATE DATABASE IF NOT EXISTS esgp;
USE esgp;

-- Tabela de usuários
CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    nivel ENUM('admin', 'gestor', 'usuario') DEFAULT 'usuario',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de município
CREATE TABLE municipios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    cnpj VARCHAR(18),
    endereco TEXT
);

-- Tabela de secretarias
CREATE TABLE secretarias (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    responsavel VARCHAR(100),
    municipio_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (municipio_id) REFERENCES municipios(id) ON DELETE CASCADE
);

-- Tabela de unidades
CREATE TABLE unidades (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    endereco TEXT,
    responsavel VARCHAR(100),
    secretaria_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (secretaria_id) REFERENCES secretarias(id) ON DELETE CASCADE
);

-- Tabela de locais
CREATE TABLE locais (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    unidade_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (unidade_id) REFERENCES unidades(id) ON DELETE CASCADE
);

-- Tabela de itens patrimoniais
CREATE TABLE itens (
    id INT PRIMARY KEY AUTO_INCREMENT,
    numero_patrimonio VARCHAR(20) UNIQUE NOT NULL,
    descricao TEXT NOT NULL,
    marca VARCHAR(50),
    modelo VARCHAR(50),
    numero_serie VARCHAR(50),
    data_aquisicao DATE,
    valor DECIMAL(10,2),
    estado ENUM('otimo', 'bom', 'regular', 'ruim', 'inservivel') DEFAULT 'bom',
    observacoes TEXT,
    foto VARCHAR(255),
    local_id INT,
    responsavel_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (local_id) REFERENCES locais(id) ON DELETE SET NULL,
    FOREIGN KEY (responsavel_id) REFERENCES usuarios(id) ON DELETE SET NULL
);

-- Tabela de movimentações
CREATE TABLE movimentacoes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    item_id INT NOT NULL,
    data_movimentacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    local_origem_id INT,
    local_destino_id INT,
    responsavel_origem_id INT,
    responsavel_destino_id INT,
    motivo TEXT,
    FOREIGN KEY (item_id) REFERENCES itens(id) ON DELETE CASCADE,
    FOREIGN KEY (local_origem_id) REFERENCES locais(id),
    FOREIGN KEY (local_destino_id) REFERENCES locais(id),
    FOREIGN KEY (responsavel_origem_id) REFERENCES usuarios(id),
    FOREIGN KEY (responsavel_destino_id) REFERENCES usuarios(id)
);

-- Tabela de leilões
CREATE TABLE leiloes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    edital VARCHAR(50) NOT NULL,
    data_leilao DATE,
    status ENUM('agendado', 'em_andamento', 'concluido', 'cancelado') DEFAULT 'agendado',
    observacoes TEXT,
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

-- Inserir usuário admin (senha: admin123)
INSERT INTO usuarios (nome, email, senha, nivel) VALUES 
('Administrador', 'admin@prefeitura.br', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Inserir município
INSERT INTO municipios (nome, cnpj, endereco) VALUES 
('Prefeitura Municipal', '00.000.000/0001-00', 'Praça Central, 100 - Centro');

-- Inserir secretarias
INSERT INTO secretarias (nome, responsavel, municipio_id) VALUES 
('Educação', 'João Silva', 1),
('Saúde', 'Maria Santos', 1),
('Obras', 'Pedro Oliveira', 1);

-- Inserir unidades
INSERT INTO unidades (nome, endereco, responsavel, secretaria_id) VALUES 
('E.M. Pedro Álvares', 'Rua das Flores, 123 - Centro', 'Diretora Maria', 1),
('E.M. Santos Dumont', 'Av. Principal, 456 - Bairro', 'Diretor João', 1),
('Posto Central', 'Rua da Saúde, 789 - Centro', 'Dr. Carlos', 2);

-- Inserir locais
INSERT INTO locais (nome, unidade_id) VALUES 
('Sala 1 - 1º Ano', 1),
('Sala 2 - 2º Ano', 1),
('Sala de Informática', 1),
('Biblioteca', 1),
('Sala 1 - 1º Ano', 2),
('Sala 2 - 2º Ano', 2),
('Recepção', 3),
('Consultório 1', 3),
('Consultório 2', 3);

-- Inserir itens
INSERT INTO itens (numero_patrimonio, descricao, marca, modelo, estado, local_id, valor) VALUES 
('2026.001', 'Computador Desktop', 'Dell', 'Optiplex 3080', 'otimo', 1, 3500.00),
('2026.002', 'Mesa professor', 'Fortal', 'Profissional', 'bom', 1, 450.00),
('2026.003', 'Cadeira', 'Flexform', 'Giratória', 'regular', 1, 180.00),
('2026.004', 'Projetor', 'Epson', 'EB-X06', 'otimo', 3, 2800.00),
('2026.005', 'Armário', 'Itatiaia', 'Aço', 'bom', 4, 890.00),
('2026.006', 'Notebook', 'Lenovo', 'IdeaPad 3', 'otimo', 3, 4200.00),
('2026.007', 'Carteira escolar', 'Plasutil', 'Infantil', 'ruim', 2, 120.00),
('2026.008', 'Ar condicionado', 'Samsung', 'Inverter', 'bom', 3, 2800.00),
('2026.009', 'Impressora', 'HP', 'LaserJet', 'regular', 1, 950.00),
('2026.010', 'Quadro branco', 'Brasforma', '1,20m', 'bom', 2, 220.00);