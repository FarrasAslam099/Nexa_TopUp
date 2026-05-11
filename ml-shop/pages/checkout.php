<?php
// ============================================
// pages/checkout.php — Proses & Konfirmasi Order
// ============================================
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /pages/topup.php');
    exit;
}

$productId     = (int)($_POST['product_id'] ?? 0);
$mlUserId      = trim($_POST['ml_user_id'] ?? '');
$mlServerId    = trim($_POST['ml_server_id'] ?? '');
$paymentMethod = trim($_POST['payment_method'] ?? '');
$errors        = [];

// Validasi input
if (!$productId) $errors[] = 'Produk tidak valid.';
if (!preg_match('/^\d+$/', $mlUserId)) $errors[] = 'User ID ML tidak valid.';
if (!preg_match('/^\d+$/', $mlServerId)) $errors[] = 'Zone ID tidak valid.';
if (empty($paymentMethod)) $errors[] = 'Pilih metode pembayaran.';

if (!empty($errors)) {
    setFlash('error', implode(' | ', $errors));
    header('Location: /pages/topup.php');
    exit;
}

// Ambil produk
$stmt = $pdo->prepare("SELECT p.*, c.name AS cat_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = ? AND p.is_active = 1");
$stmt->execute([$productId]);
$product = $stmt->fetch();
if (!$product) {
    setFlash('error', 'Produk tidak ditemukan.');
    header('Location: /pages/topup.php');
    exit;
}

// Ambil metode pembayaran
$stmt2 = $pdo->prepare("SELECT * FROM payment_methods WHERE code = ? AND is_active = 1");
$stmt2->execute([$paymentMethod]);
$payMethod = $stmt2->fetch();
if (!$payMethod) {
    setFlash('error', 'Metode pembayaran tidak valid.');
    header('Location: /pages/topup.php');
    exit;
}

// Hitung total
$basePrice   = $product['price'];
$feeAmount   = round($basePrice * ($payMethod['fee_percent'] / 100)) + $payMethod['fee_fixed'];
$totalPrice  = $basePrice + $feeAmount;

