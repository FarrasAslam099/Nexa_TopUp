<?php
// ============================================================
// pages/index.php — Halaman Utama Nexa_Topup
// ============================================================
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';

// Ambil kategori
$categories = $pdo->query(
    'SELECT * FROM categories ORDER BY sort_order'
)->fetchAll();

// Produk featured (HOT / POPULER / BEST VALUE)
$featured = $pdo->query("
    SELECT p.*, c.name AS cat_name, c.slug AS cat_slug, c.icon AS cat_icon
    FROM products p
    JOIN categories c ON p.category_id = c.id
    WHERE p.is_active = 1 AND p.badge IN ('HOT','POPULER','BEST VALUE')
    ORDER BY p.sort_order ASC
    LIMIT 8
")->fetchAll();

$flash     = getFlash();
$pageTitle = 'Nexa_Topup — Top Up Mobile Legends Termurah';

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<style>
/* ============================================================
   STYLE KHUSUS HALAMAN: pages/index.php
   Hero, Kategori, Promo Banner, Cara Top Up, Pay Bar
   ============================================================ */

/* ── HERO ── */
.hero {
  background: linear-gradient(135deg, #0d1520 0%, #111827 50%, #0d1a2e 100%);
  border-bottom: 1px solid var(--border);
  padding: 60px 20px 50px;
  text-align: center;
  position: relative; overflow: hidden;
}
.hero::before {
  content: '';
  position: absolute; inset: 0;
  background: radial-gradient(ellipse at 50% 0%, rgba(0,212,255,.08) 0%, transparent 65%);
  pointer-events: none;
}
.hero-inner { position: relative; z-index: 1; max-width: 700px; margin: 0 auto; }

.hero-badge {
  display: inline-flex; align-items: center; gap: 8px;
  background: rgba(0,212,255,.1); border: 1px solid rgba(0,212,255,.25);
  border-radius: 50px; padding: 6px 16px;
  font-size: 12px; font-weight: 700; letter-spacing: 1px;
  color: var(--cyan); margin-bottom: 24px;
}
.badge-dot {
  width: 7px; height: 7px; border-radius: 50%;
  background: var(--cyan);
  animation: blink 1.5s ease-in-out infinite;
}
@keyframes blink { 0%,100% { opacity: 1; } 50% { opacity: .3; } }

.hero-title {
  font-family: 'Orbitron', sans-serif;
  font-size: clamp(36px, 7vw, 64px);
  font-weight: 900; line-height: 1.1;
  margin-bottom: 16px;
}
.gradient-cyan { background: linear-gradient(90deg, var(--cyan), #00a8ff); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
.gradient-gold { background: linear-gradient(90deg, var(--gold), #ff9900); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }

.hero-sub { color: var(--text2); font-size: 16px; margin-bottom: 28px; max-width: 500px; margin-left: auto; margin-right: auto; }

.hero-stats {
  display: flex; align-items: center; justify-content: center; gap: 0;
  margin-bottom: 32px;
}
.hero-stat { text-align: center; padding: 0 24px; }
.hero-stat-divider { width: 1px; height: 36px; background: var(--border); }
.hs-num { display: block; font-family: 'Orbitron', sans-serif; font-size: 22px; font-weight: 900; color: var(--cyan); }
.hs-label { font-size: 11px; color: var(--text3); letter-spacing: .5px; }

.hero-cta { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }

.hero-deco {
  position: absolute; inset: 0; pointer-events: none;
  display: flex; align-items: center; justify-content: space-around;
  opacity: .04; font-size: 80px;
}

/* ── Category Grid ── */
.cat-grid {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 12px;
}
.cat-card {
  background: var(--bg2);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 18px 12px;
  text-align: center;
  text-decoration: none; color: var(--text);
  transition: all .2s;
}
.cat-card:hover {
  border-color: var(--cyan);
  background: rgba(0,212,255,.06);
  transform: translateY(-2px);
  text-decoration: none; color: var(--text);
}
.cat-icon { font-size: 28px; margin-bottom: 8px; }
.cat-name { font-size: 13px; font-weight: 600; color: var(--text2); }

/* ── Promo Banner ── */
.promo-banner {
  background: linear-gradient(135deg, #1a1035, #0e1a35);
  border: 1px solid rgba(245,166,35,.3);
  border-radius: var(--radius-lg);
  padding: 28px 32px;
  display: flex; align-items: center; justify-content: space-between;
  gap: 20px;
  margin: 0 0 8px;
}
.promo-tag { font-size: 11px; font-weight: 800; color: var(--gold); letter-spacing: 1px; margin-bottom: 6px; }
.promo-banner h3 { font-size: 20px; font-weight: 700; margin-bottom: 6px; }
.promo-pct { color: var(--gold); }
.promo-banner > .promo-left p { color: var(--text2); font-size: 14px; }
.promo-right { display: flex; align-items: center; gap: 20px; flex-shrink: 0; flex-wrap: wrap; justify-content: flex-end; }

.countdown { display: flex; align-items: center; gap: 6px; }
.cd-block { text-align: center; }
.cd-num {
  display: block;
  font-family: 'Orbitron', sans-serif;
  font-size: 24px; font-weight: 900; color: var(--gold);
  background: rgba(245,166,35,.1); border: 1px solid rgba(245,166,35,.3);
  border-radius: 6px; padding: 4px 12px;
  min-width: 52px;
}
.cd-label { font-size: 10px; color: var(--text3); letter-spacing: .5px; margin-top: 2px; display: block; }
.cd-sep { font-size: 20px; font-weight: 900; color: var(--gold); margin-bottom: 18px; }

/* ── How Steps ── */
.how-section { padding-bottom: 48px; }
.steps-grid {
  display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px;
}
.step-card {
  background: var(--bg2); border: 1px solid var(--border);
  border-radius: var(--radius); padding: 24px 16px;
  position: relative; text-align: center;
}
.step-num {
  position: absolute; top: 12px; left: 16px;
  font-family: 'Orbitron', sans-serif;
  font-size: 11px; font-weight: 900; color: var(--text3);
}
.step-icon { font-size: 32px; margin-bottom: 12px; }
.step-card h4 { font-size: 15px; font-weight: 700; margin-bottom: 6px; }
.step-card p { font-size: 13px; color: var(--text2); line-height: 1.5; }

/* ── Pay Bar ── */
.pay-bar { display: none; }

/* ── Responsive ── */
@media (max-width: 1024px) {
  .cat-grid { grid-template-columns: repeat(3, 1fr); }
  .steps-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 768px) {
  .hero { padding: 40px 16px 32px; }
  .hero-stats { gap: 0; }
  .hero-stat { padding: 0 14px; }
  .promo-banner { flex-direction: column; }
  .cat-grid { grid-template-columns: repeat(2, 1fr); }
  .steps-grid { grid-template-columns: 1fr 1fr; }
}
@media (max-width: 480px) {
  .hero-title { font-size: 36px; }
  .steps-grid { grid-template-columns: 1fr; }
  .cat-grid { grid-template-columns: repeat(2, 1fr); }
  .hero-stats { flex-direction: column; gap: 12px; }
  .hero-stat-divider { display: none; }
}
</style>

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
    <a href="<?= BASE_PATH ?>/pages/topup.php?cat=<?= $p['cat_slug'] ?>&pid=<?= $p['id'] ?>" class="product-card">
      <?php if ($p['badge']): ?>
        <div class="p-badge badge-<?= strtolower(str_replace(' ', '-', $p['badge'])) ?>">
          <?= sanitize($p['badge']) ?>
        </div>
      <?php endif; ?>
      <div class="p-icon"><?= $p['cat_icon'] ?></div>
      <div class="p-body">
        <div class="p-name"><?= sanitize($p['name']) ?></div>
        <div class="p-cat"><?= sanitize($p['cat_name']) ?></div>
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

<!-- ===== PAYMENT METHODS ===== -->
<div class="pay-bar">
  <div class="container">
    <p class="pay-bar-title">Metode Pembayaran yang Didukung</p>
    <div class="pay-bar-logos">
      <span class="pay-chip">🟢 GoPay</span>
      <span class="pay-chip">🔵 OVO</span>
      <span class="pay-chip">🟣 DANA</span>
      <span class="pay-chip">🟠 ShopeePay</span>
      <span class="pay-chip">🌐 QRIS</span>
      <span class="pay-chip">🏦 Transfer Bank</span>
      <span class="pay-chip">💳 Kartu Kredit</span>
      <span class="pay-chip">📱 Pulsa</span>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>