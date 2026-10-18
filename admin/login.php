<?php
require_once __DIR__ . '/../includes/functions.php';
if (is_admin()) redirect('admin/index.php');
$error = ''; $email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $email = strtolower(trim($_POST['email'] ?? ''));
    $a = db_one('SELECT id, password_hash FROM admins WHERE email = ?', 's', [$email]);
    if ($a && password_verify($_POST['password'] ?? '', $a['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = (int)$a['id'];
        redirect('admin/index.php');
    }
    $error = 'Incorrect admin email or password.';
}
$pageTitle = 'Admin log in';
require __DIR__ . '/../includes/admin_header.php';
?>
<div class="form-card">
  <h1>Admin log in</h1>
  <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
  <form method="post">
    <?= csrf_field() ?>
    <div class="field"><label for="email">Admin email</label><input type="email" id="email" name="email" value="<?= e($email) ?>" required></div>
    <div class="field"><label for="password">Password</label><input type="password" id="password" name="password" required></div>
    <button class="btn" type="submit">Log in</button>
  </form>
  <p class="muted" style="margin:18px 0 0;font-size:.9rem">Default admin: admin@concert.local / Admin@123</p>
</div>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
