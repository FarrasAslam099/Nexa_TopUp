<?php
// ============================================
// pages/topup.php — Katalog & Pilih Produk
// ============================================
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';

// Filter kategori
$catSlug    = $_GET['cat'] ?? 'diamond';
$productId  = (int)($_GET['product'] ?? 0);

// Ambil semua kategori
$categories = $pdo->query("SELECT * FROM categories ORDER BY sort_order")->fetchAll();

// Cari kategori aktif
$activeCat = null;
foreach ($categories as $c) {
    if ($c['slug'] === $catSlug) { $activeCat = $c; break; }
}
if (!$activeCat) { $activeCat = $categories[0]; $catSlug = $activeCat['slug']; }

// Ambil produk sesuai kategori
$stmt = $pdo->prepare("
    SELECT p.*, c.name AS cat_name, c.slug AS cat_slug
    FROM products p
    JOIN categories c ON p.category_id = c.id
    WHERE p.is_active = 1 AND c.slug = ?
    ORDER BY p.price ASC
");
$stmt->execute([$catSlug]);
$products = $stmt->fetchAll();

// Produk terpilih (untuk modal checkout)
$selectedProduct = null;
if ($productId) {
    $stmt2 = $pdo->prepare("SELECT p.*, c.slug AS cat_slug FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = ? AND p.is_active = 1");
    $stmt2->execute([$productId]);
    $selectedProduct = $stmt2->fetch();
}

$pageTitle = 'Top Up ' . htmlspecialchars($activeCat['name']) . ' – ML Shop';
require_once __DIR__ . '/../includes/header.php';
?>
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="container topup-page">

  <!-- Category Tabs -->
  <div class="cat-tabs">
    <?php foreach ($categories as $cat): ?>
    <a href="?cat=<?= $cat['slug'] ?>"
       class="cat-tab <?= $cat['slug'] === $catSlug ? 'active' : '' ?>">
      <?= $cat['icon'] ?> <?= htmlspecialchars($cat['name']) ?>
    </a>
    <?php endforeach; ?>
  </div>

  <div class="topup-layout">

    <!-- Left: Product Grid -->
    <div class="topup-left">
      <div class="section-title">
        <?= $activeCat['icon'] ?> <?= htmlspecialchars($activeCat['name']) ?>
        <span class="product-count">(<?= count($products) ?> produk)</span>
      </div>
      <div class="product-grid">
        <?php foreach ($products as $p): ?>
        <div class="product-card <?= $selectedProduct && $selectedProduct['id'] == $p['id'] ? 'selected' : '' ?>"
             onclick="selectProduct(<?= $p['id'] ?>, '<?= addslashes($p['name']) ?>', <?= $p['price'] ?>, '<?= addslashes($p['description']) ?>', <?= $p['diamond_amount'] ?>)">

          <?php if ($p['badge']): ?>
            <div class="product-badge badge-<?= strtolower(str_replace(' ', '-', $p['badge'])) ?>"><?= $p['badge'] ?></div>
          <?php endif; ?>

          <div class="product-icon">
            <?= $catSlug === 'diamond' ? '💎' : ($catSlug === 'membership' ? '👑' : ($catSlug === 'bundle' ? '🎁' : ($catSlug === 'weekly' ? '📅' : '🎨'))) ?>
          </div>
          <div class="product-info">
            <div class="product-name"><?= htmlspecialchars($p['name']) ?></div>
            <?php if ($p['diamond_amount'] > 0): ?>
              <div class="product-diamonds">💎 <?= number_format($p['diamond_amount']) ?> Diamond</div>
            <?php endif; ?>
            <div class="product-price">
              <?php if ($p['original_price']): ?>
                <span class="price-old">Rp <?= number_format($p['original_price'], 0, ',', '.') ?></span>
              <?php endif; ?>
              <span class="price-now">Rp <?= number_format($p['price'], 0, ',', '.') ?></span>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Right: Order Form -->
    <div class="topup-right" id="orderPanel">
      <div class="order-card">
        <h3>📋 Form Top Up</h3>

        <div id="selectedProductInfo" class="selected-product-display" style="display:none">
          <div class="sp-icon" id="spIcon">💎</div>
          <div class="sp-detail">
            <div class="sp-name" id="spName">—</div>
            <div class="sp-price" id="spPrice">—</div>
          </div>
        </div>
        <div id="noProductMsg" class="no-product-msg">
          ← Pilih produk terlebih dahulu
        </div>

        <form method="POST" action="/pages/checkout.php" id="orderForm">
          <input type="hidden" name="product_id" id="productIdInput" value="">

          <div class="form-group">
            <label>User ID Mobile Legends</label>
            <input type="text" name="ml_user_id" id="mlUserId" placeholder="Contoh: 123456789"
                   value="<?= htmlspecialchars($_POST['ml_user_id'] ?? '') ?>" required pattern="[0-9]+" title="Angka saja">
            <small>Lihat di profil akun ML kamu</small>
          </div>

          <div class="form-group">
            <label>Zone / Server ID</label>
            <input type="text" name="ml_server_id" id="mlServerId" placeholder="Contoh: 7601"
                   value="<?= htmlspecialchars($_POST['ml_server_id'] ?? '') ?>" required pattern="[0-9]+" title="Angka saja">
            <small>Angka dalam kurung di bawah User ID</small>
          </div>

          <div id="verifySection" style="display:none">
            <button type="button" class="btn-verify" onclick="verifyUser()">🔍 Cek Nickname</button>
            <div id="verifyResult" class="verify-result"></div>
          </div>

          <div class="form-group">
            <label>Metode Pembayaran</label>
            <select name="payment_method" required>
              <option value="">— Pilih metode —</option>
              <?php
              $payments = $pdo->query("SELECT * FROM payment_methods WHERE is_active = 1 ORDER BY id")->fetchAll();
              foreach ($payments as $pm):
              ?>
              <option value="<?= $pm['code'] ?>"><?= $pm['icon'] ?> <?= htmlspecialchars($pm['name']) ?>
                <?= $pm['fee_percent'] > 0 ? "(+{$pm['fee_percent']}%)" : ($pm['fee_fixed'] > 0 ? "(+Rp ".number_format($pm['fee_fixed'],0,',','.').")" : '') ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="order-total" id="orderTotal" style="display:none">
            <span>Total Bayar</span>
            <span class="total-price" id="totalDisplay">—</span>
          </div>

          <?php if (!isLoggedIn()): ?>
          <div class="guest-notice">
            💡 <a href="/auth/login.php">Login</a> untuk menyimpan riwayat transaksi
          </div>
          <?php endif; ?>

          <button type="submit" class="btn-primary btn-block btn-order" id="submitBtn" disabled>
            ⚡ Lanjut Checkout
          </button>
        </form>
      </div>
    </div>
  </div>
</main>

<script>
let selectedPrice = 0;

function selectProduct(id, name, price, desc, diamonds) {
    selectedPrice = price;
    document.getElementById('productIdInput').value = id;
    document.getElementById('spName').textContent = name;
    document.getElementById('spPrice').textContent = 'Rp ' + price.toLocaleString('id-ID');
    document.getElementById('selectedProductInfo').style.display = 'flex';
    document.getElementById('noProductMsg').style.display = 'none';
    document.getElementById('verifySection').style.display = 'block';
    document.getElementById('orderTotal').style.display = 'flex';
    document.getElementById('totalDisplay').textContent = 'Rp ' + price.toLocaleString('id-ID');
    document.getElementById('submitBtn').disabled = false;

    // Highlight selected card
    document.querySelectorAll('.product-card').forEach(c => c.classList.remove('selected'));
    event.currentTarget.classList.add('selected');

    // Scroll to form on mobile
    if (window.innerWidth < 900) {
        document.getElementById('orderPanel').scrollIntoView({ behavior: 'smooth' });
    }
}

function verifyUser() {
    const uid = document.getElementById('mlUserId').value;
    const sid = document.getElementById('mlServerId').value;
    if (!uid || !sid) { alert('Masukkan User ID dan Zone ID terlebih dahulu.'); return; }

    const res = document.getElementById('verifyResult');
    res.innerHTML = '<span class="verify-loading">⏳ Memverifikasi...</span>';
    // Simulasi verifikasi (connect ke API ML di implementasi nyata)
    setTimeout(() => {
        res.innerHTML = '<span class="verify-ok">✅ Akun ditemukan — silakan lanjutkan</span>';
    }, 1200);
}

// Auto-select jika ada product dari URL
<?php if ($selectedProduct): ?>
selectProduct(
    <?= $selectedProduct['id'] ?>,
    '<?= addslashes($selectedProduct['name']) ?>',
    <?= $selectedProduct['price'] ?>,
    '<?= addslashes($selectedProduct['description']) ?>',
    <?= $selectedProduct['diamond_amount'] ?>
);
<?php endif; ?>
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
