CREATE DATABASE shopping_list;
USE shopping_list;

CREATE TABLE items
(
    id      INT AUTO_INCREMENT PRIMARY KEY,
    name    VARCHAR(255) NOT NULL,
    checked TINYINT(1) DEFAULT 0
);