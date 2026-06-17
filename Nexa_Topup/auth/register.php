<?php
// ============================================================
// auth/register.php
// ============================================================
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';

if (isLoggedIn()) {
    header('Location: ' . BASE_PATH . '/pages/index.php');
    exit;
}

$errors = [];
$old    = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old      = $_POST;
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email']    ?? '');
    $phone    = trim($_POST['phone']    ?? '');
    $password = $_POST['password']         ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    // Validasi
    if (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username))
        $errors[] = 'Username 3–30 karakter, hanya huruf, angka, dan underscore.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors[] = 'Format email tidak valid.';
    if (strlen($password) < 6)
        $errors[] = 'Password minimal 6 karakter.';
    if ($password !== $confirm)
        $errors[] = 'Konfirmasi password tidak cocok.';

    if (empty($errors)) {
        $chk = $pdo->prepare('SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1');
        $chk->execute([$username, $email]);
        if ($chk->fetch()) {
            $errors[] = 'Username atau email sudah terdaftar.';
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $ins  = $pdo->prepare(
                'INSERT INTO users (username, email, phone, password) VALUES (?,?,?,?)'
            );
            $ins->execute([$username, $email, $phone ?: null, $hash]);
            setFlash('success', 'Registrasi berhasil! Silakan login. ⚡');
            header('Location: ' . BASE_PATH . '/auth/login.php');
            exit;
        }
    }
}

$pageTitle = 'Daftar — Nexa_Topup';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<style>
/* ============================================================
   STYLE KHUSUS HALAMAN: auth/register.php
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
      <h1>Buat Akun Baru</h1>
      <p>Gratis, daftar sekarang dan mulai top up!</p>
    </div>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-error">
        <?php foreach ($errors as $e): ?>
          <div>⚠️ <?= sanitize($e) ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="" autocomplete="on" novalidate>

      <div class="form-group">
        <label for="username">Username <span class="req">*</span></label>
        <input type="text" id="username" name="username"
               placeholder="Pilih username unik"
               value="<?= sanitize($old['username'] ?? '') ?>"
               autocomplete="username" required>
        <small>3–30 karakter, huruf/angka/underscore saja</small>
      </div>

      <div class="form-group">
        <label for="email">Email <span class="req">*</span></label>
        <input type="email" id="email" name="email"
               placeholder="email@contoh.com"
               value="<?= sanitize($old['email'] ?? '') ?>"
               autocomplete="email" required>
      </div>

      <div class="form-group">
        <label for="phone">No. HP <span class="opt">(opsional)</span></label>
        <input type="tel" id="phone" name="phone"
               placeholder="08xxxxxxxxxx"
               value="<?= sanitize($old['phone'] ?? '') ?>"
               autocomplete="tel">
      </div>

      <div class="form-group">
        <label for="passReg">Password <span class="req">*</span></label>
        <div class="input-pw-wrap">
          <input type="password" id="passReg" name="password"
                 placeholder="Min. 6 karakter"
                 autocomplete="new-password" required>
          <button type="button" class="btn-toggle-pw" onclick="togglePw('passReg','icn1')">
            <span id="icn1">👁️</span>
          </button>
        </div>
      </div>

      <div class="form-group">
        <label for="passConf">Konfirmasi Password <span class="req">*</span></label>
        <div class="input-pw-wrap">
          <input type="password" id="passConf" name="confirm_password"
                 placeholder="Ulangi password"
                 autocomplete="new-password" required>
          <button type="button" class="btn-toggle-pw" onclick="togglePw('passConf','icn2')">
            <span id="icn2">👁️</span>
          </button>
        </div>
      </div>

      <button type="submit" class="btn-primary btn-block">🚀 Daftar Sekarang</button>
    </form>

    <div class="auth-footer">
      Sudah punya akun? <a href="<?= BASE_PATH ?>/auth/login.php">Masuk di sini</a>
    </div>
  </div>
</main>

<script>
function togglePw(id, iconId) {
  const inp  = document.getElementById(id);
  const icon = document.getElementById(iconId);
  if (inp.type === 'password') { inp.type = 'text';     icon.textContent = '🙈'; }
  else                         { inp.type = 'password'; icon.textContent = '👁️'; }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>