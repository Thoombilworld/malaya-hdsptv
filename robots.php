<?php
require __DIR__ . '/bootstrap.php';
header('Content-Type: text/plain; charset=utf-8');

$sitemap = hs_base_url('sitemap.xml');

echo "User-agent: *\n";
echo "Allow: /\n";
echo "Disallow: /admin/\n";
echo "Disallow: /install/\n";
echo "Disallow: /writable/\n";
echo "Disallow: /auth/reset.php\n";
echo "Sitemap: {$sitemap}\n";