// Jika POST confirm (konfirmasi order)
if (isset($_POST['confirm'])) {
    $orderCode = generateOrderCode();
    $userId    = $_SESSION['user_id'] ?? null;

    $ins = $pdo->prepare("
        INSERT INTO orders (order_code, user_id, ml_user_id, ml_server_id, product_id, product_name, unit_price, total_price, payment_method)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $ins->execute([
        $orderCode, $userId, $mlUserId, $mlServerId,
        $productId, $product['name'], $basePrice, $totalPrice, $paymentMethod
    ]);
    $orderId = $pdo->lastInsertId();

    header("Location: /pages/checkout.php?done=1&order=$orderCode");
    exit;
}

// Tampilkan halaman order done
if (isset($_GET['done']) && isset($_GET['order'])) {
    $stmt3 = $pdo->prepare("SELECT o.*, p.name AS product_name FROM orders o JOIN products p ON o.product_id = p.id WHERE o.order_code = ?");
    $stmt3->execute([$_GET['order']]);
    $order = $stmt3->fetch();

    $pageTitle = 'Order Berhasil – ML Shop';
    require_once __DIR__ . '/../includes/header.php';
    require_once __DIR__ . '/../includes/navbar.php';
?>
<main class="container">
  <div class="order-success">
    <div class="success-icon">✅</div>
    <h2>Order Berhasil Dibuat!</h2>
    <p>Order kamu sedang diproses. Diamond akan masuk dalam beberapa detik.</p>
    <div class="order-summary-box">
      <div class="os-row"><span>Kode Order</span><strong><?= htmlspecialchars($order['order_code']) ?></strong></div>
      <div class="os-row"><span>Produk</span><strong><?= htmlspecialchars($order['product_name']) ?></strong></div>
      <div class="os-row"><span>ML User ID</span><strong><?= htmlspecialchars($order['ml_user_id']) ?></strong></div>
      <div class="os-row"><span>Zone ID</span><strong><?= htmlspecialchars($order['ml_server_id']) ?></strong></div>
      <div class="os-row"><span>Metode Bayar</span><strong><?= htmlspecialchars(strtoupper($order['payment_method'])) ?></strong></div>
      <div class="os-row total"><span>Total</span><strong>Rp <?= number_format($order['total_price'], 0, ',', '.') ?></strong></div>
    </div>
    <div class="success-actions">
      <a href="/pages/history.php" class="btn-outline">📋 Lihat Riwayat</a>
      <a href="/pages/topup.php" class="btn-primary">⚡ Top Up Lagi</a>
    </div>
  </div>
</main>
<?php
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// Halaman konfirmasi order
$pageTitle = 'Konfirmasi Order – ML Shop';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<main class="container">
  <div class="checkout-wrap">
    <h2>🛒 Konfirmasi Pesanan</h2>

    <div class="checkout-grid">
      <!-- Order Summary -->
      <div class="checkout-summary">
        <h4>Ringkasan Pesanan</h4>
        <div class="cs-row">
          <span>Produk</span>
          <strong><?= htmlspecialchars($product['name']) ?></strong>
        </div>
        <div class="cs-row">
          <span>Kategori</span>
          <strong><?= htmlspecialchars($product['cat_name']) ?></strong>
        </div>
        <?php if ($product['diamond_amount'] > 0): ?>
        <div class="cs-row">
          <span>Diamond</span>
          <strong>💎 <?= number_format($product['diamond_amount']) ?></strong>
        </div>
        <?php endif; ?>
        <div class="cs-row">
          <span>ML User ID</span>
          <strong><?= htmlspecialchars($mlUserId) ?></strong>
        </div>
        <div class="cs-row">
          <span>Zone / Server ID</span>
          <strong><?= htmlspecialchars($mlServerId) ?></strong>
        </div>
        <div class="cs-divider"></div>
        <div class="cs-row">
          <span>Harga Produk</span>
          <strong>Rp <?= number_format($basePrice, 0, ',', '.') ?></strong>
        </div>
        <?php if ($feeAmount > 0): ?>
        <div class="cs-row fee">
          <span>Biaya Layanan</span>
          <strong>Rp <?= number_format($feeAmount, 0, ',', '.') ?></strong>
        </div>
        <?php endif; ?>
        <div class="cs-row total">
          <span>Total Bayar</span>
          <strong>Rp <?= number_format($totalPrice, 0, ',', '.') ?></strong>
        </div>
      </div>

      <!-- Payment Info -->
      <div class="checkout-payment">
        <h4>Metode Pembayaran</h4>
        <div class="pay-selected">
          <span class="pay-icon"><?= $payMethod['icon'] ?></span>
          <span class="pay-name"><?= htmlspecialchars($payMethod['name']) ?></span>
        </div>
        <div class="pay-instruction">
          <p>💡 Setelah mengklik <strong>Konfirmasi & Bayar</strong>, kamu akan diarahkan ke halaman pembayaran.</p>
          <p>Diamond akan otomatis masuk ke akun ML kamu setelah pembayaran dikonfirmasi.</p>
        </div>

        <form method="POST" action="/pages/checkout.php">
          <input type="hidden" name="product_id" value="<?= $productId ?>">
          <input type="hidden" name="ml_user_id" value="<?= htmlspecialchars($mlUserId) ?>">
          <input type="hidden" name="ml_server_id" value="<?= htmlspecialchars($mlServerId) ?>">
          <input type="hidden" name="payment_method" value="<?= htmlspecialchars($paymentMethod) ?>">
          <input type="hidden" name="confirm" value="1">

          <button type="submit" class="btn-primary btn-block">
            ✅ Konfirmasi & Bayar — Rp <?= number_format($totalPrice, 0, ',', '.') ?>
          </button>
        </form>

        <a href="/pages/topup.php" class="btn-back">← Kembali ubah pesanan</a>
      </div>
    </div>
  </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
