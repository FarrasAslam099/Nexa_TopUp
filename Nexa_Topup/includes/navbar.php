<?php
// ============================================================
// includes/navbar.php
// ============================================================
require_once __DIR__ . '/../config/session.php';

$currentFile = basename($_SERVER['PHP_SELF']);
$currentCat  = $_GET['cat'] ?? '';

function navLink(string $href, string $label, bool $active): string {
    $cls = $active ? ' class="active"' : '';
    return "<li><a href=\"{$href}\"{$cls}>{$label}</a></li>";
}
?>
<nav id="mainNav">
  <a href="<?= BASE_PATH ?>/pages/index.php" class="logo">
    <div class="logo-icon">⚡</div>
    NEXA<span>_TOPUP</span>
  </a>

  <button class="nav-toggle" id="navToggle" aria-label="Toggle menu">
    <span></span><span></span><span></span>
  </button>

  <ul class="nav-links" id="navLinks">
    <?= navLink(BASE_PATH . '/pages/index.php',                '🏠 Beranda',   $currentFile === 'index.php') ?>
    <?= navLink(BASE_PATH . '/pages/topup.php?cat=diamond',   '💎 Diamond',   $currentFile === 'topup.php' && $currentCat === 'diamond') ?>
    <?= navLink(BASE_PATH . '/pages/topup.php?cat=membership','👑 Membership', $currentFile === 'topup.php' && $currentCat === 'membership') ?>
    <?= navLink(BASE_PATH . '/pages/topup.php?cat=bundle',    '🎁 Bundle',    $currentFile === 'topup.php' && $currentCat === 'bundle') ?>
    <?= navLink(BASE_PATH . '/pages/topup.php?cat=weekly',    '📅 Weekly',    $currentFile === 'topup.php' && $currentCat === 'weekly') ?>
    <?php if (isLoggedIn()): ?>
    <?= navLink(BASE_PATH . '/pages/history.php',             '📋 Riwayat',   $currentFile === 'history.php') ?>
    <?php endif; ?>
  </ul>

  <div class="nav-right" id="navRight">
    <?php if (isLoggedIn()): ?>
      <div class="nav-user-wrap">
        <span class="nav-avatar">👤</span>
        <span class="nav-username"><?= sanitize($_SESSION['username']) ?></span>
        <?php if (isAdmin()): ?>
          <a href="<?= BASE_PATH ?>/admin/dashboard.php" class="btn-sm btn-admin">⚙️ Admin</a>
        <?php endif; ?>
        <a href="<?= BASE_PATH ?>/auth/logout.php" class="btn-sm btn-logout">Keluar</a>
      </div>
    <?php else: ?>
      <a href="<?= BASE_PATH ?>/auth/login.php"    class="btn-sm btn-ghost">Masuk</a>
      <a href="<?= BASE_PATH ?>/auth/register.php" class="btn-sm btn-gold">Daftar</a>
    <?php endif; ?>
  </div>
</nav>
