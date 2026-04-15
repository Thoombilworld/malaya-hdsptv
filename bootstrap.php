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

function hs_pwa_head_tags() {
    $manifest = hs_base_url('manifest.webmanifest');
    $themeColor = '#0B1220';
    $icon192 = hs_base_url('assets/images/icons/icon-192.svg');
    return implode("\n", [
        '<meta name="theme-color" content="' . htmlspecialchars($themeColor, ENT_QUOTES, 'UTF-8') . '">',
        '<meta name="mobile-web-app-capable" content="yes">',
        '<meta name="apple-mobile-web-app-capable" content="yes">',
        '<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">',
        '<meta name="apple-mobile-web-app-title" content="' . htmlspecialchars(HS_APP_NAME, ENT_QUOTES, 'UTF-8') . '">',
        '<link rel="manifest" href="' . htmlspecialchars($manifest, ENT_QUOTES, 'UTF-8') . '">',
        '<link rel="apple-touch-icon" href="' . htmlspecialchars($icon192, ENT_QUOTES, 'UTF-8') . '">',
    ]);
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



function hs_role_permissions() {
    return [
        'admin' => ['dashboard.view','article.create','article.edit','article.publish','category.manage','tag.manage','user.manage','settings.manage','ads.manage','seo.manage'],
        'editor' => ['dashboard.view','article.create','article.edit','article.publish','category.manage','tag.manage','seo.manage'],
        'reporter' => ['dashboard.view','article.create','article.edit.own'],
    ];
}

function hs_admin_role() {
    return $_SESSION['hs_admin_role'] ?? 'reporter';
}

function hs_can($permission) {
    if (!hs_is_admin_logged_in()) {
        return false;
    }
    $role = hs_admin_role();
    $map = hs_role_permissions();
    $perms = $map[$role] ?? [];
    return in_array($permission, $perms, true);
}

function hs_require_permission($permission) {
    if (!hs_can($permission)) {
        http_response_code(403);
        echo 'Permission denied';
        exit;
    }
}

function hs_auth_attempt_key($email) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    return strtolower(trim($email)) . '|' . $ip;
}

function hs_auth_is_locked($email, $maxAttempts = 5, $lockSeconds = 900) {
    $key = hs_auth_attempt_key($email);
    $pool = $_SESSION['hs_auth_attempts'] ?? [];
    if (empty($pool[$key])) {
        return false;
    }
    $entry = $pool[$key];
    if (($entry['count'] ?? 0) < $maxAttempts) {
        return false;
    }
    $last = (int)($entry['last'] ?? 0);
    return (time() - $last) < $lockSeconds;
}

function hs_auth_record_failure($email) {
    $key = hs_auth_attempt_key($email);
    if (empty($_SESSION['hs_auth_attempts'][$key])) {
        $_SESSION['hs_auth_attempts'][$key] = ['count' => 0, 'last' => time()];
    }
    $_SESSION['hs_auth_attempts'][$key]['count']++;
    $_SESSION['hs_auth_attempts'][$key]['last'] = time();
}

function hs_auth_clear_attempts($email) {
    $key = hs_auth_attempt_key($email);
    unset($_SESSION['hs_auth_attempts'][$key]);
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



function hs_csrf_token() {
    if (empty($_SESSION['hs_csrf_token'])) {
        $_SESSION['hs_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['hs_csrf_token'];
}

function hs_csrf_input() {
    return '<input type="hidden" name="_csrf" value="' . htmlspecialchars(hs_csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

function hs_csrf_validate() {
    $token = $_POST['_csrf'] ?? '';
    $valid = !empty($_SESSION['hs_csrf_token']) && hash_equals($_SESSION['hs_csrf_token'], $token);
    if (!$valid) {
        http_response_code(422);
    }
    return $valid;
}

function hs_log_event($level, $message, array $context = []) {
    $dir = __DIR__ . '/writable/logs';
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
    $payload = [
        'time' => date('c'),
        'level' => $level,
        'message' => $message,
        'context' => $context,
    ];
    @file_put_contents($dir . '/app.log', json_encode($payload, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);
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
