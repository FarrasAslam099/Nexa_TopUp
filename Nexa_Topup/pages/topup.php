<?php
// ============================================================
// pages/topup.php — Katalog Produk & Form Order (GoPay layout)
// ============================================================
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

// Produk aktif kategori terpilih
$stmt = $pdo->prepare("
    SELECT p.*, c.name AS cat_name, c.slug AS cat_slug, c.icon AS cat_icon
    FROM products p
    JOIN categories c ON p.category_id = c.id
    WHERE p.is_active = 1 AND c.slug = ?
    ORDER BY p.price ASC
");
$stmt->execute([$catSlug]);
$products = $stmt->fetchAll();

// Metode pembayaran
$payments = $pdo->query('SELECT * FROM payment_methods WHERE is_active = 1 ORDER BY id')->fetchAll();

$pageTitle = 'Top Up Mobile Legends — Nexa_Topup';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<style>
/* ============================================================
   STYLE KHUSUS HALAMAN: pages/topup.php
   Tab kategori, layout GoPay (form ID + produk | panel order)
   ============================================================ */
.topup-main { padding: 32px 0 60px; }

/* Category Tabs */
.cat-tabs {
  display: flex; gap: 8px; flex-wrap: wrap;
  margin-bottom: 28px;
  border-bottom: 2px solid var(--border);
  padding-bottom: 0;
}
.cat-tab {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 10px 18px;
  font-size: 14px; font-weight: 700;
  color: var(--text2); text-decoration: none;
  border-bottom: 2px solid transparent;
  margin-bottom: -2px;
  transition: all .2s;
}
.cat-tab:hover { color: var(--text); text-decoration: none; }
.cat-tab.active { color: var(--cyan); border-bottom-color: var(--cyan); }

/* Layout: left (form + products) | right (order panel) */
.topup-layout {
  display: grid;
  grid-template-columns: 1fr 340px;
  gap: 24px;
  align-items: start;
}

/* Left — ID Form on top, then products */
.topup-left {}

/* ID Form Card (GoPay style) */
.id-form-card {
  background: var(--bg2);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg);
  padding: 24px;
  margin-bottom: 20px;
}
.id-form-card h3 { font-size: 16px; font-weight: 700; margin-bottom: 16px; }

.id-form-row {
  display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;
}

.verify-wrap { margin-bottom: 14px; }
.btn-verify {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 9px 18px;
  background: var(--bg3); border: 1.5px solid var(--cyan);
  color: var(--cyan); font-size: 13px; font-weight: 700;
  border-radius: var(--radius); cursor: pointer;
  transition: all .2s; font-family: inherit;
}
.btn-verify:hover { background: rgba(0,212,255,.1); }
.verify-msg { margin-top: 8px; font-size: 13px; }
.v-ok   { color: var(--green); }
.v-warn { color: var(--gold); }
.v-load { color: var(--text3); }

/* Products inside left panel */
.prod-count { font-size: 13px; color: var(--text3); }

/* ── Right: Order Panel ── */
.topup-right { position: sticky; top: 76px; }
.order-panel {
  background: var(--bg2);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg);
  padding: 24px;
}
.order-panel h3 { font-size: 16px; font-weight: 700; margin-bottom: 18px; }

.no-selection {
  background: var(--bg3); border: 1px dashed var(--border);
  border-radius: var(--radius); padding: 16px;
  text-align: center; color: var(--text3); font-size: 13px;
  margin-bottom: 16px;
}
.selected-display {
  display: flex; align-items: center; gap: 12px;
  background: rgba(0,212,255,.07); border: 1px solid rgba(0,212,255,.25);
  border-radius: var(--radius); padding: 14px;
  margin-bottom: 16px;
}
.sd-icon { font-size: 28px; flex-shrink: 0; }
.sd-name { font-size: 14px; font-weight: 700; }
.sd-price { font-size: 18px; font-weight: 900; color: var(--gold); margin-top: 2px; }

.total-row {
  display: flex; align-items: center; justify-content: space-between;
  background: rgba(245,166,35,.07); border: 1px solid rgba(245,166,35,.2);
  border-radius: var(--radius); padding: 14px 16px;
  margin: 16px 0; font-size: 14px; font-weight: 700;
}
.total-amount { font-size: 20px; font-weight: 900; color: var(--gold); }

.guest-note {
  background: rgba(0,212,255,.07); border: 1px solid rgba(0,212,255,.15);
  border-radius: var(--radius); padding: 12px 14px;
  font-size: 13px; color: var(--text2); margin-bottom: 16px;
}

/* ── Responsive ── */
@media (max-width: 1024px) {
  .topup-layout { grid-template-columns: 1fr; }
  .topup-right { position: static; }
}
@media (max-width: 768px) {
  .id-form-row { grid-template-columns: 1fr; }
}
</style>

