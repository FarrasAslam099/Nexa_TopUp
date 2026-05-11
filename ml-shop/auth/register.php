<?php
// ============================================
// auth/register.php — Halaman Registrasi
// ============================================
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';

if (isLoggedIn()) {
    header('Location: /pages/index.php');
    exit;
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    // Validasi
    if (strlen($username) < 3) $errors[] = 'Username minimal 3 karakter.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Format email tidak valid.';
    if (strlen($password) < 6) $errors[] = 'Password minimal 6 karakter.';
    if ($password !== $confirm) $errors[] = 'Konfirmasi password tidak cocok.';

    if (empty($errors)) {
        // Cek duplikasi
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $errors[] = 'Username atau email sudah terdaftar.';
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, phone, password) VALUES (?, ?, ?, ?)");
            $stmt->execute([$username, $email, $phone, $hash]);

            setFlash('success', 'Registrasi berhasil! Silakan login.');
            header('Location: /auth/login.php');
            exit;
        }
    }
}

$pageTitle = 'Daftar – ML Shop';
require_once __DIR__ . '/../includes/header.php';
?>
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="auth-page">
  <div class="auth-card">
    <div class="auth-logo">⚡</div>
    <h2>Buat Akun Baru</h2>
    <p class="auth-sub">Daftar gratis dan mulai top up sekarang</p>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-error">
        <?php foreach ($errors as $e): ?>
          <div>⚠️ <?= htmlspecialchars($e) ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" placeholder="Pilih username unik"
               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
      </div>

      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" placeholder="Masukkan email aktif"
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
      </div>

      <div class="form-group">
        <label>No. HP (opsional)</label>
        <input type="tel" name="phone" placeholder="08xxxxxxxxxx"
               value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" placeholder="Min. 6 karakter" required>
      </div>

      <div class="form-group">
        <label>Konfirmasi Password</label>
        <input type="password" name="confirm_password" placeholder="Ulangi password" required>
      </div>

      <button type="submit" class="btn-primary btn-block">🚀 Daftar Sekarang</button>
    </form>

    <p class="auth-switch">Sudah punya akun? <a href="/auth/login.php">Masuk di sini</a></p>
  </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
