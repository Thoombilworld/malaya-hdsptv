<?php

declare(strict_types=1);

$root = dirname(__DIR__);
chdir($root);

$errors = [];
$manifest = require __DIR__ . '/hdsptv_manifest.php';

function err(array &$errors, string $message): void
{
    $errors[] = $message;
}

function normalize_path(string $path): string
{
    $path = str_replace('\\', '/', $path);
    $path = preg_replace('#/+#', '/', $path) ?? $path;
    $segments = [];

    foreach (explode('/', $path) as $segment) {
        if ($segment === '' || $segment === '.') {
            continue;
        }
        if ($segment === '..') {
            array_pop($segments);
            continue;
        }
        $segments[] = $segment;
    }

    return implode('/', $segments);
}

function should_skip_reference(string $value): bool
{
    $value = trim($value);
    if ($value === '' || $value === '/' || $value[0] === '#') {
        return true;
    }

    if (preg_match('#^(https?:)?//#i', $value) === 1) {
        return true;
    }

    if (preg_match('#^(mailto:|tel:|javascript:|data:)#i', $value) === 1) {
        return true;
    }

    if (str_contains($value, '<?') || str_contains($value, '?>')) {
        return true;
    }

    if (str_contains($value, '{{') || str_contains($value, '}}')) {
        return true;
    }

    if (str_contains($value, '{') || str_contains($value, '}')) {
        return true;
    }

    if (str_contains($value, '$')) {
        return true;
    }

    return false;
}

function clean_reference(string $value): string
{
    $value = trim($value);
    $value = preg_replace('/[#?].*$/', '', $value) ?? $value;
    return trim($value);
}

function candidate_paths(string $reference, string $sourceFile): array
{
    $reference = clean_reference($reference);
    if ($reference === '') {
        return [];
    }

    $baseDir = normalize_path(dirname($sourceFile));
    $paths = [];

    if ($reference[0] === '/') {
        $paths[] = normalize_path(substr($reference, 1));
        $paths[] = normalize_path(($baseDir === '.' ? '' : $baseDir . '/') . ltrim($reference, '/'));
    } else {
        $paths[] = normalize_path(($baseDir === '.' ? '' : $baseDir . '/') . $reference);
        $paths[] = normalize_path($reference);
    }

    return array_values(array_unique(array_filter($paths, static fn (string $p): bool => $p !== '')));
}

function path_exists_with_fallbacks(string $path): bool
{
    if ($path === '') {
        return true;
    }

    if (is_file($path) || is_dir($path)) {
        return true;
    }

    if (is_file($path . '.php')) {
        return true;
    }

    if (is_file($path . '/index.php')) {
        return true;
    }

    return false;
}

function reference_resolves(string $reference, string $sourceFile, array $routeTargetCandidates, array $virtualTargets): bool
{
    $candidates = candidate_paths($reference, $sourceFile);
    foreach ($candidates as $candidate) {
        $trimmed = trim($candidate, '/');
        if (
            path_exists_with_fallbacks($candidate)
            || in_array($trimmed, $routeTargetCandidates, true)
            || in_array($trimmed, $virtualTargets, true)
        ) {
            return true;
        }
    }

    return false;
}

function looks_like_path_reference(string $reference, array $routeTargetCandidates, array $virtualTargets): bool
{
    $clean = clean_reference($reference);
    if ($clean === '') {
        return false;
    }

    if (str_contains($clean, '/') || str_contains($clean, '.')) {
        return true;
    }

    return in_array(trim($clean, '/'), $routeTargetCandidates, true)
        || in_array(trim($clean, '/'), $virtualTargets, true);
}

function collect_source_files(array $extensions): array
{
    $files = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator('.', FilesystemIterator::SKIP_DOTS)
    );

    foreach ($iterator as $item) {
        if (!$item->isFile()) {
            continue;
        }

        $path = str_replace('\\', '/', $item->getPathname());
        if (str_starts_with($path, './.git/')) {
            continue;
        }

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (in_array($ext, $extensions, true)) {
            $files[] = ltrim($path, './');
        }
    }

    sort($files);
    return $files;
}

$requiredDirs = $manifest['required_dirs'] ?? [];
$requiredFiles = $manifest['required_files'] ?? [];
$allowedTopLevel = $manifest['allowed_top_level'] ?? [];

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

