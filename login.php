<?php
require_once __DIR__ . '/includes/functions.php';
$next = safe_next($_GET['next'] ?? $_POST['next'] ?? '');
if (is_logged_in()) redirect($next !== '' ? $next : 'index.php');
$error = ''; $email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $email = strtolower(trim($_POST['email'] ?? ''));
    $u = db_one('SELECT id, name, password_hash FROM users WHERE email = ?', 's', [$email]);
    if ($u && password_verify($_POST['password'] ?? '', $u['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$u['id'];
        flash('success', 'Welcome back, ' . $u['name'] . '.');
        redirect($next !== '' ? $next : 'index.php');
    }
    $error = 'Incorrect email or password. Check your details and try again.';   // REQ-3.4
}
$pageTitle = 'Log in';
require __DIR__ . '/includes/header.php';
?>
<div class="form-card">
  <h1>Log in</h1>
  <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
  <form method="post">
    <?= csrf_field() ?><input type="hidden" name="next" value="<?= e($next) ?>">
    <div class="field"><label for="email">Email</label><input type="email" id="email" name="email" value="<?= e($email) ?>" required autocomplete="email"></div>
    <div class="field"><label for="password">Password</label><input type="password" id="password" name="password" required autocomplete="current-password"></div>
    <div class="form-actions"><button class="btn" type="submit">Log in</button>
      <span class="muted">New here? <a href="<?= e(url('register.php' . ($next !== '' ? '?next=' . urlencode($next) : ''))) ?>">Create an account</a></span></div>
  </form>
  <p class="muted" style="margin:18px 0 0;font-size:.9rem">Demo account: user@example.com / User@123</p>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
