
CREATE DATABASE IF NOT EXISTS gestion_entreprise
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci;

USE gestion_entreprise;

CREATE TABLE reference_management (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    site VARCHAR(255) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    Completion_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);