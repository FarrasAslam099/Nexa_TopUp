<?php
// ============================================
// admin/products.php — Kelola Produk
// ============================================
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
requireAdmin();

$errors  = [];
$success = '';

// Handle POST: tambah / edit / hapus produk
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add' || $action === 'edit') {
        $catId         = (int)($_POST['category_id'] ?? 0);
        $name          = trim($_POST['name'] ?? '');
        $description   = trim($_POST['description'] ?? '');
        $diamondAmount = (int)($_POST['diamond_amount'] ?? 0);
        $price         = (float)($_POST['price'] ?? 0);
        $originalPrice = !empty($_POST['original_price']) ? (float)$_POST['original_price'] : null;
        $badge         = trim($_POST['badge'] ?? '');
        $isActive      = isset($_POST['is_active']) ? 1 : 0;

        if (!$catId || empty($name) || $price <= 0) {
            $errors[] = 'Kategori, nama, dan harga wajib diisi.';
        } else {
            if ($action === 'add') {
                $stmt = $pdo->prepare("INSERT INTO products (category_id, name, description, diamond_amount, price, original_price, badge, is_active) VALUES (?,?,?,?,?,?,?,?)");
                $stmt->execute([$catId, $name, $description, $diamondAmount, $price, $originalPrice, $badge ?: null, $isActive]);
                $success = 'Produk berhasil ditambahkan.';
            } else {
                $editId = (int)$_POST['edit_id'];
                $stmt = $pdo->prepare("UPDATE products SET category_id=?, name=?, description=?, diamond_amount=?, price=?, original_price=?, badge=?, is_active=? WHERE id=?");
                $stmt->execute([$catId, $name, $description, $diamondAmount, $price, $originalPrice, $badge ?: null, $isActive, $editId]);
                $success = 'Produk berhasil diperbarui.';
            }
        }
    } elseif ($action === 'delete') {
        $delId = (int)$_POST['delete_id'];
        $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$delId]);
        $success = 'Produk berhasil dihapus.';
    } elseif ($action === 'toggle') {
        $togId = (int)$_POST['toggle_id'];
        $pdo->prepare("UPDATE products SET is_active = 1 - is_active WHERE id = ?")->execute([$togId]);
    }
}

// Ambil semua produk
$products   = $pdo->query("SELECT p.*, c.name AS cat_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY c.sort_order, p.sort_order")->fetchAll();
$categories = $pdo->query("SELECT * FROM categories ORDER BY sort_order")->fetchAll();

$pageTitle = 'Kelola Produk – Admin';
require_once __DIR__ . '/../includes/header.php';
?>
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="container admin-page">
  <div class="admin-header">
    <h2>📦 Kelola Produk</h2>
    <div class="admin-nav">
      <a href="/admin/dashboard.php" class="admin-link">Dashboard</a>
      <a href="/admin/products.php" class="admin-link active">Produk</a>
      <a href="/admin/orders.php" class="admin-link">Pesanan</a>
    </div>
  </div>

  <?php if ($success): ?><div class="alert alert-success">✅ <?= htmlspecialchars($success) ?></div><?php endif; ?>
  <?php foreach ($errors as $e): ?><div class="alert alert-error">⚠️ <?= htmlspecialchars($e) ?></div><?php endforeach; ?>

  <!-- Form Tambah Produk -->
  <div class="admin-form-card">
    <h4>➕ Tambah Produk Baru</h4>
    <form method="POST" action="">
      <input type="hidden" name="action" value="add">
      <div class="form-row">
        <div class="form-group">
          <label>Kategori</label>
          <select name="category_id" required>
            <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>"><?= $cat['icon'] ?> <?= htmlspecialchars($cat['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Nama Produk</label>
          <input type="text" name="name" placeholder="Contoh: 86 Diamonds" required>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Harga (Rp)</label>
          <input type="number" name="price" min="100" step="100" placeholder="15000" required>
        </div>
        <div class="form-group">
          <label>Harga Asli (coret, opsional)</label>
          <input type="number" name="original_price" min="0" step="100" placeholder="20000">
        </div>
        <div class="form-group">
          <label>Jumlah Diamond</label>
          <input type="number" name="diamond_amount" min="0" placeholder="0 jika bukan diamond">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Badge (opsional)</label>
          <select name="badge">
            <option value="">— Tidak ada —</option>
            <option value="HOT">HOT</option>
            <option value="NEW">NEW</option>
            <option value="SALE">SALE</option>
            <option value="POPULER">POPULER</option>
            <option value="BEST VALUE">BEST VALUE</option>
            <option value="PROMO">PROMO</option>
          </select>
        </div>
        <div class="form-group">
          <label>Deskripsi</label>
          <input type="text" name="description" placeholder="Deskripsi singkat produk">
        </div>
        <div class="form-group check-group">
          <label><input type="checkbox" name="is_active" checked> Aktif</label>
        </div>
      </div>
      <button type="submit" class="btn-primary">➕ Tambah Produk</button>
    </form>
  </div>

  <!-- Tabel Produk -->
  <div class="history-table-wrap">
    <table class="history-table">
      <thead>
        <tr>
          <th>ID</th><th>Kategori</th><th>Nama</th><th>Diamond</th>
          <th>Harga</th><th>Badge</th><th>Status</th><th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($products as $p): ?>
        <tr class="<?= !$p['is_active'] ? 'row-inactive' : '' ?>">
          <td><?= $p['id'] ?></td>
          <td><?= htmlspecialchars($p['cat_name']) ?></td>
          <td><?= htmlspecialchars($p['name']) ?></td>
          <td><?= $p['diamond_amount'] > 0 ? '💎 '.number_format($p['diamond_amount']) : '—' ?></td>
          <td>
            Rp <?= number_format($p['price'], 0, ',', '.') ?>
            <?php if ($p['original_price']): ?>
              <br><small class="price-old">Rp <?= number_format($p['original_price'], 0, ',', '.') ?></small>
            <?php endif; ?>
          </td>
          <td><?= $p['badge'] ? '<span class="product-badge badge-'.strtolower($p['badge']).'">'.$p['badge'].'</span>' : '—' ?></td>
          <td>
            <form method="POST" style="display:inline">
              <input type="hidden" name="action" value="toggle">
              <input type="hidden" name="toggle_id" value="<?= $p['id'] ?>">
              <button type="submit" class="btn-toggle"><?= $p['is_active'] ? '✅ Aktif' : '❌ Nonaktif' ?></button>
            </form>
          </td>
          <td>
            <form method="POST" style="display:inline">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="delete_id" value="<?= $p['id'] ?>">
              <button type="submit" class="btn-delete" onclick="return confirm('Hapus produk ini?')">🗑️</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
