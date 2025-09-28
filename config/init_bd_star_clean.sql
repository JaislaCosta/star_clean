-- Cria banco de dados do sistema
CREATE DATABASE IF NOT EXISTS bd_star_clean CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bd_star_clean;

-- CLIENTES
CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    senha VARCHAR(255)
);

-- PRESTADORES
CREATE TABLE prestadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    senha VARCHAR(255),
    especialidade VARCHAR(100),
    descricao TEXT
);

-- SERVIÇOS
CREATE TABLE servicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prestador_id INT,
    titulo VARCHAR(100),
    descricao TEXT,
    preco DECIMAL(10,2),
    FOREIGN KEY (prestador_id) REFERENCES prestadores(id)
);

-- DISPONIBILIDADE
CREATE TABLE disponibilidade (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prestador_id INT,
    data DATE,
    hora TIME,
    status ENUM('livre', 'ocupado') DEFAULT 'livre',
    FOREIGN KEY (prestador_id) REFERENCES prestadores(id)
);

-- AGENDAMENTOS
CREATE TABLE agendamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT,
    servico_id INT,
    data DATE,
    hora TIME,
    status ENUM('pendente', 'realizado', 'cancelado') DEFAULT 'pendente',
    FOREIGN KEY (cliente_id) REFERENCES clientes(id),
    FOREIGN KEY (servico_id) REFERENCES servicos(id)
);

-- AVALIAÇÕES
CREATE TABLE avaliacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    agendamento_id INT,
    nota INT,
    comentario TEXT,
    FOREIGN KEY (agendamento_id) REFERENCES agendamentos(id)
);

-- ADMINISTRADORES
CREATE TABLE administradores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    senha VARCHAR(255)
);







-- Cria banco de dados
CREATE DATABASE IF NOT EXISTS bd_star_clean CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bd_star_clean;

-- CLIENTES
CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    sobrenome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    data_nascimento DATE NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    cpf VARCHAR(14) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
);

-- PRESTADORES
CREATE TABLE prestadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_razao_social VARCHAR(150) NOT NULL,
    sobrenome_nome_fantasia VARCHAR(150) NOT NULL,
    cpf_cnpj VARCHAR(18) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    telefone VARCHAR(20) NOT NULL,
    especialidade VARCHAR(100) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
);

-- ADMINISTRADORES
CREATE TABLE administradores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    sobrenome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
);

-- ENDEREÇOS (para clientes e prestadores)
CREATE TABLE enderecos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NULL,
    prestador_id INT NULL,
    cep VARCHAR(9),
    logradouro VARCHAR(255),
    bairro VARCHAR(100),
    cidade VARCHAR(100),
    uf CHAR(2),
    numero VARCHAR(10),
    complemento VARCHAR(100),
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
    FOREIGN KEY (prestador_id) REFERENCES prestadores(id) ON DELETE CASCADE
);

-- SERVIÇOS (ligados a prestadores)
CREATE TABLE servicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prestador_id INT NOT NULL,
    titulo VARCHAR(100) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10,2) NOT NULL,
    duracao_estimada INT,
    FOREIGN KEY (prestador_id) REFERENCES prestadores(id) ON DELETE CASCADE
);

-- DISPONIBILIDADE (do prestador)
CREATE TABLE disponibilidade (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prestador_id INT NOT NULL,
    data DATE NOT NULL,
    hora TIME NOT NULL,
    status ENUM('LIVRE', 'OCUPADO') DEFAULT 'LIVRE',
    FOREIGN KEY (prestador_id) REFERENCES prestadores(id) ON DELETE CASCADE
);

-- AGENDAMENTOS
CREATE TABLE agendamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    servico_id INT NOT NULL,
    endereco_id INT NULL,
    data DATE NOT NULL,
    hora TIME NOT NULL,
    status ENUM('PENDENTE', 'REALIZADO', 'CANCELADO') DEFAULT 'PENDENTE',
    observacoes TEXT,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
    FOREIGN KEY (servico_id) REFERENCES servicos(id) ON DELETE CASCADE,
    FOREIGN KEY (endereco_id) REFERENCES enderecos(id) ON DELETE SET NULL
);

-- AVALIAÇÕES DE SERVIÇOS
CREATE TABLE avaliacoes_servicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    agendamento_id INT NOT NULL,
    nota INT CHECK (nota BETWEEN 1 AND 5),
    comentario TEXT,
    FOREIGN KEY (agendamento_id) REFERENCES agendamentos(id) ON DELETE CASCADE
);

-- AVALIAÇÕES DE PRESTADORES
CREATE TABLE avaliacoes_prestadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prestador_id INT NOT NULL,
    cliente_id INT NOT NULL,
    nota INT CHECK (nota BETWEEN 1 AND 5),
    comentario TEXT,
    FOREIGN KEY (prestador_id) REFERENCES prestadores(id) ON DELETE CASCADE,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE
);

