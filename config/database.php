<?php

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'project_management');

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$dir      = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
define('BASE_URL', $protocol . '://' . $_SERVER['HTTP_HOST'] . $dir);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $mysqli->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    die('<h2 style="font-family:sans-serif;color:#e11d48">Database Error: ' .
        htmlspecialchars($e->getMessage()) . '</h2>');
}
