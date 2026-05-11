<?php
// ============================================
// includes/navbar.php — Navigation Bar
// ============================================
require_once __DIR__ . '/../config/session.php';
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<nav>
  <a href="/pages/index.php" class="logo">
    <div class="logo-icon">⚡</div>
    ML<span>SHOP</span>
  </a>

  <ul class="nav-links">
    <li><a href="/pages/index.php" class="<?= $currentPage === 'index.php' ? 'active' : '' ?>">Beranda</a></li>
    <li><a href="/pages/topup.php" class="<?= $currentPage === 'topup.php' ? 'active' : '' ?>">Top Up</a></li>
    <li><a href="/pages/topup.php?cat=membership" class="<?= ($_GET['cat'] ?? '') === 'membership' ? 'active' : '' ?>">Membership</a></li>
    <li><a href="/pages/topup.php?cat=bundle" class="<?= ($_GET['cat'] ?? '') === 'bundle' ? 'active' : '' ?>">Bundle</a></li>
    <?php if (isLoggedIn()): ?>
    <li><a href="/pages/history.php" class="<?= $currentPage === 'history.php' ? 'active' : '' ?>">Riwayat</a></li>
    <?php endif; ?>
  </ul>

  <div class="nav-right">
    <?php if (isLoggedIn()): ?>
      <div class="nav-user">
        <span class="nav-username">👤 <?= htmlspecialchars($_SESSION['username']) ?></span>
        <?php if (isAdmin()): ?>
          <a href="/admin/dashboard.php" class="btn-admin">⚙️ Admin</a>
        <?php endif; ?>
        <a href="/auth/logout.php" class="btn-logout">Keluar</a>
      </div>
    <?php else: ?>
      <a href="/auth/login.php" class="btn-login">Masuk</a>
      <a href="/auth/register.php" class="btn-register">Daftar</a>
    <?php endif; ?>
  </div>

  <button class="nav-toggle" onclick="document.querySelector('.nav-links').classList.toggle('open')">☰</button>
</nav>
