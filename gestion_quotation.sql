-- ------------------------------------------------
-- Table clients
-- ------------------------------------------------
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

    -- Mot de passe (hashé)
    password VARCHAR(255) NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ------------------------------------------------
-- Table des ouvrages (works)
-- ------------------------------------------------
CREATE TABLE IF NOT EXISTS works (
    id_work INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    unit VARCHAR(50) NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ------------------------------------------------
-- Table des devis (quotes)
-- ------------------------------------------------
CREATE TABLE IF NOT EXISTS quotes (
    id_quote INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    quote_number VARCHAR(50) NOT NULL,
    quote_date DATE NOT NULL,
    status ENUM('pending','signed','cancelled') NOT NULL DEFAULT 'pending',
    total_ht DECIMAL(10,2) DEFAULT 0,
    total_vat DECIMAL(10,2) DEFAULT 0,
    total_ttc DECIMAL(10,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    -- Clé étrangère vers le client
    CONSTRAINT fk_quotes_client FOREIGN KEY (client_id)
        REFERENCES gestion_client(id_client)
        ON DELETE CASCADE
);

-- ------------------------------------------------
-- Table des lignes de devis (quote_items)
-- ------------------------------------------------
CREATE TABLE IF NOT EXISTS quote_items (
    id_item INT AUTO_INCREMENT PRIMARY KEY,
    quote_id INT NOT NULL,
    work_id INT NULL,
    description VARCHAR(255) NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    -- Clé étrangère vers le devis
    CONSTRAINT fk_quote_items_quote FOREIGN KEY (quote_id)
        REFERENCES quotes(id_quote)
        ON DELETE CASCADE,

    -- Clé étrangère vers l’ouvrage
    CONSTRAINT fk_quote_items_work FOREIGN KEY (work_id)
        REFERENCES works(id_work)
        ON DELETE SET NULL
);

