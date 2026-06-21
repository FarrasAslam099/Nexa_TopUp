<?php
// pages/checkout.php — Konfirmasi & Selesai
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';

// ── Halaman "done" setelah order disimpan ──
if (isset($_GET['done'], $_GET['code'])) {
    $stmt = $pdo->prepare("
        SELECT id, order_code, user_id, ml_user_id, ml_server_id,
               product_id, product_name, total_price, payment_method,
               order_status, created_at
        FROM orders
        WHERE order_code = ?
    ");
    $stmt->execute([strtoupper(trim($_GET['code']))]);
    $order = $stmt->fetch();

    if (!$order) { header('Location: ' . BASE_PATH . '/pages/index.php'); exit; }

    // Ambil diamond_amount dari products pakai product_id yang sudah tersimpan di orders
    $stmtP = $pdo->prepare('SELECT diamond_amount FROM products WHERE id = ?');
    $stmtP->execute([$order['product_id']]);
    $diamondAmount = (int)($stmtP->fetchColumn() ?? 0);

    $pageTitle = 'Pesanan Berhasil — Nexa_Topup';
    require_once __DIR__ . '/../includes/header.php';
    require_once __DIR__ . '/../includes/navbar.php';
?>
<main class="container">
  <div class="done-wrap">
    <div class="done-icon">✅</div>
    <h2>Pesanan Berhasil Dibuat!</h2>
    <p>Diamond / item sedang diproses dan akan masuk ke akun ML kamu sebentar lagi.</p>
    <div class="done-box">
      <div class="done-row"><span>Kode Pesanan</span><strong class="mono"><?= sanitize($order['order_code']) ?></strong></div>
      <div class="done-row"><span>Produk</span><strong><?= sanitize($order['product_name']) ?></strong></div>
      <?php if ($diamondAmount > 0): ?>
      <div class="done-row"><span>Diamond</span><strong>💎 <?= number_format($diamondAmount) ?></strong></div>
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

// ── Hanya terima POST ──
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_PATH . '/pages/topup.php'); exit;
}

$productId     = (int)  ($_POST['product_id']     ?? 0);
$mlUserId      = trim(   $_POST['ml_user_id']     ?? '');
$mlServerId    = trim(   $_POST['ml_server_id']   ?? '');
$paymentMethod = trim(   $_POST['payment_method'] ?? '');
$errors        = [];

if ($productId < 1)                          $errors[] = 'Produk tidak valid.';
if (!preg_match('/^\d{3,20}$/', $mlUserId))  $errors[] = 'User ID ML tidak valid (angka, 3–20 digit).';
if (!preg_match('/^\d{1,8}$/', $mlServerId)) $errors[] = 'Zone ID tidak valid.';
if (empty($paymentMethod))                   $errors[] = 'Pilih metode pembayaran.';

if (!empty($errors)) {
    setFlash('error', implode(' | ', $errors));
    header('Location: ' . BASE_PATH . '/pages/topup.php'); exit;
}

// Ambil produk — hanya dari tabel products
$stmtProd = $pdo->prepare('SELECT * FROM products WHERE id = ? AND is_active = 1');
$stmtProd->execute([$productId]);
$product = $stmtProd->fetch();
if (!$product) {
    setFlash('error', 'Produk tidak ditemukan atau sudah tidak aktif.');
    header('Location: ' . BASE_PATH . '/pages/topup.php'); exit;
}

// Ambil kategori produk — query terpisah ke categories
$stmtCat = $pdo->prepare('SELECT name, icon FROM categories WHERE id = ?');
$stmtCat->execute([$product['category_id']]);
$category = $stmtCat->fetch();

// Ambil metode bayar
$stmtPay = $pdo->prepare('SELECT * FROM payment_methods WHERE code = ? AND is_active = 1');
$stmtPay->execute([$paymentMethod]);
$payMethod = $stmtPay->fetch();
if (!$payMethod) {
    setFlash('error', 'Metode pembayaran tidak valid.');
    header('Location: ' . BASE_PATH . '/pages/topup.php'); exit;
}

$basePrice  = (float)$product['price'];
$fee        = round($basePrice * $payMethod['fee_percent'] / 100) + $payMethod['fee_fixed'];
$totalPrice = $basePrice + $fee;

// ── Simpan order setelah konfirmasi ──
if (isset($_POST['confirm'])) {
    $orderCode = generateOrderCode();
    $userId    = $_SESSION['user_id'] ?? null;
    $ins = $pdo->prepare("
        INSERT INTO orders
            (order_code, user_id, ml_user_id, ml_server_id, product_id, product_name, total_price, payment_method)
        VALUES (?,?,?,?,?,?,?,?)
    ");
    $ins->execute([$orderCode, $userId, $mlUserId, $mlServerId, $productId, $product['name'], $totalPrice, $paymentMethod]);
    header('Location: ' . BASE_PATH . '/pages/checkout.php?done=1&code=' . $orderCode); exit;
}

$pageTitle = 'Konfirmasi Pesanan — Nexa_Topup';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<main class="container">
  <div class="checkout-wrap">
    <h2>🛒 Konfirmasi Pesanan</h2>
    <div class="checkout-grid">

      <div class="co-card">
        <h4>Ringkasan Pesanan</h4>
        <div class="co-row"><span>Produk</span><strong><?= sanitize($product['name']) ?></strong></div>
        <div class="co-row">
          <span>Kategori</span>
          <strong><?= $category['icon'] ?? '' ?> <?= sanitize($category['name'] ?? '-') ?></strong>
        </div>
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
