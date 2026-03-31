-- ======================================================
-- BANCO DE DADOS: e_sgp (Sistema de Gestão de Patrimônio)
-- ======================================================
CREATE DATABASE IF NOT EXISTS e_sgp;
USE e_sgp;

-- Tabela para gerenciar os usuários do sistema
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    nivel ENUM('admin', 'gestor', 'usuario') DEFAULT 'usuario',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT pk_usuarios PRIMARY KEY (id)
);

-- Tabela das secretarias municipais/estaduais
CREATE TABLE secretarias (
    id INT AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    responsavel VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT pk_secretarias PRIMARY KEY (id)
);

-- Tabela de unidades vinculadas às secretarias
CREATE TABLE unidades (
    id INT AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    secretaria_id INT NOT NULL,
    endereco TEXT,
    responsavel VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT pk_unidades PRIMARY KEY (id),
    CONSTRAINT fk_unidades_secretaria FOREIGN KEY (secretaria_id)
        REFERENCES secretarias(id)
        ON UPDATE RESTRICT
        ON DELETE CASCADE
);

-- Tabela de locais específicos dentro das unidades
CREATE TABLE locais (
    id INT AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    unidade_id INT NOT NULL,
    responsavel_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT pk_locais PRIMARY KEY (id),
    CONSTRAINT fk_locais_unidade FOREIGN KEY (unidade_id)
        REFERENCES unidades(id)
        ON UPDATE RESTRICT
        ON DELETE CASCADE,
    CONSTRAINT fk_locais_usuario FOREIGN KEY (responsavel_id)
        REFERENCES usuarios(id)
        ON UPDATE RESTRICT
        ON DELETE SET NULL
);

-- Tabela principal de itens de patrimônio
CREATE TABLE itens (
    id INT AUTO_INCREMENT,
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
    CONSTRAINT pk_itens PRIMARY KEY (id),
    CONSTRAINT fk_itens_local FOREIGN KEY (local_id)
        REFERENCES locais(id)
        ON UPDATE RESTRICT
        ON DELETE CASCADE,
    CONSTRAINT fk_itens_usuario FOREIGN KEY (responsavel_id)
        REFERENCES usuarios(id)
        ON UPDATE RESTRICT
        ON DELETE SET NULL
);

-- Tabela para histórico de movimentação dos bens
CREATE TABLE movimentacoes (
    id INT AUTO_INCREMENT,
    item_id INT NOT NULL,
    tipo ENUM('criacao', 'transferencia', 'baixa', 'manutencao') NOT NULL,
    local_origem_id INT,
    local_destino_id INT,
    observacao TEXT,
    data_movimentacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT pk_movimentacoes PRIMARY KEY (id),
    CONSTRAINT fk_mov_item FOREIGN KEY (item_id)
        REFERENCES itens(id)
        ON UPDATE RESTRICT
        ON DELETE CASCADE,
    CONSTRAINT fk_mov_origem FOREIGN KEY (local_origem_id)
        REFERENCES locais(id)
        ON UPDATE RESTRICT
        ON DELETE SET NULL,
    CONSTRAINT fk_mov_destino FOREIGN KEY (local_destino_id)
        REFERENCES locais(id)
        ON UPDATE RESTRICT
        ON DELETE SET NULL
);
