<?php
// admin/dashboard.php — Dashboard Admin Nexa_Topup
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
requireAdmin();

// ── Stats ──
$stats = [
    'orders_total'   => (int)$pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
    'orders_today'   => (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE DATE(created_at)=CURDATE()")->fetchColumn(),
    'orders_pending' => (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE order_status='pending'")->fetchColumn(),
    'revenue_total'  => (float)($pdo->query("SELECT COALESCE(SUM(total_price),0) FROM orders WHERE order_status='completed'")->fetchColumn()),
    'revenue_today'  => (float)($pdo->query("SELECT COALESCE(SUM(total_price),0) FROM orders WHERE order_status='completed' AND DATE(created_at)=CURDATE()")->fetchColumn()),
    'users_total'    => (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='user'")->fetchColumn(),
    'products_total' => (int)$pdo->query("SELECT COUNT(*) FROM products WHERE is_active=1")->fetchColumn(),
];

// ── Recent Orders ── (tanpa JOIN, username diambil terpisah)
$recent = $pdo->query("
     SELECT order_code, user_id, product_name, ml_user_id, ml_server_id,
           total_price, payment_method, order_status, created_at, id
    FROM orders
    ORDER BY created_at DESC
    LIMIT 15
")->fetchAll();

$userIds = array_filter(array_unique(array_column($recent, 'user_id')));
$userMap = [];
if ($userIds) {
    $in   = implode(',', array_map('intval', $userIds));
    $rows = $pdo->query("SELECT id, username FROM users WHERE id IN ($in)")->fetchAll();
    foreach ($rows as $u) { $userMap[$u['id']] = $u['username']; }
}

$flash     = getFlash();
$pageTitle = 'Dashboard Admin — Nexa_Topup';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>


<main class="container admin-main">

  <div class="admin-head">
    <div>
      <h2>⚙️ Admin Dashboard</h2>
      <span class="admin-sub">Nexa_Topup Control Panel</span>
    </div>
    <div class="admin-nav-pills">
      <a href="<?= BASE_PATH ?>/admin/dashboard.php" class="pill active">📊 Dashboard</a>
      <a href="<?= BASE_PATH ?>/admin/products.php"  class="pill">📦 Produk</a>
      <a href="<?= BASE_PATH ?>/admin/orders.php"    class="pill">📋 Pesanan</a>
    </div>
  </div>

  <?php if ($flash): ?>
    <div class="flash flash-<?= $flash['type'] ?>"><?= sanitize($flash['msg']) ?></div>
  <?php endif; ?>

  <!-- ── Stats Grid ── -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-ic">📦</div>
      <div class="stat-body">
        <div class="stat-val"><?= number_format($stats['orders_total']) ?></div>
        <div class="stat-lbl">Total Pesanan</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-ic">🔥</div>
      <div class="stat-body">
        <div class="stat-val"><?= number_format($stats['orders_today']) ?></div>
        <div class="stat-lbl">Pesanan Hari Ini</div>
      </div>
    </div>
    <div class="stat-card highlight-warn">
      <div class="stat-ic">⏳</div>
      <div class="stat-body">
        <div class="stat-val"><?= number_format($stats['orders_pending']) ?></div>
        <div class="stat-lbl">Pesanan Pending</div>
      </div>
    </div>
    <div class="stat-card highlight-gold">
      <div class="stat-ic">💰</div>
      <div class="stat-body">
        <div class="stat-val"><?= formatRupiah($stats['revenue_total']) ?></div>
        <div class="stat-lbl">Total Pendapatan</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-ic">📅</div>
      <div class="stat-body">
        <div class="stat-val"><?= formatRupiah($stats['revenue_today']) ?></div>
        <div class="stat-lbl">Pendapatan Hari Ini</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-ic">👥</div>
      <div class="stat-body">
        <div class="stat-val"><?= number_format($stats['users_total']) ?></div>
        <div class="stat-lbl">Total User</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-ic">🎮</div>
      <div class="stat-body">
        <div class="stat-val"><?= number_format($stats['products_total']) ?></div>
        <div class="stat-lbl">Produk Aktif</div>
      </div>
    </div>
  </div>

  <!-- ── Recent Orders ── -->
  <div class="admin-section">
    <div class="section-head">
      <h3 class="section-title">📋 Pesanan Terbaru</h3>
      <a href="<?= BASE_PATH ?>/admin/orders.php" class="see-all">Lihat Semua →</a>
    </div>
    <div class="table-wrap">
      <table class="data-table">
        <thead>
          <tr>
            <th>Kode</th><th>User</th><th>Produk</th><th>ML ID/Zone</th>
            <th>Total</th><th>Bayar</th><th>Status</th><th>Tanggal</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($recent as $o): ?>
          <tr>
            <td><span class="mono"><?= sanitize($o['order_code']) ?></span></td>
            <td><?= isset($userMap[$o['user_id']]) ? sanitize($userMap[$o['user_id']]) : '<em class="txt-dim">Guest</em>' ?></td>
            <td><?= sanitize($o['product_name']) ?></td>
            <td><span class="mono"><?= sanitize($o['ml_user_id']) ?>/<?= sanitize($o['ml_server_id']) ?></span></td>
            <td class="price-cell"><?= formatRupiah($o['total_price']) ?></td>
            <td><?= strtoupper($o['payment_method']) ?></td>
            <td>
              <form method="POST" action="<?= BASE_PATH ?>/admin/orders.php">
                <input type="hidden" name="action"   value="update_status">
                <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                <select name="status" onchange="this.form.submit()"
                        class="st-select st-<?= $o['order_status'] ?>">
                  <option value="pending"    <?= $o['order_status']==='pending'    ?'selected':'' ?>>Menunggu</option>
                  <option value="processing" <?= $o['order_status']==='processing' ?'selected':'' ?>>Diproses</option>
                  <option value="completed"  <?= $o['order_status']==='completed'  ?'selected':'' ?>>Selesai</option>
                  <option value="failed"     <?= $o['order_status']==='failed'     ?'selected':'' ?>>Gagal</option>
                </select>
              </form>
            </td>
            <td class="date-cell"><?= date('d/m/y H:i', strtotime($o['created_at'])) ?></td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($recent)): ?>
            <tr><td colspan="8" class="empty-cell">Belum ada pesanan.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>