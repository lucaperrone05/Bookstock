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



-- POPOLAMENTO DEL DB
INSERT INTO users (username, password) VALUES
    ('Luca', '12345'),
    ('Onny', '123456');

INSERT INTO categories (name) VALUES
    ('Narrativa'),
    ('Saggistica'),
    ('Informatica'),
    ('Ragazzi'),
    ('Fantascienza'),
    ('Gialli e Thriller'),
    ('Storia'),
    ('Filosofia');

INSERT INTO authors (name) VALUES
    ('Umberto Eco'),
    ('Italo Calvino'),
    ('Robert C. Martin'),
    ('J.K. Rowling'),
    ('George Orwell'),
    ('Isaac Asimov'),
    ('Agatha Christie'),
    ('Yuval Noah Harari'),
    ('Stephen King'),
    ('Friedrich Nietzsche');

INSERT INTO publishers (name, website) VALUES
    ('Bompiani',        'https://www.bompiani.it'),
    ('Mondadori',       'https://www.mondadori.it'),
    ('Pearson',         'https://www.pearson.com'),
    ('Salani',          'https://www.salani.it'),
    ('Einaudi',         'https://www.einaudi.it'),
    ('Feltrinelli',     'https://www.feltrinelli.it'),
    ('Sperling & Kupfer','https://www.sperling.it'),
    ('Hoepli',          'https://www.hoepli.it');

INSERT INTO books (title, isbn, author_id, category_id, publisher_id, publish_year, price, stock_qty, stock_alert_qty) VALUES
    -- Umberto Eco
    ('Il Nome della Rosa',              '9788845292613', 1, 1, 1, 1980, 14.90, 20,  5),
    ('Il Pendolo di Foucault',          '9788845292614', 1, 1, 1, 1988, 15.90,  0,  5),
    ('Il Cimitero di Praga',            '9788845265303', 1, 1, 1, 2010, 19.00,  6,  5),

    -- Italo Calvino
    ('Le Cosmicomiche',                 '9788806220385', 2, 5, 2, 1965, 12.00,  3,  5),
    ('Se una notte d inverno',          '9788806220386', 2, 1, 5, 1979, 13.00,  7,  5),
    ('Il Barone Rampante',              '9788806220387', 2, 1, 5, 1957, 11.00, 10,  5),

    -- Robert C. Martin
    ('Clean Code',                      '9780132350884', 3, 3, 3, 2008, 39.90,  8,  5),
    ('The Clean Coder',                 '9780137081073', 3, 3, 3, 2011, 35.00,  4,  5),
    ('Clean Architecture',              '9780134494166', 3, 3, 3, 2017, 42.00,  2,  3),

    -- J.K. Rowling
    ('Harry Potter e la Pietra',        '9788867158157', 4, 4, 4, 1997, 19.90, 15,  5),
    ('Harry Potter e la Camera',        '9788867158158', 4, 4, 4, 1998, 19.90, 12,  5),
    ('Harry Potter e il Prigioniero',   '9788867158159', 4, 4, 4, 1999, 19.90,  1,  5),

    -- George Orwell
    ('1984',                            '9788804667308', 5, 1, 2, 1949, 12.00, 18,  5),
    ('La Fattoria degli Animali',       '9788804667309', 5, 1, 2, 1945,  9.90, 11,  5),

    -- Isaac Asimov
    ('Io Robot',                        '9788804668235', 6, 5, 2, 1950, 13.00,  9,  5),
    ('Fondazione',                      '9788804668236', 6, 5, 2, 1951, 14.00,  5,  5),
    ('Il Ciclo dei Robot',              '9788804668237', 6, 5, 2, 1954, 16.00,  0,  5),

    -- Agatha Christie
    ('Assassinio sull Oriente Express', '9788804700890', 7, 6, 2, 1934, 11.00, 13,  5),
    ('Dieci Piccoli Indiani',           '9788804700891', 7, 6, 2, 1939, 10.00,  8,  5),
    ('Morte sul Nilo',                  '9788804700892', 7, 6, 2, 1937, 11.00,  3,  5),

    -- Yuval Noah Harari
    ('Sapiens',                         '9788858119778', 8, 2, 6, 2011, 18.00, 14,  5),
    ('Homo Deus',                       '9788858124376', 8, 2, 6, 2015, 18.00,  7,  5),
    ('21 Lezioni per il XXI Secolo',    '9788858131442', 8, 2, 6, 2018, 18.00,  4,  5),

    -- Stephen King
    ('It',                              '9788846203661', 9, 6, 7, 1986, 22.00, 10,  5),
    ('Shining',                         '9788846203662', 9, 6, 7, 1977, 16.00,  6,  5),
    ('The Stand',                       '9788846203663', 9, 6, 7, 1978, 24.00,  0,  5);

