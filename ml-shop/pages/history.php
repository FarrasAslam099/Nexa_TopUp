<?php
// ============================================
// pages/history.php — Riwayat Transaksi
// ============================================
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
requireLogin();

$userId = $_SESSION['user_id'];
$page   = max(1, (int)($_GET['page'] ?? 1));
$limit  = 10;
$offset = ($page - 1) * $limit;

// Total orders
$total = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
$total->execute([$userId]);
$totalOrders = $total->fetchColumn();
$totalPages  = ceil($totalOrders / $limit);

// Ambil orders
$stmt = $pdo->prepare("
    SELECT o.*, p.diamond_amount
    FROM orders o
    JOIN products p ON o.product_id = p.id
    WHERE o.user_id = ?
    ORDER BY o.created_at DESC
    LIMIT ? OFFSET ?
");
$stmt->execute([$userId, $limit, $offset]);
$orders = $stmt->fetchAll();

$statusLabel = [
    'pending'    => ['label' => 'Menunggu', 'class' => 'status-pending'],
    'processing' => ['label' => 'Diproses', 'class' => 'status-processing'],
    'completed'  => ['label' => 'Selesai',  'class' => 'status-done'],
    'failed'     => ['label' => 'Gagal',    'class' => 'status-failed'],
];

$pageTitle = 'Riwayat Transaksi – ML Shop';
require_once __DIR__ . '/../includes/header.php';
?>
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="container">
  <h2 class="page-title">📋 Riwayat Transaksi</h2>

  <?php if (empty($orders)): ?>
    <div class="empty-state">
      <div class="empty-icon">🛒</div>
      <p>Belum ada transaksi.</p>
      <a href="/pages/topup.php" class="btn-primary">Top Up Sekarang</a>
    </div>
  <?php else: ?>
  <div class="history-table-wrap">
    <table class="history-table">
      <thead>
        <tr>
          <th>Kode Order</th>
          <th>Produk</th>
          <th>ML ID</th>
          <th>Pembayaran</th>
          <th>Total</th>
          <th>Status</th>
          <th>Tanggal</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orders as $o):
          $s = $statusLabel[$o['order_status']] ?? ['label' => $o['order_status'], 'class' => ''];
        ?>
        <tr>
          <td><code><?= htmlspecialchars($o['order_code']) ?></code></td>
          <td>
            <strong><?= htmlspecialchars($o['product_name']) ?></strong>
            <?php if ($o['diamond_amount'] > 0): ?>
              <div class="small-text">💎 <?= number_format($o['diamond_amount']) ?></div>
            <?php endif; ?>
          </td>
          <td><?= htmlspecialchars($o['ml_user_id']) ?> / <?= htmlspecialchars($o['ml_server_id']) ?></td>
          <td><?= strtoupper(htmlspecialchars($o['payment_method'])) ?></td>
          <td>Rp <?= number_format($o['total_price'], 0, ',', '.') ?></td>
          <td><span class="status-badge <?= $s['class'] ?>"><?= $s['label'] ?></span></td>
          <td><?= date('d M Y H:i', strtotime($o['created_at'])) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <?php if ($totalPages > 1): ?>
  <div class="pagination">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <a href="?page=<?= $i ?>" class="page-btn <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
  </div>
  <?php endif; ?>
  <?php endif; ?>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
