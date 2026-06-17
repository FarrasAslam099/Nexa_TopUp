<?php
// ============================================================
// admin/dashboard.php — Dashboard Admin Nexa_Topup
// ============================================================
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

// ── Recent Orders ──
$recent = $pdo->query("
    SELECT o.*, u.username
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC LIMIT 15
")->fetchAll();

$flash     = getFlash();
$pageTitle = 'Dashboard Admin — Nexa_Topup';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<style>
/* ============================================================
   STYLE KHUSUS HALAMAN: admin/dashboard.php
   ============================================================ */
.admin-main { padding: 28px 0 60px; }
.admin-head {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 24px; flex-wrap: wrap; gap: 12px;
}
.admin-head h2 { font-size: 22px; font-weight: 700; }
.admin-sub { font-size: 13px; color: var(--text3); }
.admin-nav-pills { display: flex; gap: 8px; flex-wrap: wrap; }
.pill {
  display: inline-block; padding: 8px 16px;
  background: var(--bg2); border: 1px solid var(--border);
  border-radius: var(--radius); font-size: 13px; font-weight: 700;
  color: var(--text2); text-decoration: none; transition: all .2s;
}
.pill:hover { border-color: var(--cyan); color: var(--cyan); text-decoration: none; }
.pill.active { background: rgba(0,212,255,.1); border-color: var(--cyan); color: var(--cyan); }

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(155px, 1fr));
  gap: 14px; margin-bottom: 28px;
}
.stat-card {
  background: var(--bg2); border: 1px solid var(--border);
  border-radius: var(--radius); padding: 18px 16px;
  display: flex; align-items: center; gap: 14px;
}
.stat-card.highlight-warn { border-color: rgba(245,166,35,.35); }
.stat-card.highlight-gold { border-color: rgba(34,197,94,.35); }
.stat-ic { font-size: 26px; flex-shrink: 0; }
.stat-val { font-size: 20px; font-weight: 900; line-height: 1.1; }
.stat-lbl { font-size: 11px; color: var(--text3); margin-top: 2px; }

.admin-section { margin-top: 28px; }

/* Status select (dropdown ubah status pesanan) */
.st-select {
  background: var(--bg3); border: 1px solid var(--border);
  border-radius: 6px; color: var(--text); font-size: 12px; font-weight: 700;
  padding: 4px 8px; cursor: pointer; font-family: inherit;
}
.st-select.st-pending    { border-color: rgba(245,166,35,.5); color: var(--gold); }
.st-select.st-processing { border-color: rgba(0,212,255,.5); color: var(--cyan); }
.st-select.st-completed  { border-color: rgba(34,197,94,.5); color: var(--green); }
.st-select.st-failed     { border-color: rgba(239,68,68,.5); color: var(--red); }
</style>

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
            <td><?= $o['username'] ? sanitize($o['username']) : '<em class="txt-dim">Guest</em>' ?></td>
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