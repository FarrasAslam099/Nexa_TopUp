<?php
// ============================================================
// pages/history.php — Riwayat Transaksi User
// ============================================================
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
requireLogin('/pages/history.php');

$userId = (int)$_SESSION['user_id'];
$page   = max(1, (int)($_GET['page'] ?? 1));
$limit  = 10;
$offset = ($page - 1) * $limit;

// Total
$total = $pdo->prepare('SELECT COUNT(*) FROM orders WHERE user_id = ?');
$total->execute([$userId]);
$totalRows  = (int)$total->fetchColumn();
$totalPages = (int)ceil($totalRows / $limit);

// Orders
$stmt = $pdo->prepare("
    SELECT o.*, p.diamond_amount, c.icon AS cat_icon
    FROM orders o
    JOIN products p  ON o.product_id     = p.id
    JOIN categories c ON p.category_id   = c.id
    WHERE o.user_id = ?
    ORDER BY o.created_at DESC
    LIMIT ? OFFSET ?
");
$stmt->execute([$userId, $limit, $offset]);
$orders = $stmt->fetchAll();

$statusMap = [
    'pending'    => ['label' => 'Menunggu',  'class' => 'st-pending'],
    'processing' => ['label' => 'Diproses',  'class' => 'st-processing'],
    'completed'  => ['label' => 'Selesai',   'class' => 'st-done'],
    'failed'     => ['label' => 'Gagal',     'class' => 'st-failed'],
];

$pageTitle = 'Riwayat Transaksi — Nexa_Topup';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<style>
/* ============================================================
   STYLE KHUSUS HALAMAN: pages/history.php
   ============================================================ */
.page-header {
  display: flex; align-items: baseline; gap: 16px;
  margin-bottom: 24px; padding-top: 32px;
}
.page-header h2 { font-size: 22px; font-weight: 700; }
.page-subtitle { font-size: 14px; color: var(--text3); }
</style>

<main class="container">
  <div class="page-header">
    <h2>📋 Riwayat Transaksi</h2>
    <span class="page-subtitle">Total <?= $totalRows ?> transaksi</span>
  </div>

  <?php if (empty($orders)): ?>
    <div class="empty-state">
      <div class="empty-icon">🛒</div>
      <p>Belum ada transaksi. Yuk top up sekarang!</p>
      <a href="<?= BASE_PATH ?>/pages/topup.php" class="btn-primary">💎 Top Up Sekarang</a>
    </div>
  <?php else: ?>

  <div class="table-wrap">
    <table class="data-table">
      <thead>
        <tr>
          <th>Kode Pesanan</th>
          <th>Produk</th>
          <th>ML ID / Zone</th>
          <th>Pembayaran</th>
          <th>Total</th>
          <th>Status</th>
          <th>Tanggal</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orders as $o):
          $s = $statusMap[$o['order_status']] ?? ['label' => $o['order_status'], 'class' => ''];
        ?>
        <tr>
          <td><span class="mono"><?= sanitize($o['order_code']) ?></span></td>
          <td>
            <strong><?= $o['cat_icon'] ?> <?= sanitize($o['product_name']) ?></strong>
            <?php if ($o['diamond_amount'] > 0): ?>
              <div class="cell-sub">💎 <?= number_format($o['diamond_amount']) ?> Diamond</div>
            <?php endif; ?>
          </td>
          <td>
            <span class="mono"><?= sanitize($o['ml_user_id']) ?></span>
            <span class="cell-sep">/</span>
            <span class="mono"><?= sanitize($o['ml_server_id']) ?></span>
          </td>
          <td><?= strtoupper(sanitize($o['payment_method'])) ?></td>
          <td class="price-cell"><?= formatRupiah($o['total_price']) ?></td>
          <td><span class="st-badge <?= $s['class'] ?>"><?= $s['label'] ?></span></td>
          <td class="date-cell"><?= date('d M Y', strtotime($o['created_at'])) ?><div class="cell-sub"><?= date('H:i', strtotime($o['created_at'])) ?></div></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <?php if ($totalPages > 1): ?>
  <div class="pagination">
    <?php if ($page > 1): ?>
      <a href="?page=<?= $page - 1 ?>" class="pg-btn">← Prev</a>
    <?php endif; ?>
    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
      <a href="?page=<?= $i ?>" class="pg-btn <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
    <?php if ($page < $totalPages): ?>
      <a href="?page=<?= $page + 1 ?>" class="pg-btn">Next →</a>
    <?php endif; ?>
  </div>
  <?php endif; ?>

  <?php endif; ?>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>