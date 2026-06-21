<?php

// pages/index.php — Halaman Utama Nexa_Topup
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';

// Ambil kategori & buat map by id
$categories = $pdo->query('SELECT * FROM categories ORDER BY sort_order')->fetchAll();
$catMap     = array_column($categories, null, 'id');

// Produk featured — hanya dari tabel products
$featured = $pdo->query("
    SELECT * FROM products
    WHERE is_active = 1 AND badge IN ('HOT','POPULER','BEST VALUE')
    ORDER BY sort_order ASC
    LIMIT 8
")->fetchAll();

$flash     = getFlash();
$pageTitle = 'Nexa_Topup — Top Up Mobile Legends Termurah';

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>


<!-- Flash -->
<?php if ($flash): ?>
<div class="flash flash-<?= $flash['type'] ?>">
  <?= sanitize($flash['msg']) ?>
</div>
<?php endif; ?>

<!-- ===== HERO ===== -->
<section class="hero">
  <div class="hero-inner">
    <div class="hero-badge">
      <span class="badge-dot"></span>
      PROSES OTOMATIS 24/7 &nbsp;·&nbsp; HARGA TERMURAH
    </div>

    <h1 class="hero-title">
      TOP UP<br>
      <span class="gradient-cyan">MOBILE</span><br>
      <span class="gradient-gold">LEGENDS</span>
    </h1>

    <p class="hero-sub">
      Diamond, Membership, Bundle, Weekly Pass — semua ada di Nexa_Topup.
      Transaksi instan, aman, dan terpercaya.
    </p>

    <div class="hero-stats">
      <div class="hero-stat">
        <span class="hs-num">500K+</span>
        <span class="hs-label">Transaksi / Hari</span>
      </div>
      <div class="hero-stat-divider"></div>
      <div class="hero-stat">
        <span class="hs-num">99.9%</span>
        <span class="hs-label">Sukses Rate</span>
      </div>
      <div class="hero-stat-divider"></div>
      <div class="hero-stat">
        <span class="hs-num">24/7</span>
        <span class="hs-label">Layanan Aktif</span>
      </div>
    </div>

    <div class="hero-cta">
      <a href="<?= BASE_PATH ?>/pages/topup.php?cat=diamond"    class="btn-primary">💎 Top Up Diamond</a>
      <a href="<?= BASE_PATH ?>/pages/topup.php?cat=membership" class="btn-outline">👑 Lihat Membership</a>
    </div>
  </div>

  <!-- Deco -->
  <div class="hero-deco" aria-hidden="true">
    <div class="deco-hex">💎</div>
    <div class="deco-hex">👑</div>
    <div class="deco-hex">🎁</div>
    <div class="deco-hex">⚡</div>
    <div class="deco-hex">🏆</div>
    <div class="deco-hex">🎮</div>
  </div>
</section>

<!-- ===== KATEGORI ===== -->
<section class="section container">
  <div class="section-head">
    <h2 class="section-title">📦 Kategori Produk</h2>
  </div>
  <div class="cat-grid">
    <?php foreach ($categories as $cat): ?>
    <a href="<?= BASE_PATH ?>/pages/topup.php?cat=<?= $cat['slug'] ?>" class="cat-card">
      <div class="cat-icon"><?= $cat['icon'] ?></div>
      <div class="cat-name"><?= sanitize($cat['name']) ?></div>
    </a>
    <?php endforeach; ?>
  </div>
</section>

<!-- ===== PROMO BANNER ===== -->
<div class="container">
  <div class="promo-banner">
    <div class="promo-left">
      <div class="promo-tag">⚡ FLASH SALE</div>
      <h3>Diskon hingga <span class="promo-pct">20% OFF</span></h3>
      <p>Spesial untuk paket Diamond & Membership hari ini saja!</p>
    </div>
    <div class="promo-right">
      <div class="countdown" id="countdown">
        <div class="cd-block"><span class="cd-num" id="cd-h">00</span><span class="cd-label">Jam</span></div>
        <span class="cd-sep">:</span>
        <div class="cd-block"><span class="cd-num" id="cd-m">00</span><span class="cd-label">Menit</span></div>
        <span class="cd-sep">:</span>
        <div class="cd-block"><span class="cd-num" id="cd-s">00</span><span class="cd-label">Detik</span></div>
      </div>
      <a href="<?= BASE_PATH ?>/pages/topup.php" class="btn-primary">Ambil Promo →</a>
    </div>
  </div>
</div>

<!-- ===== PRODUK FEATURED ===== -->
<section class="section container">
  <div class="section-head">
    <h2 class="section-title">🔥 Produk Terpopuler</h2>
    <a href="<?= BASE_PATH ?>/pages/topup.php" class="see-all">Lihat Semua →</a>
  </div>
  <div class="product-grid">
    <?php foreach ($featured as $p): ?>
    <a href="<?= BASE_PATH ?>/pages/topup.php?cat=<?= $catMap[$p['category_id']]['slug'] ?? 'diamond' ?>&pid=<?= $p['id'] ?>" class="product-card">
      <?php $pCat = $catMap[$p['category_id']] ?? []; ?>
      <?php if ($p['badge']): ?>
        <div class="p-badge badge-<?= strtolower(str_replace(' ', '-', $p['badge'])) ?>">
          <?= sanitize($p['badge']) ?>
        </div>
      <?php endif; ?>
      <div class="p-icon"><?= $pCat['icon'] ?? '💎' ?></div>
      <div class="p-body">
        <div class="p-name"><?= sanitize($p['name']) ?></div>
        <div class="p-cat"><?= sanitize($pCat['name'] ?? '') ?></div>
        <?php if ($p['diamond_amount'] > 0): ?>
          <div class="p-diamonds">💎 <?= number_format($p['diamond_amount']) ?> Diamond</div>
        <?php endif; ?>
        <div class="p-price">
          <?php if ($p['original_price']): ?>
            <span class="price-old"><?= formatRupiah($p['original_price']) ?></span>
          <?php endif; ?>
          <span class="price-now"><?= formatRupiah($p['price']) ?></span>
        </div>
      </div>
    </a>
    <?php endforeach; ?>
  </div>
</section>

<!-- ===== CARA TOP UP ===== -->
<section class="section container how-section">
  <div class="section-head">
    <h2 class="section-title">📖 Cara Top Up di Nexa_Topup</h2>
  </div>
  <div class="steps-grid">
    <div class="step-card">
      <div class="step-num">01</div>
      <div class="step-icon">🎮</div>
      <h4>Pilih Produk</h4>
      <p>Pilih Diamond, Membership, Bundle, atau paket lain sesuai kebutuhanmu</p>
    </div>
    <div class="step-card">
      <div class="step-num">02</div>
      <div class="step-icon">🆔</div>
      <h4>Masukkan ID Game</h4>
      <p>Isi User ID dan Zone ID akun Mobile Legends kamu dengan benar</p>
    </div>
    <div class="step-card">
      <div class="step-num">03</div>
      <div class="step-icon">💳</div>
      <h4>Pilih Pembayaran</h4>
      <p>Bayar via GoPay, OVO, DANA, QRIS, Transfer Bank, dan banyak lagi</p>
    </div>
    <div class="step-card">
      <div class="step-num">04</div>
      <div class="step-icon">⚡</div>
      <h4>Terima Instan</h4>
      <p>Diamond / item langsung masuk ke akun ML dalam hitungan detik</p>
    </div>
  </div>
</section>



<?php require_once __DIR__ . '/../includes/footer.php'; ?>