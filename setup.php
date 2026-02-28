<?php
include "Includes/config.php";

/* ===============================
   CONEXÃO SEM BANCO
   (necessário para criar o DB)
================================ */
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

/* ===============================
   CRIA BANCO SE NÃO EXISTIR
================================ */
$conn->query("
    CREATE DATABASE IF NOT EXISTS ".DB_NAME."
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci
");

/* ===============================
   SELECIONA BANCO
================================ */
$conn->select_db(DB_NAME);

/* ===============================
   TABELA: USUÁRIOS
================================ */
$conn->query("
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    perfil ENUM('admin','infra','leitura') DEFAULT 'infra',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
");

/* ===============================
   TABELA: MATERIAIS
================================ */
$conn->query("
CREATE TABLE IF NOT EXISTS materiais (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    categoria VARCHAR(50),
    quantidade INT NOT NULL DEFAULT 0,
    minimo INT NOT NULL DEFAULT 0,
    foto VARCHAR(255),
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
");

/* ===============================
   TABELA: OBRAS / PROJETOS
================================ */
$conn->query("
CREATE TABLE IF NOT EXISTS obras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cliente VARCHAR(100),
    status ENUM('ativa','finalizada') DEFAULT 'ativa',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
");

/* ===============================
   TABELA: MOVIMENTAÇÕES
================================ */
$conn->query("
CREATE TABLE IF NOT EXISTS movimentacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    material_id INT NOT NULL,
    obra_id INT NULL,
    tipo ENUM('entrada','saida') NOT NULL,
    quantidade INT NOT NULL,
    observacao VARCHAR(255),
    data_movimentacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (material_id) REFERENCES materiais(id)
        ON DELETE CASCADE,

    FOREIGN KEY (obra_id) REFERENCES obras(id)
        ON DELETE SET NULL
) ENGINE=InnoDB;
");

/* ===============================
   TABELA: ORDEM DE COMPRA
================================ */
$conn->query("
CREATE TABLE IF NOT EXISTS ordem_compra (
    id INT AUTO_INCREMENT PRIMARY KEY,
    material_id INT NOT NULL,
    quantidade_sugerida INT NOT NULL,
    status ENUM('pendente','aprovada','concluida') DEFAULT 'pendente',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (material_id) REFERENCES materiais(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;


");
/* ===============================
   TABELA: OBRAS / PROJETOS
================================ */
$conn->query("
CREATE TABLE IF NOT EXISTS obras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cliente VARCHAR(100),
    status ENUM('ativa','finalizada') DEFAULT 'ativa',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
 ");

/* ===============================
   TABELA: MOVIMENTAÇÕES  
================================ */



/* ===============================
   USUÁRIO ADMIN PADRÃO
================================ */
$senha = password_hash("admin123", PASSWORD_DEFAULT);

$conn->query("
INSERT IGNORE INTO usuarios (usuario, senha, perfil)
VALUES ('admin', '$senha', 'admin')
");


echo "✅ Setup executado com sucesso!";