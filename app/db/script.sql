DROP TABLE IF EXISTS users CASCADE;
DROP TABLE IF EXISTS articles CASCADE;
DROP TABLE IF EXISTS categories CASCADE;
DROP TABLE IF EXISTS comments CASCADE;
DROP TABLE IF EXISTS pages CASCADE;

CREATE TABLE categories (
    id SERIAL PRIMARY KEY,
    label VARCHAR(50)
);

CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    login VARCHAR(50) NOT NULL,
    email VARCHAR(320) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    status SMALLINT DEFAULT 0,
    validate BOOLEAN DEFAULT FALSE,
    validation_token VARCHAR(32)
);

CREATE TABLE articles (
    id SERIAL PRIMARY KEY,
    title VARCHAR(170) NOT NULL,
    content TEXT NOT NULL,
    keywords TEXT NOT NULL,
    picture_url VARCHAR(255) NULL,
    id_category INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_creator INT NOT NULL,
    updated_at TIMESTAMP NULL,
    id_updator INT NULL,
    published_at TIMESTAMP DEFAULT NULL,
    FOREIGN KEY (id_category) REFERENCES categories(id),
    FOREIGN KEY (id_creator) REFERENCES users(id),
    FOREIGN KEY (id_updator) REFERENCES users(id)
);

CREATE TABLE comments (
    id SERIAL PRIMARY KEY,
    id_article INT,
    id_comment_response INT DEFAULT NULL,
    id_user INT,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    valid BOOLEAN DEFAULT FALSE,
    validate_at TIMESTAMP NULL,
    id_validator INT NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (id_article) REFERENCES articles(id),
    FOREIGN KEY (id_comment_response) REFERENCES comments(id),
    FOREIGN KEY (id_user) REFERENCES users(id),
    FOREIGN KEY (id_validator) REFERENCES users(id)
);

CREATE TABLE pages (
    id SERIAL PRIMARY KEY,
    url VARCHAR(50) NOT NULL,
    title VARCHAR(255) NOT NULL,
    html TEXT NOT NULL,
    css TEXT NOT NULL,
    meta_description TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_creator INT NOT NULL,
    updated_at TIMESTAMP NULL,
    id_updator INT NULL,
    FOREIGN KEY (id_creator) REFERENCES users(id),
    FOREIGN KEY (id_updator) REFERENCES users(id)
);

INSERT INTO categories (label) VALUES
('Musique'),
('Peinture'),
('Sculpture'),
('Photographie'),
('Dessin'),
('Cinéma'),
('Littérature'),
('Danse'),
('Théâtre'),
('Autre');