<?php

declare(strict_types=1);

$root = dirname(__DIR__);
chdir($root);

$errors = [];

function err(array &$errors, string $message): void
{
    $errors[] = $message;
}

$requiredDirs = [
    'admin',
    'admin/content',
    'app/Modules/Admin',
    'app/Modules/Content',
    'app/Views/frontend',
    'assets/css',
    'assets/js',
    'install',
    'auth',
    'writable/logs',
    'writable/uploads/images',
];

$requiredFiles = [
    'bootstrap.php',
    'config/config.php',
    'index.php',
    'post.php',
    'category.php',
    'tag.php',
    'search.php',
    'admin/_layout.php',
    'admin/index.php',
    'admin/login.php',
    'admin/content/articles.php',
    'assets/css/style.css',
    'assets/js/pwa.js',
    '.htaccess',
    'manifest.webmanifest',
    'service-worker.js',
    'install/index.php',
    'install/install.sql',
];

foreach ($requiredDirs as $dir) {
    if (!is_dir($dir)) {
        err($errors, "Missing required directory: {$dir}");
    }
}

foreach ($requiredFiles as $file) {
    if (!is_file($file)) {
        err($errors, "Missing required file: {$file}");
    }
}

require_once $root . '/bootstrap.php';
$routes = function_exists('hs_routes') ? hs_routes() : [];

if (empty($routes)) {
    err($errors, 'Route registry (hs_routes) is empty or unavailable.');
} else {
    foreach ($routes as $name => $path) {
        if (!is_string($path) || $path === '') {
            err($errors, "Route '{$name}' has invalid empty path.");
            continue;
        }
        if (strpos($path, ':') !== false) {
            continue;
        }
        if ($path === '/' || $path === '') {
            continue;
        }
        $candidate = ltrim($path, '/');
        if (is_file($candidate) || is_dir($candidate)) {
            continue;
        }
        if (is_file($candidate . '.php')) {
            continue;
        }
        err($errors, "Route '{$name}' points to non-existing path: {$path}");
    }
}

$sourceFiles = [];
foreach (['php', 'js', 'css', 'html'] as $ext) {
    $cmd = "find . -type f -name '*." . $ext . "' -not -path './.git/*'";
    $list = trim((string)shell_exec($cmd));
    if ($list === '') {
        continue;
    }
    $sourceFiles = array_merge($sourceFiles, array_filter(explode("\n", $list)));
}

$routePattern = "/hs_route\\(['\\\"]([^'\\\"]+)['\\\"]/";
$basePattern = "/hs_base_url\\(['\\\"]([^'\\\"]+)['\\\"]/";
$adminPattern = "/hs_admin_url\\(['\\\"]([^'\\\"]*)['\\\"]/";
$adminContentPattern = "/hs_admin_content_url\\(['\\\"]([^'\\\"]*)['\\\"]/";

foreach ($sourceFiles as $file) {
    $content = @file_get_contents($file);
    if ($content === false) {
        err($errors, "Unable to read source file: {$file}");
        continue;
    }

    if (preg_match_all($routePattern, $content, $m)) {
        foreach ($m[1] as $routeName) {
            if (!isset($routes[$routeName])) {
                err($errors, "{$file}: unknown route name '{$routeName}' in hs_route().");
            }
        }
    }

    if (preg_match_all($basePattern, $content, $m)) {
        foreach ($m[1] as $path) {
            $path = ltrim($path, '/');
            $path = preg_replace('/[#?].*$/', '', $path);
            if ($path === '' || strpos($path, '{') !== false) {
                continue;
            }
            if (preg_match('#^(https?:)?//#', $path)) {
                continue;
            }
            if (is_file($path) || is_dir($path)) {
                continue;
            }
            if (is_file($path . '.php')) {
                continue;
            }
            if (preg_match('#^(search|post|category|tag|about|contact|breaking|trending|video|gallery|live|profile|saved|notifications|sitemap\.xml|robots\.txt)$#', $path)) {
                continue;
            }
            err($errors, "{$file}: unresolved hs_base_url path '{$path}'.");
        }
    }

    if (preg_match_all($adminPattern, $content, $m)) {
        foreach ($m[1] as $path) {
            $path = trim($path);
            if ($path === '') {
                $path = 'index.php';
            } elseif (strpos($path, '.') === false) {
                $path .= '.php';
            }
            $full = 'admin/' . ltrim($path, '/');
            if (!is_file($full)) {
                err($errors, "{$file}: unresolved hs_admin_url path '{$full}'.");
            }
        }
    }

    if (preg_match_all($adminContentPattern, $content, $m)) {
        foreach ($m[1] as $path) {
            $path = trim($path);
            if ($path === '') {
                $path = 'index.php';
            } elseif (strpos($path, '.') === false) {
                $path .= '.php';
            }
            $full = 'admin/content/' . ltrim($path, '/');
            if (!is_file($full)) {
                err($errors, "{$file}: unresolved hs_admin_content_url path '{$full}'.");
            }
        }
    }
}

if (!empty($errors)) {
    fwrite(STDERR, "HDSPTV integrity validation failed:\n");
    foreach ($errors as $e) {
        fwrite(STDERR, " - {$e}\n");
    }
    exit(1);
}

echo "HDSPTV integrity validation passed.\n";
