<?php
// ============================================================
// config/session.php — Session & Helper Functions Nexa_Topup
// ============================================================

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 86400,
        'path'     => '/',
        'secure'   => false,   // true jika HTTPS
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// ── Auth Helpers ──────────────────────────────────────────

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

function isAdmin(): bool
{
    return isLoggedIn() && ($_SESSION['role'] ?? '') === 'admin';
}

function requireLogin(string $redirect = ''): void
{
    if (!isLoggedIn()) {
        $back = $redirect ?: $_SERVER['REQUEST_URI'];
        header('Location: ' . BASE_PATH . '/auth/login.php?redirect=' . urlencode($back));
        exit;
    }
}

function requireAdmin(): void
{
    requireLogin();
    if (!isAdmin()) {
        header('Location: ' . BASE_PATH . '/pages/index.php');
        exit;
    }
}

// ── Flash Messages ────────────────────────────────────────

function setFlash(string $type, string $msg): void
{
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

function getFlash(): ?array
{
    if (!isset($_SESSION['flash'])) return null;
    $f = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $f;
}

// ── Utilities ─────────────────────────────────────────────

function generateOrderCode(): string
{
    return 'NXT' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 9));
}

function formatRupiah(float $amount): string
{
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

function sanitize(string $val): string
{
    return htmlspecialchars(trim($val), ENT_QUOTES, 'UTF-8');
}