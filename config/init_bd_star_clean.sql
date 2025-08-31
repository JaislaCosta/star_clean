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
