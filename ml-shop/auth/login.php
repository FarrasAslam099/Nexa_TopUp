<?php
// ============================================
// auth/login.php — Halaman Login
// ============================================
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';

if (isLoggedIn()) {
    header('Location: /pages/index.php');
    exit;
}

$error = '';
$redirect = $_GET['redirect'] ?? '/pages/index.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? '');
    $password   = $_POST['password'] ?? '';

    if (empty($identifier) || empty($password)) {
        $error = 'Email/username dan password wajib diisi.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR username = ? LIMIT 1");
        $stmt->execute([$identifier, $identifier]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email']    = $user['email'];
            $_SESSION['role']     = $user['role'];
            setFlash('success', 'Selamat datang, ' . $user['username'] . '!');
            header('Location: ' . $redirect);
            exit;
        } else {
            $error = 'Email/username atau password salah.';
        }
    }
}

$pageTitle = 'Masuk – ML Shop';
require_once __DIR__ . '/../includes/header.php';
?>
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="auth-page">
  <div class="auth-card">
    <div class="auth-logo">⚡</div>
    <h2>Masuk ke Akun</h2>
    <p class="auth-sub">Silakan login untuk melanjutkan</p>

    <?php if ($error): ?>
      <div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">

      <div class="form-group">
        <label>Email atau Username</label>
        <input type="text" name="identifier" placeholder="Masukkan email atau username"
               value="<?= htmlspecialchars($_POST['identifier'] ?? '') ?>" required>
      </div>

      <div class="form-group">
        <label>Password</label>
        <div class="input-pass">
          <input type="password" name="password" id="passInput" placeholder="Masukkan password" required>
          <span class="toggle-pass" onclick="togglePass()">👁️</span>
        </div>
      </div>

      <button type="submit" class="btn-primary btn-block">⚡ Masuk</button>
    </form>

    <p class="auth-switch">Belum punya akun? <a href="/auth/register.php">Daftar sekarang</a></p>
  </div>
</main>

<script>
function togglePass() {
    const inp = document.getElementById('passInput');
    inp.type = inp.type === 'password' ? 'text' : 'password';
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
