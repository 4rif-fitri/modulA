CREATE DATABASE url_shortener;

USE url_shortener;

CREATE TABLE urls (

    id INT AUTO_INCREMENT PRIMARY KEY,

    original_url TEXT NOT NULL,

    short_code VARCHAR(6) UNIQUE NOT NULL,

    clicks INT DEFAULT 0,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

);