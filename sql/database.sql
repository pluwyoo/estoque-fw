CREATE DATABASE estoque_fw;
USE estoque_fw;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50),
    senha VARCHAR(255)
);

CREATE TABLE materiais (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100),
    categoria VARCHAR(50),
    quantidade INT,
    minimo INT,
    foto VARCHAR(255)
);

CREATE TABLE ordem_compra (
    id INT AUTO_INCREMENT PRIMARY KEY,
    material_id INT,
    quantidade INT,
    status VARCHAR(20),
    FOREIGN KEY (material_id) REFERENCES materiais(id)
);

CREATE TABLE movimentacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    material_id INT,
    tipo ENUM('entrada','saida'),
    quantidade INT,
    observacao VARCHAR(255),
    data DATETIME DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE usuarios
ADD COLUMN perfil ENUM('admin','infra','leitura') DEFAULT 'infra';