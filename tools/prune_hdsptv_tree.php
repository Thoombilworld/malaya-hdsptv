<?php

declare(strict_types=1);

$root = dirname(__DIR__);
chdir($root);

$manifest = require __DIR__ . '/hdsptv_manifest.php';
$allowedTopLevel = $manifest['allowed_top_level'] ?? [];

$apply = in_array('--apply', $argv, true);
$entries = array_values(array_filter(scandir('.') ?: [], static fn (string $entry): bool => $entry !== '.' && $entry !== '..'));
$remove = [];

foreach ($entries as $entry) {
    if (!in_array($entry, $allowedTopLevel, true)) {
        $remove[] = $entry;
    }
}

if (empty($remove)) {
    echo "No extra top-level entries found.\n";
    exit(0);
}

echo $apply
    ? "Pruning extra top-level entries:\n"
    : "Dry run - extra top-level entries found (use --apply to delete):\n";

foreach ($remove as $entry) {
    echo " - {$entry}\n";
    if (!$apply) {
        continue;
    }

    if (is_dir($entry) && !is_link($entry)) {
        exec('rm -rf ' . escapeshellarg($entry), $output, $code);
        if ($code !== 0) {
            fwrite(STDERR, "Failed to remove directory: {$entry}\n");
            exit(1);
        }
        continue;
    }

    if (file_exists($entry) && !unlink($entry)) {
        fwrite(STDERR, "Failed to remove file: {$entry}\n");
        exit(1);
    }
}

echo $apply ? "Prune complete.\n" : "Dry run complete.\n";
