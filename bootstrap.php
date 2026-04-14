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

function hs_query_value($sql, $default = 0) {
    if (!defined('HS_INSTALLED') || !HS_INSTALLED) {
        return $default;
    }
    $res = mysqli_query(hs_db(), $sql);
    if (!$res) {
        return $default;
    }
    $row = mysqli_fetch_row($res);
    return $row[0] ?? $default;
}

function hs_count_posts_today() {
    return (int) hs_query_value("SELECT COUNT(*) FROM hs_posts WHERE DATE(created_at)=CURDATE()", 0);
}

function hs_count_posts_by_status($status) {
    $status = mysqli_real_escape_string(hs_db(), (string)$status);
    return (int) hs_query_value("SELECT COUNT(*) FROM hs_posts WHERE status='{$status}'", 0);
}

function hs_count_breaking_active() {
    return (int) hs_query_value("SELECT COUNT(*) FROM hs_posts WHERE status='published' AND is_breaking=1", 0);
}

function hs_latest_posts($limit = 8) {
    if (!defined('HS_INSTALLED') || !HS_INSTALLED) {
        return [];
    }
    $limit = max(1, (int)$limit);
    $sql = "SELECT p.title, p.status, p.created_at, c.name AS category_name
            FROM hs_posts p
            LEFT JOIN hs_categories c ON c.id = p.category_id
            ORDER BY p.created_at DESC
            LIMIT {$limit}";
    $res = mysqli_query(hs_db(), $sql);
    if (!$res) {
        return [];
    }

    $rows = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $rows[] = $row;
    }
    return $rows;
}

function hs_top_categories($limit = 5) {
    if (!defined('HS_INSTALLED') || !HS_INSTALLED) {
        return [];
    }
    $limit = max(1, (int)$limit);
    $sql = "SELECT c.name, COUNT(*) AS total
            FROM hs_posts p
            LEFT JOIN hs_categories c ON c.id = p.category_id
            WHERE p.status='published'
            GROUP BY p.category_id
            ORDER BY total DESC
            LIMIT {$limit}";
    $res = mysqli_query(hs_db(), $sql);
    if (!$res) {
        return [];
    }

    $rows = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $rows[] = $row;
    }
    return $rows;
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
