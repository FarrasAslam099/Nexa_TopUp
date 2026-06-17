<?php
// ============================================================
// pages/checkout.php
// ============================================================
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';

// ── Halaman "done" ──
if (isset($_GET['done'], $_GET['code'])) {
    $stmt = $pdo->prepare("
        SELECT o.*, p.diamond_amount, c.name AS cat_name, c.icon AS cat_icon
        FROM orders o
        JOIN products p ON o.product_id = p.id
        JOIN categories c ON p.category_id = c.id
        WHERE o.order_code = ?
    ");
    $stmt->execute([strtoupper(trim($_GET['code']))]);
    $order = $stmt->fetch();

    if (!$order) { header('Location: ' . BASE_PATH . '/pages/index.php'); exit; }

    $pageTitle = 'Pesanan Berhasil — Nexa_Topup';
    require_once __DIR__ . '/../includes/header.php';
    require_once __DIR__ . '/../includes/navbar.php';
?>
<style>
/* ============================================================
   STYLE KHUSUS HALAMAN: pages/checkout.php (Done Page)
   ============================================================ */
.done-wrap {
  max-width: 520px; margin: 60px auto;
  text-align: center; padding-bottom: 60px;
}
.done-icon { font-size: 64px; margin-bottom: 16px; }
.done-wrap h2 { font-size: 24px; font-weight: 700; margin-bottom: 10px; }
.done-wrap > p { color: var(--text2); font-size: 15px; margin-bottom: 28px; }
.done-box {
  background: var(--bg2); border: 1px solid var(--border);
  border-radius: var(--radius-lg); padding: 24px;
  margin-bottom: 24px; text-align: left;
}
.done-row {
  display: flex; justify-content: space-between; align-items: center;
  gap: 12px; padding: 10px 0; border-bottom: 1px solid var(--border);
  font-size: 14px;
}
.done-row:last-child { border-bottom: none; }
.done-row span { color: var(--text2); }
.done-total span, .done-total strong { font-size: 16px; font-weight: 800; }
.done-total strong { color: var(--gold); }
.done-actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
</style>
<main class="container">
  <div class="done-wrap">
    <div class="done-icon">✅</div>
    <h2>Pesanan Berhasil Dibuat!</h2>
    <p>Diamond / item sedang diproses dan akan masuk ke akun ML kamu sebentar lagi.</p>
    <div class="done-box">
      <div class="done-row"><span>Kode Pesanan</span><strong class="mono"><?= sanitize($order['order_code']) ?></strong></div>
      <div class="done-row"><span>Produk</span><strong><?= sanitize($order['product_name']) ?></strong></div>
      <?php if ($order['diamond_amount'] > 0): ?>
      <div class="done-row"><span>Diamond</span><strong>💎 <?= number_format($order['diamond_amount']) ?></strong></div>
      <?php endif; ?>
      <div class="done-row"><span>User ID / Zone</span><strong><?= sanitize($order['ml_user_id']) ?> / <?= sanitize($order['ml_server_id']) ?></strong></div>
      <div class="done-row"><span>Metode Bayar</span><strong><?= strtoupper(sanitize($order['payment_method'])) ?></strong></div>
      <div class="done-row done-total"><span>Total Dibayar</span><strong><?= formatRupiah($order['total_price']) ?></strong></div>
    </div>
    <div class="done-actions">
      <a href="<?= BASE_PATH ?>/pages/history.php" class="btn-outline">📋 Lihat Riwayat</a>
      <a href="<?= BASE_PATH ?>/pages/topup.php"   class="btn-primary">⚡ Top Up Lagi</a>
    </div>
  </div>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; exit; }

// ── Hanya POST ──
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_PATH . '/pages/topup.php'); exit;
}

$productId     = (int)($_POST['product_id']     ?? 0);
$mlUserId      = trim($_POST['ml_user_id']      ?? '');
$mlServerId    = trim($_POST['ml_server_id']    ?? '');
$paymentMethod = trim($_POST['payment_method']  ?? '');
$errors        = [];

