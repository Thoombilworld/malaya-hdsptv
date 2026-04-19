<?php
// NEWS HDSPTV - runtime config bootstrap

date_default_timezone_set('UTC');

if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode', '1');
    $secureCookie = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $secureCookie,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

$envFile = __DIR__ . '/../.env.php';
if (!file_exists($envFile)) {
    define('HS_INSTALLED', false);
    define('HS_APP_NAME', 'NEWS HDSPTV');
    define('HS_BASE_URL', '/');

    if (!function_exists('hs_db')) {
        function hs_db() {
            return null;
        }
    }
    return;
}

require $envFile;

define('HS_INSTALLED', true);
define('HS_APP_NAME', $HS_APP_NAME ?? 'NEWS HDSPTV');

define('HS_BASE_URL', rtrim(($HS_BASE_URL ?? '/'), '/') . '/');

$HS_DB_HOST = $HS_DB_HOST ?? 'localhost';
$HS_DB_NAME = $HS_DB_NAME ?? 'news_hdsptv';
$HS_DB_USER = $HS_DB_USER ?? 'root';
$HS_DB_PASS = $HS_DB_PASS ?? '';

$hs_db = @mysqli_connect($HS_DB_HOST, $HS_DB_USER, $HS_DB_PASS, $HS_DB_NAME);
$hs_db_error = '';
if (!$hs_db) {
    $hs_db_error = mysqli_connect_error();
    define('HS_DB_CONNECTED', false);
} else {
    define('HS_DB_CONNECTED', true);
    mysqli_set_charset($hs_db, 'utf8mb4');
}
define('HS_DB_ERROR', $hs_db_error);

if (!function_exists('hs_db')) {
    function hs_db() {
        global $hs_db;
        return $hs_db;
    }
}
