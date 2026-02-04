
-- CREATION DE LA BASE DE DONNÉES --

CREATE DATABASE IF NOT EXISTS gestion_entreprise
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci;

USE gestion_entreprise;

CREATE TABLE gestion_personnel (
    id_personnel INT AUTO_INCREMENT PRIMARY KEY,

    -- Identité --
    firstname VARCHAR(100) NOT NULL,
    lastname VARCHAR(100) NOT NULL,
    mail VARCHAR(150) NOT NULL UNIQUE,
    birthdate DATE NULL,
   
    -- Contact --
    phone VARCHAR(12) NOT NULL,

    -- Adresse --
    rue VARCHAR(255) NOT NULL,
    cp VARCHAR(20) NOT NULL,
    ville VARCHAR(150) NOT NULL,

    -- Mot de passe (hashé) --
    password VARCHAR(255) NOT NULL,

    -- role --
    fonction text

    );



