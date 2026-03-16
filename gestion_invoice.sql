
-- CREATION DE LA BASE DE DONNÉES --

CREATE DATABASE IF NOT EXISTS gestion_entreprise
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci;

USE gestion_entreprise;


-- ------------------------------------------------
-- Table des factures (invoices)
-- ------------------------------------------------
CREATE TABLE IF NOT EXISTS invoices (
    id_invoice INT AUTO_INCREMENT PRIMARY KEY,

    quote_id INT NULL,
    client_id INT NOT NULL,

    invoice_number VARCHAR(50) NOT NULL,
    invoice_date DATE NOT NULL,
    due_date DATE NOT NULL,

    status ENUM('brouillon','envoyée','payée','annulée') 
    DEFAULT 'brouillon',

    total_ht DECIMAL(10,2) DEFAULT 0,
    total_vat DECIMAL(10,2) DEFAULT 0,
    total_ttc DECIMAL(10,2) DEFAULT 0,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    -- lien vers client
    CONSTRAINT fk_invoice_client FOREIGN KEY (client_id)
        REFERENCES gestion_client(id_client)
        ON DELETE CASCADE,

    -- lien vers devis
    CONSTRAINT fk_invoice_quote FOREIGN KEY (quote_id)
        REFERENCES quotes(id_quote)
        ON DELETE SET NULL
);


-- ------------------------------------------------
-- Table des lignes de facture (invoice_items)
-- ------------------------------------------------
CREATE TABLE IF NOT EXISTS invoice_items (
    id_item INT AUTO_INCREMENT PRIMARY KEY,

    invoice_id INT NOT NULL,
    work_id INT NULL,

    description VARCHAR(255) NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    -- lien facture
    CONSTRAINT fk_invoice_items_invoice FOREIGN KEY (invoice_id)
        REFERENCES invoices(id_invoice)
        ON DELETE CASCADE,

    -- lien ouvrage
    CONSTRAINT fk_invoice_items_work FOREIGN KEY (work_id)
        REFERENCES works(id_work)
        ON DELETE SET NULL
);
