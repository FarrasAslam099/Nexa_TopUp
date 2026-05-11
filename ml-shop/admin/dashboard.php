<?php
// ============================================
// admin/dashboard.php — Dashboard Admin
// ============================================
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
requireAdmin();

// Stats
$stats = [
    'total_orders'    => $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
    'orders_today'    => $pdo->query("SELECT COUNT(*) FROM orders WHERE DATE(created_at) = CURDATE()")->fetchColumn(),
    'revenue_total'   => $pdo->query("SELECT SUM(total_price) FROM orders WHERE order_status = 'completed'")->fetchColumn() ?? 0,
    'revenue_today'   => $pdo->query("SELECT SUM(total_price) FROM orders WHERE order_status = 'completed' AND DATE(created_at) = CURDATE()")->fetchColumn() ?? 0,
    'total_users'     => $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn(),
    'pending_orders'  => $pdo->query("SELECT COUNT(*) FROM orders WHERE order_status = 'pending'")->fetchColumn(),
];

// Recent orders
$recentOrders = $pdo->query("
    SELECT o.*, u.username FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC LIMIT 10
")->fetchAll();

$pageTitle = 'Admin Dashboard – ML Shop';
require_once __DIR__ . '/../includes/header.php';
?>
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="container admin-page">
  <div class="admin-header">
    <h2>⚙️ Admin Dashboard</h2>
    <div class="admin-nav">
      <a href="/admin/dashboard.php" class="admin-link active">Dashboard</a>
      <a href="/admin/products.php" class="admin-link">Produk</a>
      <a href="/admin/orders.php" class="admin-link">Pesanan</a>
    </div>
  </div>

  <!-- Stats Cards -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon">📦</div>
      <div class="stat-info">
        <div class="stat-value"><?= number_format($stats['total_orders']) ?></div>
        <div class="stat-label">Total Pesanan</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon">🔥</div>
      <div class="stat-info">
        <div class="stat-value"><?= number_format($stats['orders_today']) ?></div>
        <div class="stat-label">Pesanan Hari Ini</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon">💰</div>
      <div class="stat-info">
        <div class="stat-value">Rp <?= number_format($stats['revenue_total'], 0, ',', '.') ?></div>
        <div class="stat-label">Total Pendapatan</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon">📅</div>
      <div class="stat-info">
        <div class="stat-value">Rp <?= number_format($stats['revenue_today'], 0, ',', '.') ?></div>
        <div class="stat-label">Pendapatan Hari Ini</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon">👥</div>
      <div class="stat-info">
        <div class="stat-value"><?= number_format($stats['total_users']) ?></div>
        <div class="stat-label">Total User</div>
      </div>
    </div>
    <div class="stat-card highlight">
      <div class="stat-icon">⏳</div>
      <div class="stat-info">
        <div class="stat-value"><?= number_format($stats['pending_orders']) ?></div>
        <div class="stat-label">Pesanan Pending</div>
      </div>
    </div>
  </div>

  <!-- Recent Orders -->
  <div class="admin-section">
    <div class="section-header">
      <div class="section-title">📋 Pesanan Terbaru</div>
      <a href="/admin/orders.php" class="see-all">Lihat Semua →</a>
    </div>
    <div class="history-table-wrap">
      <table class="history-table">
        <thead>
          <tr>
            <th>Kode</th><th>User</th><th>Produk</th><th>ML ID</th>
            <th>Total</th><th>Bayar</th><th>Status</th><th>Tanggal</th><th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($recentOrders as $o): ?>
          <tr>
            <td><code><?= htmlspecialchars($o['order_code']) ?></code></td>
            <td><?= $o['username'] ? htmlspecialchars($o['username']) : '<em>Guest</em>' ?></td>
            <td><?= htmlspecialchars($o['product_name']) ?></td>
            <td><?= htmlspecialchars($o['ml_user_id']) ?>/<?= htmlspecialchars($o['ml_server_id']) ?></td>
            <td>Rp <?= number_format($o['total_price'], 0, ',', '.') ?></td>
            <td><?= strtoupper($o['payment_method']) ?></td>
            <td>
              <form method="POST" action="/admin/orders.php" style="display:inline">
                <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                <select name="status" onchange="this.form.submit()" class="status-select status-<?= $o['order_status'] ?>">
                  <option value="pending"    <?= $o['order_status']==='pending'    ? 'selected' : '' ?>>Pending</option>
                  <option value="processing" <?= $o['order_status']==='processing' ? 'selected' : '' ?>>Diproses</option>
                  <option value="completed"  <?= $o['order_status']==='completed'  ? 'selected' : '' ?>>Selesai</option>
                  <option value="failed"     <?= $o['order_status']==='failed'     ? 'selected' : '' ?>>Gagal</option>
                </select>
              </form>
            </td>
            <td><?= date('d/m H:i', strtotime($o['created_at'])) ?></td>
            <td>
              <form method="POST" action="/admin/orders.php">
                <input type="hidden" name="delete_id" value="<?= $o['id'] ?>">
                <button type="submit" class="btn-delete" onclick="return confirm('Hapus order ini?')">🗑️</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