if ($productId < 1)                            $errors[] = 'Produk tidak valid.';
if (!preg_match('/^\d{3,20}$/', $mlUserId))    $errors[] = 'User ID ML tidak valid (angka, 3–20 digit).';
if (!preg_match('/^\d{1,8}$/', $mlServerId))   $errors[] = 'Zone ID tidak valid.';
if (empty($paymentMethod))                     $errors[] = 'Pilih metode pembayaran.';

if (!empty($errors)) {
    setFlash('error', implode(' | ', $errors));
    header('Location: ' . BASE_PATH . '/pages/topup.php'); exit;
}

// Ambil produk
$stmt = $pdo->prepare("
    SELECT p.*, c.name AS cat_name, c.icon AS cat_icon
    FROM products p JOIN categories c ON p.category_id = c.id
    WHERE p.id = ? AND p.is_active = 1
");
$stmt->execute([$productId]);
$product = $stmt->fetch();
if (!$product) {
    setFlash('error', 'Produk tidak ditemukan atau sudah tidak aktif.');
    header('Location: ' . BASE_PATH . '/pages/topup.php'); exit;
}

// Ambil metode bayar
$stmt2 = $pdo->prepare('SELECT * FROM payment_methods WHERE code = ? AND is_active = 1');
$stmt2->execute([$paymentMethod]);
$payMethod = $stmt2->fetch();
if (!$payMethod) {
    setFlash('error', 'Metode pembayaran tidak valid.');
    header('Location: ' . BASE_PATH . '/pages/topup.php'); exit;
}

$basePrice  = (float)$product['price'];
$fee        = round($basePrice * $payMethod['fee_percent'] / 100) + $payMethod['fee_fixed'];
$totalPrice = $basePrice + $fee;

// ── Konfirmasi & simpan ──
if (isset($_POST['confirm'])) {
    $orderCode = generateOrderCode();
    $userId    = $_SESSION['user_id'] ?? null;
    $ins = $pdo->prepare("
        INSERT INTO orders (order_code, user_id, ml_user_id, ml_server_id, product_id, product_name, total_price, payment_method)
        VALUES (?,?,?,?,?,?,?,?)
    ");
    $ins->execute([$orderCode, $userId, $mlUserId, $mlServerId, $productId, $product['name'], $totalPrice, $paymentMethod]);
    header('Location: ' . BASE_PATH . '/pages/checkout.php?done=1&code=' . $orderCode); exit;
}

$pageTitle = 'Konfirmasi Pesanan — Nexa_Topup';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<style>
/* ============================================================
   STYLE KHUSUS HALAMAN: pages/checkout.php (Halaman Konfirmasi)
   ============================================================ */
.checkout-wrap {
  max-width: 820px; margin: 40px auto; padding-bottom: 60px;
}
.checkout-wrap h2 { font-size: 22px; font-weight: 700; margin-bottom: 24px; }
.checkout-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }

.co-card {
  background: var(--bg2); border: 1px solid var(--border);
  border-radius: var(--radius-lg); padding: 24px;
}
.co-card h4 { font-size: 15px; font-weight: 700; margin-bottom: 16px; color: var(--text2); }
.co-row {
  display: flex; justify-content: space-between; align-items: flex-start;
  gap: 12px; padding: 9px 0; border-bottom: 1px solid var(--border);
  font-size: 14px;
}
.co-row:last-child { border-bottom: none; }
.co-row span { color: var(--text2); flex-shrink: 0; }
.co-row strong { text-align: right; }
.co-fee strong { color: var(--text3); }
.co-total { margin-top: 4px; }
.co-total span { font-weight: 700; font-size: 15px; }
.co-total strong { font-size: 20px; font-weight: 900; color: var(--gold); }
.co-divider { height: 1px; background: var(--border); margin: 8px 0; }

.pay-chosen {
  display: flex; align-items: center; gap: 12px;
  background: var(--bg3); border: 1px solid var(--border);
  border-radius: var(--radius); padding: 14px;
  margin-bottom: 16px;
}
.pay-chosen-icon { font-size: 24px; }
.pay-chosen-name { font-size: 16px; font-weight: 700; }

.pay-note { margin-bottom: 20px; }
.pay-note p { font-size: 13px; color: var(--text2); margin-bottom: 6px; }

.btn-back-link {
  display: block; text-align: center; margin-top: 12px;
  font-size: 13px; color: var(--text3);
}
.btn-back-link:hover { color: var(--cyan); }

/* ── Responsive ── */
@media (max-width: 768px) {
  .checkout-grid { grid-template-columns: 1fr; }
}
</style>

<main class="container">
  <div class="checkout-wrap">
    <h2>🛒 Konfirmasi Pesanan</h2>
    <div class="checkout-grid">

      <div class="co-card">
        <h4>Ringkasan Pesanan</h4>
        <div class="co-row"><span>Produk</span><strong><?= sanitize($product['name']) ?></strong></div>
        <div class="co-row"><span>Kategori</span><strong><?= $product['cat_icon'] ?> <?= sanitize($product['cat_name']) ?></strong></div>
        <?php if ($product['diamond_amount'] > 0): ?>
        <div class="co-row"><span>Diamond</span><strong>💎 <?= number_format($product['diamond_amount']) ?></strong></div>
        <?php endif; ?>
        <div class="co-row"><span>User ID ML</span><strong class="mono"><?= sanitize($mlUserId) ?></strong></div>
        <div class="co-row"><span>Zone / Server ID</span><strong class="mono"><?= sanitize($mlServerId) ?></strong></div>
        <div class="co-divider"></div>
        <div class="co-row"><span>Harga Produk</span><strong><?= formatRupiah($basePrice) ?></strong></div>
        <?php if ($fee > 0): ?>
        <div class="co-row co-fee"><span>Biaya Layanan</span><strong><?= formatRupiah($fee) ?></strong></div>
        <?php endif; ?>
        <div class="co-row co-total">
          <span>Total Bayar</span>
          <strong><?= formatRupiah($totalPrice) ?></strong>
        </div>
      </div>

      <div class="co-card">
        <h4>Metode Pembayaran</h4>
        <div class="pay-chosen">
          <span class="pay-chosen-icon"><?= $payMethod['icon'] ?></span>
          <span class="pay-chosen-name"><?= sanitize($payMethod['name']) ?></span>
        </div>
        <div class="pay-note">
          <p>✅ Setelah konfirmasi, kamu akan diarahkan ke halaman pembayaran.</p>
          <p>⚡ Diamond masuk otomatis dalam beberapa detik setelah pembayaran diterima.</p>
          <p>⚠️ Pastikan <strong>User ID</strong> dan <strong>Zone ID</strong> sudah benar sebelum lanjut.</p>
        </div>
        <form method="POST" action="<?= BASE_PATH ?>/pages/checkout.php">
          <input type="hidden" name="product_id"     value="<?= $productId ?>">
          <input type="hidden" name="ml_user_id"     value="<?= sanitize($mlUserId) ?>">
          <input type="hidden" name="ml_server_id"   value="<?= sanitize($mlServerId) ?>">
          <input type="hidden" name="payment_method" value="<?= sanitize($paymentMethod) ?>">
          <input type="hidden" name="confirm"        value="1">
          <button type="submit" class="btn-primary btn-block">
            ✅ Konfirmasi & Bayar — <?= formatRupiah($totalPrice) ?>
          </button>
        </form>
        <a href="javascript:history.back()" class="btn-back-link">← Ubah pesanan</a>
      </div>

    </div>
  </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
