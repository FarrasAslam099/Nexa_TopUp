<?php
// ============================================
// admin/orders.php — Kelola Pesanan
// ============================================
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
requireAdmin();

// Handle update status
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['order_id'], $_POST['status'])) {
        $allowed = ['pending', 'processing', 'completed', 'failed'];
        $status  = in_array($_POST['status'], $allowed) ? $_POST['status'] : 'pending';
        $stmt    = $pdo->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
        $stmt->execute([$status, (int)$_POST['order_id']]);
    }
    if (isset($_POST['delete_id'])) {
        $pdo->prepare("DELETE FROM orders WHERE id = ?")->execute([(int)$_POST['delete_id']]);
    }
    header('Location: /admin/orders.php');
    exit;
}

// Filter
$statusFilter = $_GET['status'] ?? '';
$search       = trim($_GET['q'] ?? '');
$page         = max(1, (int)($_GET['page'] ?? 1));
$limit        = 20;
$offset       = ($page - 1) * $limit;

$where  = [];
$params = [];
if ($statusFilter) { $where[] = 'o.order_status = ?'; $params[] = $statusFilter; }
if ($search)        { $where[] = '(o.order_code LIKE ? OR o.ml_user_id LIKE ? OR o.product_name LIKE ?)'; $params[] = "%$search%"; $params[] = "%$search%"; $params[] = "%$search%"; }
$whereStr = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM orders o $whereStr");
$countStmt->execute($params);
$totalOrders = $countStmt->fetchColumn();
$totalPages  = ceil($totalOrders / $limit);

$stmt = $pdo->prepare("
    SELECT o.*, u.username FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    $whereStr ORDER BY o.created_at DESC LIMIT $limit OFFSET $offset
");
$stmt->execute($params);
$orders = $stmt->fetchAll();

$pageTitle = 'Kelola Pesanan – Admin';
require_once __DIR__ . '/../includes/header.php';
?>
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="container admin-page">
  <div class="admin-header">
    <h2>📋 Kelola Pesanan</h2>
    <div class="admin-nav">
      <a href="/admin/dashboard.php" class="admin-link">Dashboard</a>
      <a href="/admin/products.php" class="admin-link">Produk</a>
      <a href="/admin/orders.php" class="admin-link active">Pesanan</a>
    </div>
  </div>

  <!-- Filter Bar -->
  <form method="GET" class="filter-bar">
    <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Cari kode, ML ID, produk...">
    <select name="status">
      <option value="">Semua Status</option>
      <option value="pending"    <?= $statusFilter==='pending'    ? 'selected':'' ?>>Pending</option>
      <option value="processing" <?= $statusFilter==='processing' ? 'selected':'' ?>>Diproses</option>
      <option value="completed"  <?= $statusFilter==='completed'  ? 'selected':'' ?>>Selesai</option>
      <option value="failed"     <?= $statusFilter==='failed'     ? 'selected':'' ?>>Gagal</option>
    </select>
    <button type="submit" class="btn-primary">🔍 Filter</button>
    <a href="/admin/orders.php" class="btn-outline">Reset</a>
  </form>

  <div class="history-table-wrap">
    <table class="history-table">
      <thead>
        <tr>
          <th>Kode</th><th>User</th><th>Produk</th><th>ML ID / Zone</th>
          <th>Total</th><th>Bayar</th><th>Status</th><th>Tgl</th><th>Hapus</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orders as $o): ?>
        <tr>
          <td><code><?= htmlspecialchars($o['order_code']) ?></code></td>
          <td><?= $o['username'] ? htmlspecialchars($o['username']) : '<em>Guest</em>' ?></td>
          <td><?= htmlspecialchars($o['product_name']) ?></td>
          <td><?= htmlspecialchars($o['ml_user_id']) ?> / <?= htmlspecialchars($o['ml_server_id']) ?></td>
          <td>Rp <?= number_format($o['total_price'], 0, ',', '.') ?></td>
          <td><?= strtoupper($o['payment_method']) ?></td>
          <td>
            <form method="POST">
              <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
              <select name="status" onchange="this.form.submit()" class="status-select status-<?= $o['order_status'] ?>">
                <option value="pending"    <?= $o['order_status']==='pending'    ? 'selected':'' ?>>Pending</option>
                <option value="processing" <?= $o['order_status']==='processing' ? 'selected':'' ?>>Diproses</option>
                <option value="completed"  <?= $o['order_status']==='completed'  ? 'selected':'' ?>>Selesai</option>
                <option value="failed"     <?= $o['order_status']==='failed'     ? 'selected':'' ?>>Gagal</option>
              </select>
            </form>
          </td>
          <td><?= date('d/m H:i', strtotime($o['created_at'])) ?></td>
          <td>
            <form method="POST">
              <input type="hidden" name="delete_id" value="<?= $o['id'] ?>">
              <button type="submit" class="btn-delete" onclick="return confirm('Hapus?')">🗑️</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($orders)): ?>
          <tr><td colspan="9" style="text-align:center;padding:40px">Tidak ada pesanan ditemukan.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <?php if ($totalPages > 1): ?>
  <div class="pagination">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <a href="?page=<?= $i ?>&status=<?= urlencode($statusFilter) ?>&q=<?= urlencode($search) ?>"
       class="page-btn <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
  </div>
  <?php endif; ?>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
