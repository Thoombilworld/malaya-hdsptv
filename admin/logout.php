<?php
require __DIR__ . '/../bootstrap.php';
session_destroy();
header('Location: ' . hs_admin_url('login.php'));
exit;
