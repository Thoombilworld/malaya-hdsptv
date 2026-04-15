<?php
require_once __DIR__ . '/config.php';

function lnp_latest_posts($limit = 10) {
    return hs_latest_posts($limit);
}

function lnp_settings() {
    return hs_settings();
}

function lnp_db_connected() {
    return function_exists('hs_db_connected') ? hs_db_connected() : (bool) hs_db();
}

function lnp_post_url($slug) {
    return hs_post_url($slug);
}

function lnp_category_url($slug) {
    return hs_category_url($slug);
}
