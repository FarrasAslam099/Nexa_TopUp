<?php

// admin/orders.php — Kelola Pesanan
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
requireAdmin();

// ── Handle Actions ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_status') {
        $id     = (int)($_POST['order_id'] ?? 0);
        $status = $_POST['status'] ?? '';
        $valid  = ['pending','processing','completed','failed'];
        if ($id && in_array($status, $valid)) {
            $pdo->prepare('UPDATE orders SET order_status = ? WHERE id = ?')->execute([$status, $id]);
            setFlash('success', 'Status pesanan diperbarui.');
        }
    }

    if ($action === 'delete') {
        $id = (int)($_POST['order_id'] ?? 0);
        if ($id) {
            $pdo->prepare('DELETE FROM orders WHERE id = ?')->execute([$id]);
            setFlash('success', 'Pesanan dihapus.');
        }
    }

    header('Location: ' . BASE_PATH . '/admin/orders.php' . ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : ''));
    exit;
}

// ── Filters ──
$search  = trim($_GET['search']  ?? '');
$status  = trim($_GET['status']  ?? '');
$page    = max(1, (int)($_GET['page']   ?? 1));
$perPage = 20;
$offset  = ($page - 1) * $perPage;

// Filter hanya pada kolom tabel orders (tanpa JOIN)
$where  = '1=1';
$params = [];
if ($search) {
    $where .= ' AND (order_code LIKE ? OR ml_user_id LIKE ? OR product_name LIKE ?)';
    $s = "%$search%";
    $params = array_merge($params, [$s, $s, $s]);
}
if ($status) {
    $where .= ' AND order_status = ?';
    $params[] = $status;
}

$stmtTotal = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE $where");
$stmtTotal->execute($params);
$totalRows  = (int)$stmtTotal->fetchColumn();
$totalPages = (int)ceil($totalRows / $perPage);

$stmt = $pdo->prepare("
    SELECT order_code, user_id, product_name, ml_user_id, ml_server_id,
           total_price, payment_method, order_status, created_at, id
    FROM orders
    WHERE $where
    ORDER BY created_at DESC
    LIMIT ? OFFSET ?
");
$stmt->execute(array_merge($params, [$perPage, $offset]));
$orders = $stmt->fetchAll();

// Ambil username dari tabel users berdasarkan user_id yang ada
$userIds = array_filter(array_unique(array_column($orders, 'user_id')));
$userMap = [];
if ($userIds) {
    $in   = implode(',', array_map('intval', $userIds));
    $rows = $pdo->query("SELECT id, username FROM users WHERE id IN ($in)")->fetchAll();
    foreach ($rows as $u) { $userMap[$u['id']] = $u['username']; }
}

$flash     = getFlash();
$pageTitle = 'Kelola Pesanan — Admin Nexa_Topup';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>


<main class="container admin-main">

  <div class="admin-head">
    <div>
      <h2>📋 Kelola Pesanan</h2>
      <span class="admin-sub">Total <?= $totalRows ?> pesanan ditemukan</span>
    </div>
    <div class="admin-nav-pills">
      <a href="<?= BASE_PATH ?>/admin/dashboard.php" class="pill">📊 Dashboard</a>
      <a href="<?= BASE_PATH ?>/admin/products.php"  class="pill">📦 Produk</a>
      <a href="<?= BASE_PATH ?>/admin/orders.php"    class="pill active">📋 Pesanan</a>
    </div>
  </div>

  <?php if ($flash): ?>
    <div class="flash flash-<?= $flash['type'] ?>"><?= sanitize($flash['msg']) ?></div>
  <?php endif; ?>

  <!-- Filters -->
  <form method="GET" action="">
    <div class="admin-filters">
      <div class="form-group">
        <label>Cari</label>
        <input type="text" name="search" value="<?= sanitize($search) ?>"
               placeholder="Kode, ML ID, produk, user...">
      </div>
      <div class="form-group">
        <label>Status</label>
        <select name="status">
          <option value="">Semua</option>
          <option value="pending"    <?= $status==='pending'    ?'selected':'' ?>>Menunggu</option>
          <option value="processing" <?= $status==='processing' ?'selected':'' ?>>Diproses</option>
          <option value="completed"  <?= $status==='completed'  ?'selected':'' ?>>Selesai</option>
          <option value="failed"     <?= $status==='failed'     ?'selected':'' ?>>Gagal</option>
        </select>
      </div>
      <button type="submit" class="btn-primary" style="padding:11px 20px">🔍 Filter</button>
      <a href="<?= BASE_PATH ?>/admin/orders.php" class="btn-secondary" style="padding:11px 20px">↺ Reset</a>
    </div>
  </form>

  <!-- Table -->
  <div class="table-wrap">
    <table class="data-table">
      <thead>
        <tr>
          <th>Kode</th><th>User</th><th>Produk</th><th>ML ID / Zone</th>
          <th>Total</th><th>Pembayaran</th><th>Status</th><th>Tanggal</th><th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orders as $o): ?>
        <tr>
          <td><span class="mono"><?= sanitize($o['order_code']) ?></span></td>
          <td><?= isset($userMap[$o['user_id']]) ? sanitize($userMap[$o['user_id']]) : '<em class="txt-dim">Guest</em>' ?></td>
          <td><?= sanitize($o['product_name']) ?></td>
          <td>
            <span class="mono"><?= sanitize($o['ml_user_id']) ?></span>
            <span class="cell-sep">/</span>
            <span class="mono"><?= sanitize($o['ml_server_id']) ?></span>
          </td>
          <td class="price-cell"><?= formatRupiah($o['total_price']) ?></td>
          <td><?= strtoupper(sanitize($o['payment_method'])) ?></td>
          <td>
            <form method="POST">
              <input type="hidden" name="action"   value="update_status">
              <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
              <select name="status" class="st-select st-<?= $o['order_status'] ?>"
                      onchange="this.form.submit()">
                <option value="pending"    <?= $o['order_status']==='pending'    ?'selected':'' ?>>Menunggu</option>
                <option value="processing" <?= $o['order_status']==='processing' ?'selected':'' ?>>Diproses</option>
                <option value="completed"  <?= $o['order_status']==='completed'  ?'selected':'' ?>>Selesai</option>
                <option value="failed"     <?= $o['order_status']==='failed'     ?'selected':'' ?>>Gagal</option>
              </select>
            </form>
          </td>
          <td class="date-cell">
            <?= date('d/m/y', strtotime($o['created_at'])) ?>
            <div class="cell-sub"><?= date('H:i', strtotime($o['created_at'])) ?></div>
          </td>
          <td>
            <form method="POST" onsubmit="return confirm('Hapus pesanan ini?')">
              <input type="hidden" name="action"   value="delete">
              <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
              <button type="submit" class="btn-danger" style="padding:5px 10px;font-size:12px">🗑️</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($orders)): ?>
          <tr><td colspan="9" class="empty-cell">Tidak ada pesanan.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <?php if ($totalPages > 1): ?>
  <div class="pagination">
    <?php if ($page > 1): ?>
      <a href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>" class="pg-btn">← Prev</a>
    <?php endif; ?>
    <?php for ($i = max(1,$page-2); $i <= min($totalPages,$page+2); $i++): ?>
      <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>"
         class="pg-btn <?= $i===$page?'active':'' ?>"><?= $i ?></a>
    <?php endfor; ?>
    <?php if ($page < $totalPages): ?>
      <a href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>" class="pg-btn">Next →</a>
    <?php endif; ?>
  </div>
  <?php endif; ?>

</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
