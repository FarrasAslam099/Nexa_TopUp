<?php
// ============================================================
// admin/products.php — Kelola Produk
// ============================================================
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
requireAdmin();

// ── Handle Actions ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $catId   = (int)($_POST['category_id'] ?? 0);
        $name    = trim($_POST['name'] ?? '');
        $desc    = trim($_POST['description'] ?? '');
        $diamond = (int)($_POST['diamond_amount'] ?? 0);
        $price   = (float)($_POST['price'] ?? 0);
        $origP   = ($_POST['original_price'] !== '' && $_POST['original_price'] !== null)
                   ? (float)$_POST['original_price'] : null;
        $badge   = trim($_POST['badge'] ?? '') ?: null;
        $sort    = (int)($_POST['sort_order'] ?? 0);

        if ($catId && $name && $price > 0) {
            $ins = $pdo->prepare("
                INSERT INTO products (category_id, name, description, diamond_amount, price, original_price, badge, sort_order)
                VALUES (?,?,?,?,?,?,?,?)
            ");
            $ins->execute([$catId, $name, $desc, $diamond, $price, $origP, $badge, $sort]);
            setFlash('success', 'Produk berhasil ditambahkan.');
        } else {
            setFlash('error', 'Isi kategori, nama, dan harga.');
        }
    }

    if ($action === 'toggle') {
        $id = (int)($_POST['product_id'] ?? 0);
        $pdo->prepare('UPDATE products SET is_active = 1 - is_active WHERE id = ?')->execute([$id]);
        setFlash('success', 'Status produk diperbarui.');
    }

    if ($action === 'delete') {
        $id = (int)($_POST['product_id'] ?? 0);
        $pdo->prepare('DELETE FROM products WHERE id = ?')->execute([$id]);
        setFlash('success', 'Produk dihapus.');
    }

    header('Location: ' . BASE_PATH . '/admin/products.php');
    exit;
}

// Ambil data
$categories = $pdo->query('SELECT * FROM categories ORDER BY sort_order')->fetchAll();
$products   = $pdo->query("
    SELECT p.*, c.name AS cat_name
    FROM products p JOIN categories c ON p.category_id = c.id
    ORDER BY c.sort_order, p.sort_order, p.price
")->fetchAll();

$flash     = getFlash();
$pageTitle = 'Kelola Produk — Admin Nexa_Topup';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<style>
/* ============================================================
   STYLE KHUSUS HALAMAN: admin/products.php
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

/* Form tambah produk */
.admin-form-wrap {
  background: var(--bg2); border: 1px solid var(--border);
  border-radius: var(--radius-lg); padding: 24px; margin-bottom: 24px;
}
.admin-form-wrap h3 { font-size: 16px; font-weight: 700; margin-bottom: 18px; }
.form-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.form-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px; }
.form-actions { display: flex; gap: 10px; margin-top: 8px; }

