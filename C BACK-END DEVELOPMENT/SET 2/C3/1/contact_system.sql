CREATE DATABASE contact_system;

USE contact_system;

CREATE TABLE contact_submissions (

    id INT AUTO_INCREMENT PRIMARY KEY,

    name VARCHAR(255) NOT NULL,

    email VARCHAR(255) NOT NULL,

    subject VARCHAR(255) NOT NULL,

    message TEXT NOT NULL,

    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    email_sent TINYINT(1) DEFAULT 0

);