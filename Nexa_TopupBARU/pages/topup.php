<?php
// pages/topup.php — Katalog Produk & Form Order
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';

$catSlug   = $_GET['cat'] ?? 'diamond';
$preselect = (int)($_GET['pid'] ?? 0);

// Ambil semua kategori
$categories = $pdo->query('SELECT * FROM categories ORDER BY sort_order')->fetchAll();

// Validasi slug
$validSlug = false;
foreach ($categories as $c) {
    if ($c['slug'] === $catSlug) { $validSlug = true; break; }
}
if (!$validSlug) { $catSlug = 'diamond'; }

// Produk aktif kategori terpilih — hanya dari tabel products
$catObj = null;
foreach ($categories as $c) {
    if ($c['slug'] === $catSlug) { $catObj = $c; break; }
}

$stmt = $pdo->prepare('
    SELECT * FROM products
    WHERE is_active = 1 AND category_id = ?
    ORDER BY price ASC
');
$stmt->execute([$catObj['id'] ?? 0]);
$products = $stmt->fetchAll();

// Metode pembayaran
$payments = $pdo->query('SELECT * FROM payment_methods WHERE is_active = 1 ORDER BY id')->fetchAll();

$flash     = getFlash();
$pageTitle = 'Top Up Mobile Legends — Nexa_Topup';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<main class="topup-main container">

  <?php if ($flash): ?>
    <div class="flash flash-<?= $flash['type'] ?>"><?= sanitize($flash['msg']) ?></div>
  <?php endif; ?>

  <!-- Category Tabs -->
  <div class="cat-tabs" role="tablist">
    <?php foreach ($categories as $cat): ?>
    <a href="?cat=<?= $cat['slug'] ?>"
       class="cat-tab <?= $cat['slug'] === $catSlug ? 'active' : '' ?>"
       role="tab" aria-selected="<?= $cat['slug'] === $catSlug ? 'true' : 'false' ?>">
      <?= $cat['icon'] ?> <?= sanitize($cat['name']) ?>
    </a>
    <?php endforeach; ?>
  </div>

  <div class="topup-layout">

    <!-- ── LEFT: ID Form + Produk ── -->
    <div class="topup-left">

      <div class="id-form-card">
        <h3>🆔 Masukkan ID Akun Mobile Legends</h3>
        <div class="id-form-row">
          <div class="form-group" style="margin-bottom:0">
            <label for="mlUserId">User ID <span class="req">*</span></label>
            <input type="text" id="mlUserId" placeholder="Contoh: 123456789"
                   pattern="[0-9]+" inputmode="numeric" autocomplete="off">
            <small>Buka profil ML → salin User ID</small>
          </div>
          <div class="form-group" style="margin-bottom:0">
            <label for="mlServerId">Zone / Server ID <span class="req">*</span></label>
            <input type="text" id="mlServerId" placeholder="Contoh: 7601"
                   pattern="[0-9]+" inputmode="numeric" autocomplete="off">
            <small>Angka dalam kurung di bawah User ID</small>
          </div>
        </div>
        <div class="verify-wrap" style="margin-top:14px; margin-bottom:0">
          <button type="button" class="btn-verify" onclick="verifyUser()">🔍 Cek Nickname</button>
          <div id="verifyMsg" class="verify-msg"></div>
        </div>
      </div>

      <!-- Product Grid -->
      <div class="section-head">
        <h2 class="section-title">
          <?php foreach ($categories as $c) {
            if ($c['slug'] === $catSlug) { echo $c['icon'] . ' ' . sanitize($c['name']); break; }
          } ?>
        </h2>
        <span class="prod-count"><?= count($products) ?> produk</span>
      </div>

      <?php if (empty($products)): ?>
        <div class="empty-state">
          <div class="empty-icon">📦</div>
          <p>Belum ada produk di kategori ini.</p>
        </div>
      <?php else: ?>
      <div class="product-grid" id="productGrid">
        <?php foreach ($products as $p): ?>
        <?php $catIcon = addslashes($catObj['icon'] ?? '💎'); ?>
        <div class="product-card" id="pc-<?= $p['id'] ?>"
             onclick="selectProduct(<?= $p['id'] ?>, <?= htmlspecialchars(json_encode($p['name'])) ?>, <?= $p['price'] ?>, <?= $p['diamond_amount'] ?>, '<?= $catIcon ?>')"
             role="button" tabindex="0"
             onkeydown="if(event.key==='Enter'||event.key===' ')selectProduct(<?= $p['id'] ?>,<?= htmlspecialchars(json_encode($p['name'])) ?>,<?= $p['price'] ?>,<?= $p['diamond_amount'] ?>,'<?= $catIcon ?>')">

          <?php if ($p['badge']): ?>
            <div class="p-badge badge-<?= strtolower(str_replace(' ', '-', $p['badge'])) ?>">
              <?= sanitize($p['badge']) ?>
            </div>
          <?php endif; ?>

          <div class="p-icon"><?= $catObj['icon'] ?? '💎' ?></div>
          <div class="p-body">
            <div class="p-name"><?= sanitize($p['name']) ?></div>
            <?php if ($p['diamond_amount'] > 0): ?>
              <div class="p-diamonds">💎 <?= number_format($p['diamond_amount']) ?></div>
            <?php endif; ?>
            <div class="p-price">
              <?php if ($p['original_price']): ?>
                <span class="price-old"><?= formatRupiah($p['original_price']) ?></span>
              <?php endif; ?>
              <span class="price-now"><?= formatRupiah($p['price']) ?></span>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

    </div><!-- /topup-left -->

    <!-- ── RIGHT: Order Panel ── -->
    <div class="topup-right">
      <div class="order-panel" id="orderPanel">
        <h3>⚡ Detail Pesanan</h3>

        <div id="noSelection" class="no-selection">← Pilih produk di sebelah kiri</div>
        <div id="selectedDisplay" class="selected-display" style="display:none">
          <div class="sd-icon" id="sdIcon">💎</div>
          <div class="sd-info">
            <div class="sd-name"  id="sdName">—</div>
            <div class="sd-price" id="sdPrice">—</div>
          </div>
        </div>

        <form method="POST" action="<?= BASE_PATH ?>/pages/checkout.php" id="orderForm">
          <input type="hidden" name="product_id"     id="productIdInput" value="">
          <input type="hidden" name="ml_user_id"     id="hiddenUserId"   value="">
          <input type="hidden" name="ml_server_id"   id="hiddenServerId" value="">
          <input type="hidden" name="payment_method" id="hiddenPayment"  value="">

          <div class="form-group" id="paymentWrap" style="display:none">
            <label for="paymentMethod">Metode Pembayaran <span class="req">*</span></label>
            <select id="paymentMethod" onchange="updateTotal(); document.getElementById('hiddenPayment').value=this.value">
              <option value="">— Pilih metode pembayaran —</option>
              <?php foreach ($payments as $pm): ?>
              <option value="<?= $pm['code'] ?>"
                      data-fee-pct="<?= $pm['fee_percent'] ?>"
                      data-fee-fix="<?= $pm['fee_fixed'] ?>">
                <?= $pm['icon'] ?> <?= sanitize($pm['name']) ?>
                <?php if ($pm['fee_percent'] > 0): ?>(+<?= $pm['fee_percent'] ?>%)<?php endif; ?>
                <?php if ($pm['fee_fixed']   > 0): ?>(+<?= formatRupiah($pm['fee_fixed']) ?>)<?php endif; ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="total-row" id="totalRow" style="display:none">
            <span>Total Bayar</span>
            <span class="total-amount" id="totalAmount">—</span>
          </div>

          <?php if (!isLoggedIn()): ?>
          <div class="guest-note">
            💡 <a href="<?= BASE_PATH ?>/auth/login.php">Login</a> untuk menyimpan riwayat transaksi kamu.
          </div>
          <?php endif; ?>

          <button type="submit" class="btn-primary btn-block" id="submitBtn" disabled
                  onclick="syncHiddenFields()">
            Lanjut ke Checkout →
          </button>
        </form>

      </div>
    </div><!-- /topup-right -->

  </div><!-- /topup-layout -->

</main>

<?php if ($preselect): ?>
<div id="preselectData" data-pid="<?= $preselect ?>" style="display:none"></div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
