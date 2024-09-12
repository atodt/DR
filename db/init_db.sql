CREATE DATABASE IF NOT EXISTS app;
USE app;

CREATE TABLE IF NOT EXISTS product
(
    id          INT AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(255) NOT NULL,
    description VARCHAR(255) NOT NULL,
    category    VARCHAR(255) NOT NULL,
    state       BOOLEAN      NOT NULL
);

INSERT INTO product (title, description, category, state)
VALUES ('Samsung galaxys10', 'Mobile Phone from Samsung', 'Phone', true),
       ('Apple iPhone 13', 'Apple mobilephone', 'Phone', true),
       ('Apple watch 8', 'Apple Watch 8 Smartwatch', 'Smartwatch', true),
       ('Garmin ApproachS62', 'Garmin golf watch Smartwatch', 'Smartwatch', true),
       ('Apple air pods', 'ANC headphones from Apple', 'Headphone', true);