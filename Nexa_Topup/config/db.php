<?php
// ============================================================
// config/db.php — Koneksi Database Nexa_Topup
// ============================================================

// Base URL dinamis — otomatis menyesuaikan nama folder/subfolder
$_protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$_host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
$_script   = $_SERVER['SCRIPT_NAME'] ?? '';
// Cari root folder project (Nexa_Topup) dari path script
$_parts    = explode('/', trim($_script, '/'));
$_base     = '';
if (!empty($_parts[0])) {
    $_base = '/' . $_parts[0];
}
define('BASE_URL', $_protocol . '://' . $_host . $_base);
define('BASE_PATH', $_base); // hanya path, tanpa domain

define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // ganti sesuai setup kamu
define('DB_PASS', '');           // ganti sesuai setup kamu
define('DB_NAME', 'nexa_topup');

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    die('<div style="font-family:sans-serif;padding:40px;color:#f87171;background:#0d0d0d;">
        <h2>⚡ Nexa_Topup</h2>
        <p>Koneksi database gagal. Pastikan MySQL aktif dan konfigurasi <code>config/db.php</code> benar.</p>
        <code>' . htmlspecialchars($e->getMessage()) . '</code>
    </div>');
}