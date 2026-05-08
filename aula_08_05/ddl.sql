-- DDL principal - V1 (CRUD de livros com genero relacional)

CREATE DATABASE IF NOT EXISTS senai_livraria
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE senai_livraria;

CREATE TABLE IF NOT EXISTS generos (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(80) NOT NULL,
  criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_generos_nome (nome)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS livros (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(180) NOT NULL,
  ano SMALLINT UNSIGNED NOT NULL,
  genero_id INT UNSIGNED NOT NULL,
  quantidade INT UNSIGNED NOT NULL,
  status_livro ENUM('ativo', 'inativo') NOT NULL DEFAULT 'ativo',
  criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_livros_titulo (titulo),
  KEY idx_livros_status (status_livro),
  KEY idx_livros_genero_id (genero_id),
  CONSTRAINT chk_livros_quantidade CHECK (quantidade >= 0),
  CONSTRAINT fk_livros_generos FOREIGN KEY (genero_id) REFERENCES generos(id)
) ENGINE=InnoDB;
