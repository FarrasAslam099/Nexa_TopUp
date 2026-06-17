<?php
// ============================================================
// auth/login.php
// ============================================================
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

<style>
/* ============================================================
   STYLE KHUSUS HALAMAN: auth/login.php
   ============================================================ */
.auth-wrap {
  min-height: calc(100vh - 60px);
  display: flex; align-items: center; justify-content: center;
  padding: 40px 20px;
}
.auth-card {
  background: var(--bg2); border: 1px solid var(--border);
  border-radius: var(--radius-lg); padding: 36px;
  width: 100%; max-width: 420px;
}
.auth-head { text-align: center; margin-bottom: 28px; }
.auth-logo { font-size: 40px; margin-bottom: 12px; }
.auth-head h1 { font-size: 22px; font-weight: 800; margin-bottom: 6px; }
.auth-head p { color: var(--text2); font-size: 14px; }
.auth-footer { text-align: center; margin-top: 20px; font-size: 14px; color: var(--text2); }

.alert { padding: 12px 16px; border-radius: var(--radius); margin-bottom: 16px; font-size: 13px; }
.alert-error { background: rgba(239,68,68,.1); border: 1px solid rgba(239,68,68,.3); color: #f87171; }
.alert-success { background: rgba(34,197,94,.1); border: 1px solid rgba(34,197,94,.3); color: #4ade80; }

.input-pw-wrap { position: relative; }
.input-pw-wrap input { padding-right: 44px; }
.btn-toggle-pw {
  position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
  background: none; border: none; cursor: pointer; font-size: 16px; padding: 4px;
}
</style>

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
            <span id="pwIcon">👁️</span>
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

<script>
function togglePw() {
  const inp  = document.getElementById('passInput');
  const icon = document.getElementById('pwIcon');
  if (inp.type === 'password') { inp.type = 'text'; icon.textContent = '🙈'; }
  else                         { inp.type = 'password'; icon.textContent = '👁️'; }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>