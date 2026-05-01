-- BOOKSTOCK — Schema Database
CREATE DATABASE IF NOT EXISTS bookstock
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE bookstock;

-- UTENTI (login)
CREATE TABLE users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(100) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL
);

-- AUTORI
CREATE TABLE authors (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(150) NOT NULL
);

-- CATEGORIE
CREATE TABLE categories (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL
);

-- CASE EDITRICI
CREATE TABLE publishers (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(150) NOT NULL,
    website    VARCHAR(255)
);

-- LIBRI
CREATE TABLE books (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    title           VARCHAR(255) NOT NULL,
    isbn            VARCHAR(20) UNIQUE,
    author_id       INT NOT NULL,
    category_id     INT NOT NULL,
    publisher_id    INT,
    publish_year    YEAR,
    description     TEXT,
    price           DECIMAL(10,2) DEFAULT 0.00,
    stock_qty       INT DEFAULT 0,
    stock_alert_qty INT DEFAULT 5,

    FOREIGN KEY (author_id)    REFERENCES authors(id)    ON DELETE RESTRICT,
    FOREIGN KEY (category_id)  REFERENCES categories(id) ON DELETE RESTRICT,
    FOREIGN KEY (publisher_id) REFERENCES publishers(id) ON DELETE SET NULL
);

-- MOVIMENTI DI MAGAZZINO
CREATE TABLE stock_movements (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    book_id    INT NOT NULL,
    type       ENUM('carico', 'scarico') NOT NULL,
    quantity   INT NOT NULL,
    note       VARCHAR(255),

    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
);

-- POPOLAMENTO DEL DB
INSERT INTO users (username, password) VALUES
    ('Luca', '12345'),
    ('Onny', '123456');

INSERT INTO categories (name) VALUES
    ('Narrativa'),
    ('Saggistica'),
    ('Informatica'),
    ('Ragazzi');

INSERT INTO authors (name) VALUES
    ('Umberto Eco'),
    ('Italo Calvino'),
    ('Robert C. Martin'),
    ('J.K. Rowling');

INSERT INTO publishers (name, website) VALUES
    ('Bompiani',  'https://www.bompiani.it'),
    ('Mondadori', 'https://www.mondadori.it'),
    ('Pearson',   'https://www.pearson.com'),
    ('Salani',    'https://www.salani.it');

INSERT INTO books (title, isbn, author_id, category_id, publisher_id, publish_year, price, stock_qty, stock_alert_qty) VALUES
    ('Il Nome della Rosa',         '9788845292613', 1, 1, 1, 1980, 14.90, 20, 5),
    ('Il Pendolo di Foucault',     '9788845292614', 1, 1, 1, 1988, 15.90,  0, 5),
    ('Le Cosmicomiche',            '9788806220385', 2, 1, 2, 1965, 12.00,  3, 5),
    ('Se una notte d inverno',     '9788806220386', 2, 1, 2, 1979, 13.00,  7, 5),
    ('Clean Code',                 '9780132350884', 3, 3, 3, 2008, 39.90,  8, 5),
    ('Harry Potter e la Pietra',   '9788867158157', 4, 4, 4, 1997, 19.90, 15, 5);

INSERT INTO stock_movements (book_id, type, quantity, note) VALUES
    (1, 'carico',  20, 'Primo carico iniziale'),
    (2, 'carico',   5, 'Primo carico iniziale'),
    (2, 'scarico',  5, 'Vendite settimana 1'),   -- esaurito
    (3, 'carico',  10, 'Primo carico iniziale'),
    (3, 'scarico',  7, 'Vendite settimana 1'),   -- sotto soglia
    (4, 'carico',   7, 'Primo carico iniziale'),
    (5, 'carico',   8, 'Primo carico iniziale'),
    (6, 'carico',  15, 'Primo carico iniziale');