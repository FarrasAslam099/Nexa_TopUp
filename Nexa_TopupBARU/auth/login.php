<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';

if (isLoggedIn()) {
    header('Location: ' . BASE_PATH . '/pages/index.php');
    exit;
}

$error    = '';
$redirect = $_GET['redirect'] ?? BASE_PATH . '/pages/index.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? '');
    $password   = $_POST['password'] ?? '';

    if (empty($identifier) || empty($password)) {
        $error = 'Email/username dan password wajib diisi.';
    } else {
        $stmt = $pdo->prepare(
            'SELECT * FROM users WHERE email = ? OR username = ? LIMIT 1'
        );
        $stmt->execute([$identifier, $identifier]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email']    = $user['email'];
            $_SESSION['role']     = $user['role'];
            setFlash('success', 'Selamat datang kembali, ' . $user['username'] . '! ⚡');
            header('Location: ' . $redirect);
            exit;
        } else {
            $error = 'Email/username atau password salah.';
        }
    }
}

$pageTitle = 'Masuk — Nexa_Topup';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>


<main class="auth-wrap">
  <div class="auth-card">

    <div class="auth-head">
      <div class="auth-logo">⚡</div>
      <h1>Masuk ke Akun</h1>
      <p>Login untuk menyimpan riwayat transaksi kamu</p>
    </div>

    <?php if ($error): ?>
      <div class="alert alert-error">⚠️ <?= sanitize($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="" autocomplete="on" novalidate>
      <input type="hidden" name="redirect" value="<?= sanitize($redirect) ?>">

      <div class="form-group">
        <label for="identifier">Email atau Username</label>
        <input
          type="text" id="identifier" name="identifier"
          placeholder="contoh@email.com atau username"
          value="<?= sanitize($_POST['identifier'] ?? '') ?>"
          autocomplete="username" required>
      </div>

      <div class="form-group">
        <label for="passInput">Password</label>
        <div class="input-pw-wrap">
          <input
            type="password" id="passInput" name="password"
            placeholder="Masukkan password"
            autocomplete="current-password" required>
          <button type="button" class="btn-toggle-pw" onclick="togglePw()" aria-label="Tampilkan password">
            <span id="pwIcon"></span>
          </button>
        </div>
      </div>

      <button type="submit" class="btn-primary btn-block">⚡ Masuk Sekarang</button>
    </form>

    <div class="auth-footer">
      Belum punya akun? <a href="<?= BASE_PATH ?>/auth/register.php">Daftar gratis</a>
    </div>
  </div>
</main>


<?php require_once __DIR__ . '/../includes/footer.php'; ?>