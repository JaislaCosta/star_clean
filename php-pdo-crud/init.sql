
-- Crie o banco antes (ex.: CREATE DATABASE crud_php CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;)
-- USE crud_php;

CREATE TABLE IF NOT EXISTS usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  sobrenome VARCHAR(120),
  email VARCHAR(160) NOT NULL UNIQUE,
  endereco VARCHAR(250),
  telefone VARCHAR(11),
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