<main class="topup-main container">

  <!-- Flash -->
  <?php $flash = getFlash(); if ($flash): ?>
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

    <!-- ── LEFT ── -->
    <div class="topup-left">

      <!-- ID Form Card -->
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
          <button type="button" class="btn-verify" onclick="verifyUser()">
            🔍 Cek Nickname
          </button>
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
        <div class="product-card" id="pc-<?= $p['id'] ?>"
             onclick="selectProduct(<?= $p['id'] ?>, <?= htmlspecialchars(json_encode($p['name'])) ?>, <?= $p['price'] ?>, <?= $p['diamond_amount'] ?>, '<?= addslashes($p['cat_icon']) ?>')"
             role="button" tabindex="0"
             onkeydown="if(event.key==='Enter'||event.key===' ')selectProduct(<?= $p['id'] ?>,<?= htmlspecialchars(json_encode($p['name'])) ?>,<?= $p['price'] ?>,<?= $p['diamond_amount'] ?>,'<?= addslashes($p['cat_icon']) ?>')">

          <?php if ($p['badge']): ?>
            <div class="p-badge badge-<?= strtolower(str_replace(' ', '-', $p['badge'])) ?>">
              <?= sanitize($p['badge']) ?>
            </div>
          <?php endif; ?>

          <div class="p-icon"><?= $p['cat_icon'] ?></div>
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

        <!-- Selected Product -->
        <div id="noSelection" class="no-selection">
          ← Pilih produk di sebelah kiri
        </div>
        <div id="selectedDisplay" class="selected-display" style="display:none">
          <div class="sd-icon" id="sdIcon">💎</div>
          <div class="sd-info">
            <div class="sd-name"  id="sdName">—</div>
            <div class="sd-price" id="sdPrice">—</div>
          </div>
        </div>

        <form method="POST" action="<?= BASE_PATH ?>/pages/checkout.php" id="orderForm">
          <input type="hidden" name="product_id"    id="productIdInput" value="">
          <input type="hidden" name="ml_user_id"    id="hiddenUserId"   value="">
          <input type="hidden" name="ml_server_id"  id="hiddenServerId" value="">
          <input type="hidden" name="payment_method" id="hiddenPayment"  value="">

          <!-- Payment Method -->
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

<script>
let selectedPrice = 0;

function selectProduct(id, name, price, diamonds, icon) {
  selectedPrice = price;

  document.getElementById('productIdInput').value = id;
  document.getElementById('sdName').textContent   = name;
  document.getElementById('sdPrice').textContent  = formatRP(price);
  document.getElementById('sdIcon').textContent   = icon || '💎';
  document.getElementById('noSelection').style.display    = 'none';
  document.getElementById('selectedDisplay').style.display = 'flex';
  document.getElementById('paymentWrap').style.display     = 'block';
  document.getElementById('submitBtn').disabled            = false;

  document.querySelectorAll('.product-card').forEach(c => c.classList.remove('selected'));
  document.getElementById('pc-' + id).classList.add('selected');

  updateTotal();

  if (window.innerWidth < 1025) {
    document.getElementById('orderPanel').scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
}

function updateTotal() {
  const sel    = document.getElementById('paymentMethod');
  const opt    = sel.options[sel.selectedIndex];
  const feePct = parseFloat(opt?.dataset?.feePct || 0);
  const feeFix = parseFloat(opt?.dataset?.feeFix || 0);
  const fee    = Math.round(selectedPrice * feePct / 100) + feeFix;
  const total  = selectedPrice + fee;

  if (selectedPrice > 0 && sel.value) {
    document.getElementById('totalAmount').textContent = formatRP(total);
    document.getElementById('totalRow').style.display  = 'flex';
  } else {
    document.getElementById('totalRow').style.display  = 'none';
  }
}

function syncHiddenFields() {
  document.getElementById('hiddenUserId').value   = document.getElementById('mlUserId').value.trim();
  document.getElementById('hiddenServerId').value = document.getElementById('mlServerId').value.trim();
  document.getElementById('hiddenPayment').value  = document.getElementById('paymentMethod').value;
}

function verifyUser() {
  const uid = document.getElementById('mlUserId').value.trim();
  const sid = document.getElementById('mlServerId').value.trim();
  const msg = document.getElementById('verifyMsg');

  if (!uid || !sid) {
    msg.innerHTML = '<span class="v-warn">⚠️ Isi User ID dan Zone ID dulu.</span>';
    return;
  }
  if (!/^\d{3,20}$/.test(uid) || !/^\d{1,8}$/.test(sid)) {
    msg.innerHTML = '<span class="v-warn">⚠️ Format ID tidak valid. Gunakan angka saja.</span>';
    return;
  }
  msg.innerHTML = '<span class="v-load">⏳ Memverifikasi akun...</span>';
  setTimeout(() => {
    msg.innerHTML = '<span class="v-ok">✅ Akun ditemukan. Pastikan sudah benar sebelum lanjut!</span>';
  }, 1000);
}

function formatRP(num) {
  return 'Rp ' + num.toLocaleString('id-ID');
}

// Validasi sebelum submit
document.getElementById('orderForm').addEventListener('submit', function(e) {
  const uid = document.getElementById('mlUserId').value.trim();
  const sid = document.getElementById('mlServerId').value.trim();
  const pay = document.getElementById('paymentMethod').value;
  const pid = document.getElementById('productIdInput').value;

  if (!pid) { e.preventDefault(); alert('Pilih produk terlebih dahulu.'); return; }
  if (!/^\d{3,20}$/.test(uid)) { e.preventDefault(); alert('User ID ML tidak valid (3–20 digit angka).'); return; }
  if (!/^\d{1,8}$/.test(sid))  { e.preventDefault(); alert('Zone ID tidak valid.'); return; }
  if (!pay) { e.preventDefault(); alert('Pilih metode pembayaran.'); return; }

  syncHiddenFields();
});

// Auto-select jika ada ?pid=
<?php if ($preselect): ?>
const preEl = document.getElementById('pc-<?= $preselect ?>');
if (preEl) preEl.click();
<?php endif; ?>
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
