<?php
// ============================================
// pages/index.php — Halaman Utama
// ============================================
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';

// Ambil produk featured (HOT / POPULER) per kategori
$featured = $pdo->query("
    SELECT p.*, c.name AS cat_name, c.slug AS cat_slug
    FROM products p
    JOIN categories c ON p.category_id = c.id
    WHERE p.is_active = 1 AND p.badge IN ('HOT','POPULER','BEST VALUE')
    ORDER BY p.sort_order ASC
    LIMIT 8
")->fetchAll();

// Ambil semua kategori
$categories = $pdo->query("SELECT * FROM categories ORDER BY sort_order")->fetchAll();

// Flash message
$flash = getFlash();

$pageTitle = 'ML Shop – Top Up Mobile Legends Termurah';
require_once __DIR__ . '/../includes/header.php';
?>
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<?php if ($flash): ?>
<div class="flash-banner flash-<?= $flash['type'] ?>">
  <?= htmlspecialchars($flash['msg']) ?>
</div>
<?php endif; ?>

<!-- ===== HERO ===== -->
<section class="hero">
  <div class="hero-badge">
    <div class="badge-dot"></div>
    PROSES OTOMATIS 24/7 · HARGA TERMURAH
  </div>
  <h1>TOP UP<br><span class="line-cyan">MOBILE</span><br><span class="line-gold">LEGENDS</span></h1>
  <p class="hero-sub">Diamond, Membership, Bundle, Weekly Pass — semua ada. Harga terjangkau, transaksi instan.</p>
  <div class="hero-stats">
    <div class="stat"><span class="stat-num">500K+</span><span class="stat-label">Transaksi/Hari</span></div>
    <div class="stat"><span class="stat-num">99.9%</span><span class="stat-label">Sukses Rate</span></div>
    <div class="stat"><span class="stat-num">24/7</span><span class="stat-label">Layanan Aktif</span></div>
  </div>
  <div class="hero-cta">
    <a href="/pages/topup.php" class="btn-primary">💎 Top Up Sekarang</a>
    <a href="/pages/topup.php?cat=membership" class="btn-outline">👑 Lihat Membership</a>
  </div>
</section>

<!-- ===== KATEGORI ===== -->
<section class="container">
  <div class="section-header">
    <div class="section-title">📦 Kategori Produk</div>
  </div>
  <div class="cat-grid">
    <?php foreach ($categories as $cat): ?>
    <a href="/pages/topup.php?cat=<?= $cat['slug'] ?>" class="cat-card">
      <div class="cat-icon"><?= $cat['icon'] ?></div>
      <div class="cat-name"><?= htmlspecialchars($cat['name']) ?></div>
    </a>
    <?php endforeach; ?>
  </div>
</section>

<!-- ===== PROMO BANNER ===== -->
<div class="promo-banner">
  <div class="promo-text">
    <h2>FLASH SALE <span>20% OFF</span> 🎉</h2>
    <p>Diskon spesial untuk paket Diamond & Membership hari ini!</p>
  </div>
  <div class="promo-actions">
    <div class="promo-timer" id="promoTimer"></div>
    <a href="/pages/topup.php" class="btn-primary">Ambil Promo</a>
  </div>
</div>

<!-- ===== PRODUK FEATURED ===== -->
<section class="container">
  <div class="section-header">
    <div class="section-title">🔥 Produk Terpopuler</div>
    <a href="/pages/topup.php" class="see-all">Lihat Semua →</a>
  </div>
  <div class="product-grid">
    <?php foreach ($featured as $p): ?>
    <a href="/pages/topup.php?cat=<?= $p['cat_slug'] ?>&product=<?= $p['id'] ?>" class="product-card">
      <?php if ($p['badge']): ?>
        <div class="product-badge badge-<?= strtolower($p['badge']) ?>"><?= $p['badge'] ?></div>
      <?php endif; ?>
      <div class="product-icon">
        <?= $p['cat_slug'] === 'diamond' ? '💎' : ($p['cat_slug'] === 'membership' ? '👑' : ($p['cat_slug'] === 'bundle' ? '🎁' : ($p['cat_slug'] === 'weekly' ? '📅' : '🎨'))) ?>
      </div>
      <div class="product-info">
        <div class="product-name"><?= htmlspecialchars($p['name']) ?></div>
        <div class="product-cat"><?= htmlspecialchars($p['cat_name']) ?></div>
        <div class="product-price">
          <?php if ($p['original_price']): ?>
            <span class="price-old">Rp <?= number_format($p['original_price'], 0, ',', '.') ?></span>
          <?php endif; ?>
          <span class="price-now">Rp <?= number_format($p['price'], 0, ',', '.') ?></span>
        </div>
      </div>
    </a>
    <?php endforeach; ?>
  </div>
</section>

<!-- ===== CARA TOP UP ===== -->
<section class="container how-section">
  <div class="section-title">📖 Cara Top Up</div>
  <div class="steps-grid">
    <div class="step-card">
      <div class="step-num">1</div>
      <div class="step-icon">🎮</div>
      <h4>Pilih Produk</h4>
      <p>Pilih Diamond, Membership, atau paket lainnya sesuai kebutuhan</p>
    </div>
    <div class="step-card">
      <div class="step-num">2</div>
      <div class="step-icon">🆔</div>
      <h4>Masukkan ID Game</h4>
      <p>Masukkan User ID dan Zone ID akun Mobile Legends kamu</p>
    </div>
    <div class="step-card">
      <div class="step-num">3</div>
      <div class="step-icon">💳</div>
      <h4>Pilih Pembayaran</h4>
      <p>Bayar via GoPay, OVO, DANA, QRIS, transfer bank, dan lainnya</p>
    </div>
    <div class="step-card">
      <div class="step-num">4</div>
      <div class="step-icon">⚡</div>
      <h4>Terima Instan</h4>
      <p>Diamond langsung masuk ke akun kamu dalam hitungan detik</p>
    </div>
  </div>
</section>

<!-- ===== PAYMENT METHODS ===== -->
<div class="payment-section">
  <p class="payment-title">Metode Pembayaran Tersedia</p>
  <div class="payment-logos">
    <div class="pay-item">🟢 GoPay</div>
    <div class="pay-item">🔵 OVO</div>
    <div class="pay-item">🟣 DANA</div>
    <div class="pay-item">🟠 ShopeePay</div>
    <div class="pay-item">🌐 QRIS</div>
    <div class="pay-item">🏦 Transfer Bank</div>
    <div class="pay-item">💳 Kartu Kredit</div>
    <div class="pay-item">📱 Pulsa</div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
