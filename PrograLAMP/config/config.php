<?php
// ====================================================
// config.php - configuracion local (NO subir a GITHUB)
// ====================================================

// --- Base de datos ---
define('DB_HOST',    'localhost');
define('DB_NAME',    'url_shortener');
define('DB_USER',    'root');
define('DB_PASS',    '');
define('DB_CHARSET', 'utf8mb4');

// --- URL base de la aplicacion (sin trailing slash) ---
// Cambia BASE_PATH si la app vive en una subcarpeta de Apache
define('BASE_URL',  'http://localhost/url-shortener');
define('BASE_PATH', '/url-shortener');

// --- API externa de geolocalizacion ---
define('IP_API_BASE', 'http://ip-api.com/json/');

// --- Configuracion del codigo corto ---
define('SHORT_CODE_LENGTH', 6);

// --- Entorno ---
define('APP_ENV', 'development');
