<?php

function is_admin_logged_in(): bool
{
    static $cfg = null;
    if ($cfg === null) {
        $cfg = require __DIR__ . '/../config.php';
    }

    return isset($_SESSION[$cfg['admin']['session_name']])
        && is_numeric($_SESSION[$cfg['admin']['session_name']]);
}

function require_admin(): void
{
    if (!is_admin_logged_in()) {
        header('Location: login.php');
        exit;
    }
}