$topLevelEntries = array_values(array_filter(scandir('.') ?: [], static fn (string $entry): bool => $entry !== '.' && $entry !== '..'));
foreach ($topLevelEntries as $entry) {
    if (!in_array($entry, $allowedTopLevel, true)) {
        err($errors, "Unexpected top-level entry (remove if not needed): {$entry}");
    }
}

require_once $root . '/bootstrap.php';
$routes = function_exists('hs_routes') ? hs_routes() : [];
$routeTargetCandidates = [];
$virtualTargets = ['sitemap.xml', 'robots.txt'];

if (empty($routes)) {
    err($errors, 'Route registry (hs_routes) is empty or unavailable.');
} else {
    foreach ($routes as $name => $path) {
        if (!is_string($path) || trim($path) === '') {
            err($errors, "Route '{$name}' has invalid empty path.");
            continue;
        }

        $normalized = ltrim(trim($path), '/');
        $normalized = preg_replace('/:[A-Za-z0-9_]+/', '', $normalized) ?? $normalized;
        $normalized = trim((string) $normalized, '/');

        if ($normalized !== '') {
            $routeTargetCandidates[] = $normalized;
        }

        if (str_contains($path, ':')) {
            continue;
        }

        if ($path === '/' || $normalized === '') {
            continue;
        }

        if (!path_exists_with_fallbacks($normalized)) {
            err($errors, "Route '{$name}' points to non-existing path: {$path}");
        }
    }
}

$routeTargetCandidates = array_values(array_unique($routeTargetCandidates));

$sourceFiles = collect_source_files(['php', 'html', 'js', 'css']);

$routePattern = "/hs_route\\(['\"]([^'\"]+)['\"]/";
$basePattern = "/hs_base_url\\(['\"]([^'\"]+)['\"]/";
$adminPattern = "/hs_admin_url\\(['\"]([^'\"]*)['\"]/";
$adminContentPattern = "/hs_admin_content_url\\(['\"]([^'\"]*)['\"]/";

$htmlReferencePatterns = [
    '/(?:href|src|action|poster|data-src|data-href)\\s*=\\s*["\\\']([^"\\\']+)["\\\']/i',
];

$jsReferencePatterns = [
    '/(?:fetch|register|importScripts)\\(\\s*["\\\']([^"\\\']+)["\\\']/i',
];

$cssReferencePatterns = [
    '/url\\(\\s*["\\\']?([^"\\\')]+)["\\\']?\\s*\\)/i',
];

$includePatterns = [
    '/(?:require|require_once|include|include_once)\\s+__DIR__\\s*\\.\\s*[\'"]([^\'"]+)[\'"]/i',
    '/(?:require|require_once|include|include_once)\\s*[\'"]([^\'"]+)[\'"]/i',
];

