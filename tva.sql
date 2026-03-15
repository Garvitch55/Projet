CREATE DATABASE IF NOT EXISTS gestion_entreprise
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci;

USE gestion_entreprise;

CREATE TABLE tva (
    id_tva INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    rate DECIMAL(5,2) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

