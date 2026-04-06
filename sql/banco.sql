-- BANCO DE DADOS: e_sgp (Sistema de Gestão de Patrimônio)
CREATE DATABASE e_sgp;
USE e_sgp;

-- Tabela para gerenciar os usuários do sistema
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT, -- Gera um numero unico para cada usuario
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE, -- Limita o email para ser unico no sistema
    senha VARCHAR(255) NOT NULL,
    nivel ENUM('admin', 'gestor', 'usuario') DEFAULT 'usuario', -- Nivel de usuario 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Data de criação do usuario
    CONSTRAINT pk_usuarios PRIMARY KEY (id)
);

-- Tabela das secretarias municipais
CREATE TABLE secretarias (
    id INT AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    responsavel VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT pk_secretarias PRIMARY KEY (id)
);

-- Tabela de unidades vinculadas as secretarias
CREATE TABLE unidades (
    id INT AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    secretariaId INT NOT NULL,
    endereco TEXT,
    responsavel VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    CONSTRAINT pk_unidades PRIMARY KEY (id),
    CONSTRAINT fk_unidades_secretaria FOREIGN KEY (secretariaId) 
        REFERENCES secretarias(id)
        ON UPDATE RESTRICT
        ON DELETE CASCADE
);

-- Tabela de locais especificos dentro das unidades
CREATE TABLE locais (
    id INT AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    unidadeId INT NOT NULL, 
    responsavelId INT,      
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    CONSTRAINT pk_locais PRIMARY KEY (id),
    CONSTRAINT fk_locais_unidade FOREIGN KEY (unidadeId) 
        REFERENCES unidades(id)
        ON UPDATE RESTRICT
        ON DELETE CASCADE,
    CONSTRAINT fk_locais_usuario FOREIGN KEY (responsavelId) 
        REFERENCES usuarios(id)
        ON UPDATE RESTRICT
        ON DELETE SET NULL
);

-- Tabela principal de itens de patrimonio
CREATE TABLE itens (
    id INT AUTO_INCREMENT,
    numeroPatrimonio VARCHAR(20) NOT NULL UNIQUE, 
    descricao TEXT NOT NULL,
    marca VARCHAR(50),
    modelo VARCHAR(50),
    numeroSerie VARCHAR(50), 
    estado ENUM('otimo', 'bom', 'regular', 'ruim', 'inservivel') DEFAULT 'bom',
    dataAquisicao DATE,      
    valor DECIMAL(10,2),
    notaFiscal VARCHAR(50),  
    foto VARCHAR(255),
    observacoes TEXT,
    localId INT NOT NULL,       
    responsavelId INT,         
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, -- atualiza sozinho quando modifica
    CONSTRAINT pk_itens PRIMARY KEY (id),
    CONSTRAINT fk_itens_local FOREIGN KEY (localId) 
        REFERENCES locais(id)
        ON UPDATE RESTRICT
        ON DELETE CASCADE,
    CONSTRAINT fk_itens_usuario FOREIGN KEY (responsavelId) 
        REFERENCES usuarios(id)
        ON UPDATE RESTRICT
        ON DELETE SET NULL
);

-- Tabela para historico de movimentaçao dos bens
CREATE TABLE movimentacoes (
    id INT AUTO_INCREMENT,
    itemId INT NOT NULL,
    tipo ENUM('criacao', 'transferencia', 'baixa', 'manutencao') NOT NULL,
    localOrigemId INT,   
    localDestinoId INT, 
    observacao TEXT,
    dataMovimentacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    CONSTRAINT pk_movimentacoes PRIMARY KEY (id),
    CONSTRAINT fk_mov_item FOREIGN KEY (itemId) 
        REFERENCES itens(id)
        ON UPDATE RESTRICT
        ON DELETE CASCADE,
    CONSTRAINT fk_mov_origem FOREIGN KEY (localOrigemId) 
        REFERENCES locais(id)
        ON UPDATE RESTRICT
        ON DELETE SET NULL,
    CONSTRAINT fk_mov_destino FOREIGN KEY (localDestinoId) 
        REFERENCES locais(id)
        ON UPDATE RESTRICT
        ON DELETE SET NULL
);
