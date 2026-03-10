CREATE DATABASE IF NOT EXISTS gestion_entreprise
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci;

USE gestion_entreprise;

CREATE TABLE contact (
    id_contact INT AUTO_INCREMENT PRIMARY KEY,

    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0, -- 0 = non lu, 1 = lu

    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);