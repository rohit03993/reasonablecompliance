<?php
require __DIR__ . '/auth.php';
$_SESSION = [];
session_destroy();
header('Location: /manage/login.php');
exit;