.btn-danger {
  display: inline-block; padding: 8px 16px;
  background: rgba(239,68,68,.15); border: 1px solid rgba(239,68,68,.35);
  color: #f87171; font-size: 13px; font-weight: 700;
  border-radius: var(--radius); cursor: pointer; text-decoration: none;
  transition: all .2s; font-family: inherit;
}
.btn-danger:hover { background: rgba(239,68,68,.3); text-decoration: none; color: #f87171; }

.btn-secondary {
  display: inline-block; padding: 8px 16px;
  background: var(--bg3); border: 1px solid var(--border);
  color: var(--text2); font-size: 13px; font-weight: 700;
  border-radius: var(--radius); cursor: pointer; text-decoration: none;
  transition: all .2s; font-family: inherit;
}
.btn-secondary:hover { border-color: var(--cyan); color: var(--cyan); text-decoration: none; }

/* Status select (badge aktif/nonaktif menggunakan .st-badge dari style global) */
</style>

<main class="container admin-main">

  <div class="admin-head">
    <div>
      <h2>📦 Kelola Produk</h2>
      <span class="admin-sub">Tambah, aktifkan/nonaktifkan, atau hapus produk</span>
    </div>
    <div class="admin-nav-pills">
      <a href="<?= BASE_PATH ?>/admin/dashboard.php" class="pill">📊 Dashboard</a>
      <a href="<?= BASE_PATH ?>/admin/products.php"  class="pill active">📦 Produk</a>
      <a href="<?= BASE_PATH ?>/admin/orders.php"    class="pill">📋 Pesanan</a>
    </div>
  </div>

  <?php if ($flash): ?>
    <div class="flash flash-<?= $flash['type'] ?>"><?= sanitize($flash['msg']) ?></div>
  <?php endif; ?>

  <!-- Add Product Form -->
  <div class="admin-form-wrap">
    <h3>➕ Tambah Produk Baru</h3>
    <form method="POST" action="">
      <input type="hidden" name="action" value="add">
      <div class="form-row-2">
        <div class="form-group">
          <label>Kategori <span class="req">*</span></label>
          <select name="category_id" required>
            <option value="">— Pilih kategori —</option>
            <?php foreach ($categories as $c): ?>
            <option value="<?= $c['id'] ?>"><?= $c['icon'] ?> <?= sanitize($c['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Nama Produk <span class="req">*</span></label>
          <input type="text" name="name" placeholder="Contoh: 60 Diamonds" required>
        </div>
      </div>
      <div class="form-row-3">
        <div class="form-group">
          <label>Harga (Rp) <span class="req">*</span></label>
          <input type="number" name="price" placeholder="9500" min="0" step="1" required>
        </div>
        <div class="form-group">
          <label>Harga Asli (opsional)</label>
          <input type="number" name="original_price" placeholder="10000" min="0" step="1">
        </div>
        <div class="form-group">
          <label>Jumlah Diamond</label>
          <input type="number" name="diamond_amount" placeholder="60" min="0" step="1" value="0">
        </div>
      </div>
      <div class="form-row-2">
        <div class="form-group">
          <label>Badge (opsional)</label>
          <select name="badge">
            <option value="">— Tidak ada —</option>
            <option value="HOT">🔥 HOT</option>
            <option value="POPULER">⭐ POPULER</option>
            <option value="BEST VALUE">✅ BEST VALUE</option>
            <option value="SALE">💥 SALE</option>
            <option value="PROMO">🎉 PROMO</option>
            <option value="NEW">🆕 NEW</option>
          </select>
        </div>
        <div class="form-group">
          <label>Urutan Sort</label>
          <input type="number" name="sort_order" value="0" min="0">
        </div>
      </div>
      <div class="form-group">
        <label>Deskripsi</label>
        <input type="text" name="description" placeholder="Deskripsi singkat produk">
      </div>
      <div class="form-actions">
        <button type="submit" class="btn-primary">➕ Tambah Produk</button>
      </div>
    </form>
  </div>

  <!-- Product Table -->
  <div class="section-head">
    <h3 class="section-title">Daftar Produk (<?= count($products) ?>)</h3>
  </div>
  <div class="table-wrap">
    <table class="data-table">
      <thead>
        <tr>
          <th>ID</th><th>Kategori</th><th>Nama Produk</th>
          <th>Diamond</th><th>Harga</th><th>Harga Asli</th>
          <th>Badge</th><th>Status</th><th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($products as $p): ?>
        <tr>
          <td class="mono">#<?= $p['id'] ?></td>
          <td><?= sanitize($p['cat_name']) ?></td>
          <td><strong><?= sanitize($p['name']) ?></strong></td>
          <td><?= $p['diamond_amount'] > 0 ? '💎 ' . number_format($p['diamond_amount']) : '—' ?></td>
          <td class="price-cell"><?= formatRupiah($p['price']) ?></td>
          <td><?= $p['original_price'] ? formatRupiah($p['original_price']) : '—' ?></td>
          <td><?= $p['badge'] ? sanitize($p['badge']) : '—' ?></td>
          <td>
            <span class="st-badge <?= $p['is_active'] ? 'st-done' : 'st-failed' ?>">
              <?= $p['is_active'] ? 'Aktif' : 'Nonaktif' ?>
            </span>
          </td>
          <td>
            <div style="display:flex;gap:6px;flex-wrap:wrap">
              <form method="POST" style="display:inline">
                <input type="hidden" name="action"     value="toggle">
                <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                <button type="submit" class="btn-secondary" style="padding:5px 10px;font-size:12px">
                  <?= $p['is_active'] ? '🔕 Nonaktif' : '✅ Aktifkan' ?>
                </button>
              </form>
              <form method="POST" style="display:inline"
                    onsubmit="return confirm('Hapus produk ini? Tidak bisa dikembalikan.')">
                <input type="hidden" name="action"     value="delete">
                <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                <button type="submit" class="btn-danger" style="padding:5px 10px;font-size:12px">🗑️ Hapus</button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($products)): ?>
          <tr><td colspan="9" class="empty-cell">Belum ada produk.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
