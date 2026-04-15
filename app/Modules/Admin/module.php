<?php

function hs_admin_module_boot(string $permission, string $title, string $pageTitle, string $active): void {
    hs_require_admin();
    if ($permission !== '') {
        hs_require_permission($permission);
    }
    require __DIR__ . '/../../../admin/_layout.php';
    hs_admin_shell_start($title, $pageTitle, $active);
}

function hs_admin_module_end(): void {
    hs_admin_shell_end();
}

function hs_admin_back_link(string $href = 'admin/index.php', string $label = 'Back to Admin Dashboard'): string {
    return '<p style="margin:0 0 12px;"><a href="' . htmlspecialchars(hs_base_url($href), ENT_QUOTES, 'UTF-8') . '">← ' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</a></p>';
}
