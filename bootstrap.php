<?php
session_start();
require __DIR__ . '/config/config.php';

function hs_available_locales() {
    return [
        'en' => 'English',
        'ml' => 'മലയാളം',
        'ar' => 'العربية',
        'hi' => 'हिन्दी',
    ];
}

function hs_country_locale_map() {
    return [
        'IN' => 'hi',
        'AE' => 'ar', 'SA' => 'ar', 'QA' => 'ar', 'KW' => 'ar', 'OM' => 'ar', 'BH' => 'ar',
        'EG' => 'ar', 'JO' => 'ar', 'IQ' => 'ar', 'LB' => 'ar', 'MA' => 'ar', 'DZ' => 'ar',
        'US' => 'en', 'GB' => 'en', 'CA' => 'en', 'AU' => 'en', 'NZ' => 'en',
    ];
}

function hs_set_locale($locale) {
    $_SESSION['hs_locale'] = $locale;
    setcookie('hs_locale', $locale, time() + (86400 * 180), '/');
}

function hs_detect_accept_language() {
    $header = strtolower(trim($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? ''));
    if ($header === '') {
        return null;
    }
    $supported = hs_available_locales();
    foreach (explode(',', $header) as $chunk) {
        $chunk = trim(explode(';', $chunk)[0] ?? '');
        if ($chunk === '') {
            continue;
        }
        $primary = substr($chunk, 0, 2);
        if (isset($supported[$primary])) {
            return $primary;
        }
    }
    return null;
}

function hs_detect_country_locale() {
    $country = strtoupper(trim($_SERVER['HTTP_CF_IPCOUNTRY'] ?? ($_SERVER['GEOIP_COUNTRY_CODE'] ?? '')));
    if ($country === '') {
        return null;
    }
    $map = hs_country_locale_map();
    return $map[$country] ?? null;
}

function hs_bootstrap_locale() {
    $supported = hs_available_locales();
    $requested = strtolower(trim($_GET['lang'] ?? ''));
    if ($requested !== '' && isset($supported[$requested])) {
        hs_set_locale($requested);
        return $requested;
    }

    $cookie = strtolower(trim($_COOKIE['hs_locale'] ?? ''));
    if ($cookie !== '' && isset($supported[$cookie])) {
        $_SESSION['hs_locale'] = $cookie;
        return $cookie;
    }

    $session = strtolower(trim($_SESSION['hs_locale'] ?? ''));
    if ($session !== '' && isset($supported[$session])) {
        return $session;
    }

    $countryLocale = hs_detect_country_locale();
    if ($countryLocale && isset($supported[$countryLocale])) {
        hs_set_locale($countryLocale);
        return $countryLocale;
    }

    $acceptLocale = hs_detect_accept_language();
    if ($acceptLocale && isset($supported[$acceptLocale])) {
        hs_set_locale($acceptLocale);
        return $acceptLocale;
    }

    hs_set_locale('en');
    return 'en';
}

function hs_locale() {
    return $_SESSION['hs_locale'] ?? 'en';
}

function hs_is_rtl() {
    return hs_locale() === 'ar';
}

function hs_t($key) {
    $dict = [
        'en' => [
            'international_news_network' => 'International News Network',
            'global_edition' => 'Global Edition',
            'live_desk_active' => 'Live Desk Active',
            'home' => 'Home', 'india' => 'India', 'gcc' => 'GCC', 'world' => 'World', 'sports' => 'Sports',
            'live_tv' => 'Live TV', 'search_stories' => 'Search stories', 'login' => 'Login', 'register' => 'Register',
            'install_app' => 'Install App',
        ],
        'ml' => [
            'international_news_network' => 'അന്താരാഷ്ട്ര വാർത്താ നെറ്റ്വർക്ക്',
            'global_edition' => 'ഗ്ലോബൽ എഡിഷൻ',
            'live_desk_active' => 'ലൈവ് ഡെസ്ക് സജീവമാണ്',
            'home' => 'ഹോം', 'india' => 'ഇന്ത്യ', 'gcc' => 'ജിസിസി', 'world' => 'ലോകം', 'sports' => 'കായികം',
            'live_tv' => 'ലൈവ് ടിവി', 'search_stories' => 'വാർത്തകൾ തിരയുക', 'login' => 'ലോഗിൻ', 'register' => 'രജിസ്റ്റർ',
            'install_app' => 'ആപ്പ് ഇൻസ്റ്റാൾ ചെയ്യുക',
        ],
        'ar' => [
            'international_news_network' => 'شبكة أخبار دولية',
            'global_edition' => 'النسخة العالمية',
            'live_desk_active' => 'مكتب البث المباشر نشط',
            'home' => 'الرئيسية', 'india' => 'الهند', 'gcc' => 'الخليج', 'world' => 'العالم', 'sports' => 'الرياضة',
            'live_tv' => 'البث المباشر', 'search_stories' => 'ابحث في الأخبار', 'login' => 'تسجيل الدخول', 'register' => 'إنشاء حساب',
            'install_app' => 'تثبيت التطبيق',
        ],
        'hi' => [
            'international_news_network' => 'अंतरराष्ट्रीय समाचार नेटवर्क',
            'global_edition' => 'ग्लोबल एडिशन',
            'live_desk_active' => 'लाइव डेस्क सक्रिय',
            'home' => 'होम', 'india' => 'भारत', 'gcc' => 'जीसीसी', 'world' => 'विश्व', 'sports' => 'खेल',
            'live_tv' => 'लाइव टीवी', 'search_stories' => 'समाचार खोजें', 'login' => 'लॉगिन', 'register' => 'रजिस्टर',
            'install_app' => 'ऐप इंस्टॉल करें',
        ],
    ];
    $locale = hs_locale();
    return $dict[$locale][$key] ?? $dict['en'][$key] ?? $key;
}

hs_bootstrap_locale();

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
