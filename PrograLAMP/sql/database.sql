-- Base de datos para acortador de URLs
CREATE DATABASE IF NOT EXISTS url_shortener
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE url_shortener;

-- Tabla principal
-- guarda cada URL acortada
CREATE TABLE IF NOT EXISTS urls (
    id           INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    short_code   VARCHAR(10)     NOT NULL,           -- codigo de 6 chars
    original_url TEXT            NOT NULL,           -- URL completa
    created_at   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    click_count  INT UNSIGNED    NOT NULL DEFAULT 0, -- contador

    PRIMARY KEY (id),
    UNIQUE KEY uq_short_code (short_code)            -- O(1) diria Torres!!
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de clicks
-- registra cada visita a una URL corta
CREATE TABLE IF NOT EXISTS clicks (
    id           INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    url_id       INT UNSIGNED    NOT NULL,
    ip_address   VARCHAR(45)     NOT NULL,           -- IPv4 y IPv6
    country      VARCHAR(100)    NOT NULL DEFAULT 'Unknown',
    country_code VARCHAR(2)      NOT NULL DEFAULT '--',
    accessed_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    KEY idx_url_id      (url_id),
    KEY idx_accessed_at (accessed_at),              -- queries por fecha

    CONSTRAINT fk_clicks_url
        FOREIGN KEY (url_id) REFERENCES urls(id)
        ON DELETE CASCADE                           -- si se borra URL, se borran respectivos clicks
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Datos de ejemplo para asegurar que todo ta bien
INSERT IGNORE INTO urls (short_code, original_url, click_count) VALUES
    ('google1', 'https://www.google.com',         3),
    ('github1', 'https://github.com',             1),
    ('wiki001', 'https://es.wikipedia.org/wiki/LAMP', 0);

-- Clicks de ejemplo (id=1)
INSERT IGNORE INTO clicks (url_id, ip_address, country, country_code) VALUES
    (1, '187.216.1.1',  'Mexico',        'MX'),
    (1, '200.91.1.1',   'Argentina',     'AR'),
    (1, '200.91.1.2',   'Argentina',     'AR'),
    (2, '8.8.8.8',      'United States', 'US');
