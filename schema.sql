-- Schema Database

CREATE DATABASE IF NOT EXISTS bookstock
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE bookstock;

-- AUTORI
CREATE TABLE authors (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    name      VARCHAR(150) NOT NULL,
    bio       TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- CATEGORIE
CREATE TABLE categories (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    description TEXT
);

-- LIBRI
CREATE TABLE books (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    title           VARCHAR(255) NOT NULL,
    isbn            VARCHAR(20) UNIQUE,
    author_id       INT NOT NULL,
    category_id     INT NOT NULL,
    description     TEXT,
    cover_url       VARCHAR(500),
    price           DECIMAL(10,2) DEFAULT 0.00,
    stock_qty       INT DEFAULT 0,
    stock_alert_qty INT DEFAULT 5,          -- soglia alert scorte basse
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (author_id)   REFERENCES authors(id)    ON DELETE RESTRICT,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
);

-- MOVIMENTI DI MAGAZZINO
CREATE TABLE stock_movements (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    book_id    INT NOT NULL,
    type       ENUM('carico', 'scarico', 'reso') NOT NULL,
    quantity   INT NOT NULL,
    note       VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
);

-- DATI DI ESEMPIO
INSERT INTO categories (name, description) VALUES
    ('Narrativa',   'Romanzi e racconti di finzione'),
    ('Saggistica',  'Libri di divulgazione e saggi'),
    ('Informatica', 'Manuali e libri tecnici'),
    ('Ragazzi',     'Libri per bambini e adolescenti');

INSERT INTO authors (name, bio) VALUES
    ('Umberto Eco',      'Scrittore e semiologo italiano'),
    ('Italo Calvino',    'Scrittore italiano del Novecento'),
    ('Robert C. Martin', 'Ingegnere del software, autore di Clean Code');

INSERT INTO books (title, isbn, author_id, category_id, price, stock_qty, stock_alert_qty) VALUES
    ('Il Nome della Rosa',         '9788845292613', 1, 1, 14.90, 20, 5),
    ('Le Cosmicomiche',            '9788806220385', 2, 1, 12.00,  3, 5),
    ('Clean Code',                 '9780132350884', 3, 3, 39.90,  8, 5),
    ('Il Pendolo di Foucault',     '9788845292614', 1, 1, 15.90,  0, 5);

INSERT INTO stock_movements (book_id, type, quantity, note) VALUES
    (1, 'carico',  20, 'Primo carico iniziale'),
    (2, 'carico',  10, 'Primo carico iniziale'),
    (2, 'scarico',  7, 'Vendite settimana 1'),
    (3, 'carico',   8, 'Primo carico iniziale'),
    (4, 'carico',   5, 'Primo carico iniziale'),
    (4, 'scarico',  5, 'Vendite settimana 1');