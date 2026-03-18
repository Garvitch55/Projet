
-- CREATION DE LA BASE DE DONNÉES --

CREATE DATABASE IF NOT EXISTS gestion_entreprise
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci;

USE gestion_entreprise;


-- Table des clients

CREATE TABLE IF NOT EXISTS gestion_client (
    id_client INT AUTO_INCREMENT PRIMARY KEY,

    -- Identité
    firstname VARCHAR(100) NOT NULL,
    lastname VARCHAR(100) NOT NULL,
    birthdate DATE NULL,

    -- Contact
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,

    -- Adresse
    rue VARCHAR(255) NOT NULL,
    cp VARCHAR(20) NOT NULL,
    ville VARCHAR(150) NOT NULL,

    -- Demande client
    demande TEXT NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,  -- 0 = non lu, 1 = lu

    -- Mot de passe (hashé)
    password VARCHAR(255) NOT NULL
);