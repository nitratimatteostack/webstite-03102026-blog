<?php
session_start();

$cfg = require __DIR__ . '/../config.php';

unset($_SESSION[$cfg['admin']['session_name']]);
session_destroy();

header('Location: login.php');
exit;
