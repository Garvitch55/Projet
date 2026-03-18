-- =========================================
-- CREATION DE LA BASE DE DONNÉES
-- =========================================
CREATE DATABASE IF NOT EXISTS gestion_entreprise
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci;

USE gestion_entreprise;

-- =========================================
-- TABLE CLIENT
-- =========================================
CREATE TABLE IF NOT EXISTS gestion_client (
    id_client INT AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(100) NOT NULL,
    lastname VARCHAR(100) NOT NULL,
    birthdate DATE NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    rue VARCHAR(255) NOT NULL,
    cp VARCHAR(20) NOT NULL,
    ville VARCHAR(150) NOT NULL,
    demande TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    password VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =========================================
-- TABLE PERSONNEL
-- =========================================
CREATE TABLE IF NOT EXISTS gestion_personnel (
    id_personnel INT AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(100) NOT NULL,
    lastname VARCHAR(100) NOT NULL,
    mail VARCHAR(150) NOT NULL UNIQUE,
    birthdate DATE NULL,
    phone VARCHAR(20) NOT NULL,
    rue VARCHAR(255) NOT NULL,
    cp VARCHAR(20) NOT NULL,
    ville VARCHAR(150) NOT NULL,
    password VARCHAR(255) NOT NULL,
    fonction TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =========================================
-- TABLE DES RÉPONSES AUX CLIENTS
-- =========================================
CREATE TABLE IF NOT EXISTS client_replies (
    id_reply INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    message TEXT NOT NULL,
    personnel_id INT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    response_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_client_replies_client FOREIGN KEY (client_id)
        REFERENCES gestion_client(id_client)
        ON DELETE CASCADE,
    CONSTRAINT fk_client_replies_personnel FOREIGN KEY (personnel_id)
        REFERENCES gestion_personnel(id_personnel)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =========================================
-- TABLE CONTACT
-- =========================================
CREATE TABLE IF NOT EXISTS contact (
    id_contact INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =========================================
-- TABLE WORKS
-- =========================================
CREATE TABLE IF NOT EXISTS works (
    id_work INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    unit VARCHAR(50) NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =========================================
-- TABLE TVA
-- =========================================
CREATE TABLE IF NOT EXISTS tva (
    id_tva INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    rate DECIMAL(5,2) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =========================================
-- TABLE REFERENCES
-- =========================================
CREATE TABLE IF NOT EXISTS reference_management (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    site VARCHAR(255) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    Completion_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =========================================
-- TABLE QUOTES
-- =========================================
CREATE TABLE IF NOT EXISTS quotes (
    id_quote INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    quote_number VARCHAR(50) NOT NULL,
    quote_date DATE NOT NULL,
    status ENUM('en attente','signé','annulé') NOT NULL DEFAULT 'en attente',
    total_ht DECIMAL(10,2) DEFAULT 0,
    total_vat DECIMAL(10,2) DEFAULT 0,
    total_ttc DECIMAL(10,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_quotes_client FOREIGN KEY (client_id)
        REFERENCES gestion_client(id_client)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =========================================
-- TABLE QUOTE ITEMS
-- =========================================
CREATE TABLE IF NOT EXISTS quote_items (
    id_item INT AUTO_INCREMENT PRIMARY KEY,
    quote_id INT NOT NULL,
    work_id INT NULL,
    description VARCHAR(255) NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_quote_items_quote FOREIGN KEY (quote_id)
        REFERENCES quotes(id_quote)
        ON DELETE CASCADE,
    CONSTRAINT fk_quote_items_work FOREIGN KEY (work_id)
        REFERENCES works(id_work)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =========================================
-- TABLE INVOICES
-- =========================================
CREATE TABLE IF NOT EXISTS invoices (
    id_invoice INT AUTO_INCREMENT PRIMARY KEY,
    quote_id INT NULL,
    client_id INT NOT NULL,
    invoice_number VARCHAR(50) NOT NULL,
    invoice_date DATE NOT NULL,
    due_date DATE NOT NULL,
    status ENUM('brouillon','en attente de paiement','payée','annulée') DEFAULT 'brouillon',
    total_ht DECIMAL(10,2) DEFAULT 0,
    total_vat DECIMAL(10,2) DEFAULT 0,
    total_ttc DECIMAL(10,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_invoice_client FOREIGN KEY (client_id)
        REFERENCES gestion_client(id_client)
        ON DELETE CASCADE,
    CONSTRAINT fk_invoice_quote FOREIGN KEY (quote_id)
        REFERENCES quotes(id_quote)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =========================================
-- TABLE INVOICE ITEMS
-- =========================================
CREATE TABLE IF NOT EXISTS invoice_items (
    id_item INT AUTO_INCREMENT PRIMARY KEY,
    invoice_id INT NOT NULL,
    work_id INT NULL,
    description VARCHAR(255) NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_invoice_items_invoice FOREIGN KEY (invoice_id)
        REFERENCES invoices(id_invoice)
        ON DELETE CASCADE,
    CONSTRAINT fk_invoice_items_work FOREIGN KEY (work_id)
        REFERENCES works(id_work)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;