<?php
require_once __DIR__ . '/config.php';

function lnp_latest_posts($limit = 10) {
    return hs_latest_posts($limit);
}

function lnp_settings() {
    return hs_settings();
}
