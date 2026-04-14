<?php
session_start();
require __DIR__ . '/config/config.php';

function hs_base_url($path = '') {
    return HS_BASE_URL . ltrim($path, '/');
}

function hs_view($view, $data = []) {
    extract($data);
    include __DIR__ . '/app/Views/' . $view . '.php';
}

function hs_settings() {
    static $settings = null;
    if ($settings !== null) return $settings;

    $settings = [
        'site_title' => HS_APP_NAME,
        'tagline'    => 'News for India, GCC, Kerala & the World',
        'logo'       => hs_base_url('assets/images/logo.png'),
    ];

    if (defined('HS_INSTALLED') && HS_INSTALLED) {
        $res = mysqli_query(hs_db(), "SELECT `key`, `value` FROM hs_settings");
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $settings[$row['key']] = $row['value'];
            }
        }
    }
    return $settings;
}

// Admin auth helpers
function hs_is_admin_logged_in() {
    return !empty($_SESSION['hs_admin_id']);
}
function hs_require_admin() {
    if (!hs_is_admin_logged_in()) {
        header('Location: ' . hs_base_url('admin/login.php'));
        exit;
    }
}

// Frontend user helpers
function hs_current_user() {
    if (empty($_SESSION['hs_user_id'])) return null;
    $id = (int) $_SESSION['hs_user_id'];
    $res = mysqli_query(hs_db(), "SELECT id, name, email, is_premium FROM hs_frontend_users WHERE id = " . $id . " LIMIT 1");
    return $res ? mysqli_fetch_assoc($res) : null;
}
function hs_require_user() {
    if (!hs_current_user()) {
        header('Location: ' . hs_base_url('auth/login.php'));
        exit;
    }
}
