<?php
// ============================================
// auth/logout.php — Logout
// ============================================
require_once __DIR__ . '/../config/session.php';

session_destroy();
header('Location: /pages/index.php');
exit;