foreach ($sourceFiles as $file) {
    $content = @file_get_contents($file);
    if ($content === false) {
        err($errors, "Unable to read source file: {$file}");
        continue;
    }

    if (preg_match_all($routePattern, $content, $matches)) {
        foreach ($matches[1] as $routeName) {
            if (!isset($routes[$routeName])) {
                err($errors, "{$file}: unknown route name '{$routeName}' in hs_route().");
            }
        }
    }

    if (preg_match_all($basePattern, $content, $matches)) {
        foreach ($matches[1] as $path) {
            if (should_skip_reference($path)) {
                continue;
            }

            if (!reference_resolves($path, $file, $routeTargetCandidates, $virtualTargets)) {
                err($errors, "{$file}: unresolved hs_base_url path '{$path}'.");
            }
        }
    }

    if (preg_match_all($adminPattern, $content, $matches)) {
        foreach ($matches[1] as $path) {
            $path = trim($path);
            if ($path === '') {
                $path = 'index.php';
            } elseif (!str_contains($path, '.')) {
                $path .= '.php';
            }

            $full = 'admin/' . ltrim($path, '/');
            if (!is_file($full)) {
                err($errors, "{$file}: unresolved hs_admin_url path '{$full}'.");
            }
        }
    }

    if (preg_match_all($adminContentPattern, $content, $matches)) {
        foreach ($matches[1] as $path) {
            $path = trim($path);
            if ($path === '') {
                $path = 'index.php';
            } elseif (!str_contains($path, '.')) {
                $path .= '.php';
            }

            $full = 'admin/content/' . ltrim($path, '/');
            if (!is_file($full)) {
                err($errors, "{$file}: unresolved hs_admin_content_url path '{$full}'.");
            }
        }
    }

    if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
        foreach ($includePatterns as $pattern) {
            if (!preg_match_all($pattern, $content, $matches)) {
                continue;
            }

            foreach ($matches[1] as $includePath) {
                if (should_skip_reference($includePath)) {
                    continue;
                }

                $resolved = false;
                foreach (candidate_paths($includePath, $file) as $candidate) {
                    if (path_exists_with_fallbacks($candidate)) {
                        $resolved = true;
                        break;
                    }
                }

                if (!$resolved) {
                    err($errors, "{$file}: unresolved include/require path '{$includePath}'.");
                }
            }
        }
    }

    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $referencePatterns = [];
    if ($extension === 'html' || $extension === 'php') {
        $referencePatterns = $htmlReferencePatterns;
    } elseif ($extension === 'js') {
        $referencePatterns = $jsReferencePatterns;
    } elseif ($extension === 'css') {
        $referencePatterns = $cssReferencePatterns;
    }

    foreach ($referencePatterns as $pattern) {
        if (!preg_match_all($pattern, $content, $matches)) {
            continue;
        }

        foreach ($matches[1] as $reference) {
            if (should_skip_reference($reference)) {
                continue;
            }
            if (!looks_like_path_reference($reference, $routeTargetCandidates, $virtualTargets)) {
                continue;
            }

            if (!reference_resolves($reference, $file, $routeTargetCandidates, $virtualTargets)) {
                err($errors, "{$file}: unresolved static reference '{$reference}'.");
            }
        }
    }
}

$manifestPath = 'manifest.webmanifest';
if (is_file($manifestPath)) {
    $manifest = json_decode((string) file_get_contents($manifestPath), true);
    if (!is_array($manifest)) {
        err($errors, 'manifest.webmanifest is not valid JSON.');
    } else {
        foreach (['start_url', 'scope'] as $key) {
            if (empty($manifest[$key]) || !is_string($manifest[$key])) {
                continue;
            }

            $reference = $manifest[$key];
            if (should_skip_reference($reference)) {
                continue;
            }

            if (!reference_resolves($reference, $manifestPath, $routeTargetCandidates, $virtualTargets)) {
                err($errors, "manifest.webmanifest: unresolved {$key} '{$reference}'.");
            }
        }

        if (!empty($manifest['icons']) && is_array($manifest['icons'])) {
            foreach ($manifest['icons'] as $icon) {
                if (!is_array($icon) || empty($icon['src']) || !is_string($icon['src'])) {
                    err($errors, 'manifest.webmanifest: icon entry missing valid src.');
                    continue;
                }

                $candidates = candidate_paths($icon['src'], $manifestPath);
                $found = false;
                foreach ($candidates as $candidate) {
                    if (path_exists_with_fallbacks($candidate)) {
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    err($errors, "manifest.webmanifest: unresolved icon src '{$icon['src']}'.");
                }
            }
        }
    }
}

$swPath = 'service-worker.js';
if (is_file($swPath)) {
    $swContent = (string) file_get_contents($swPath);
    if (preg_match('/STATIC_ASSETS\\s*=\\s*\\[(.*?)\\];/s', $swContent, $matches)) {
        preg_match_all('/["\\\']([^"\\\']+)["\\\']/', $matches[1], $assetMatches);
        foreach ($assetMatches[1] as $assetPath) {
            if (should_skip_reference($assetPath)) {
                continue;
            }

            if (!reference_resolves($assetPath, $swPath, $routeTargetCandidates, $virtualTargets)) {
                err($errors, "service-worker.js: unresolved STATIC_ASSETS entry '{$assetPath}'.");
            }
        }
    } else {
        err($errors, 'service-worker.js: STATIC_ASSETS array not found.');
    }
}

$errors = array_values(array_unique($errors));
sort($errors);

if (!empty($errors)) {
    fwrite(STDERR, "HDSPTV integrity validation failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "HDSPTV integrity validation passed.\n";
