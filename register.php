<?php
require_once __DIR__ . '/includes/functions.php';
if (is_logged_in()) redirect('index.php');
$next = safe_next($_GET['next'] ?? $_POST['next'] ?? '');
$errors = []; $name = ''; $email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $pass = $_POST['password'] ?? '';
    $pass2 = $_POST['password2'] ?? '';

    if ($name === '' || mb_strlen($name) > 100) $errors[] = 'Enter your name (up to 100 characters).';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 150) $errors[] = 'Enter a valid email address.';
    if (strlen($pass) < 6) $errors[] = 'Password must be at least 6 characters.';
    if ($pass !== $pass2) $errors[] = 'The two passwords do not match.';
    if (!$errors && db_one('SELECT id FROM users WHERE email = ?', 's', [$email])) {
        $errors[] = 'An account with this email already exists. Try logging in instead.';
    }
    if (!$errors) {
        db_exec('INSERT INTO users (name, email, password_hash) VALUES (?,?,?)', 'sss',
                [$name, $email, password_hash($pass, PASSWORD_DEFAULT)]);   // REQ-3.3
        session_regenerate_id(true);
        $_SESSION['user_id'] = $db->insert_id;
        flash('success', 'Welcome, ' . $name . '! Your account is ready.');
        redirect($next !== '' ? $next : 'index.php');
    }
}
$pageTitle = 'Sign up';
require __DIR__ . '/includes/header.php';
?>
<div class="form-card">
  <h1>Create your account</h1>
  <?php foreach ($errors as $er): ?><div class="alert alert-error"><?= e($er) ?></div><?php endforeach; ?>
  <form method="post" novalidate>
    <?= csrf_field() ?><input type="hidden" name="next" value="<?= e($next) ?>">
    <div class="field"><label for="name">Full name</label><input type="text" id="name" name="name" value="<?= e($name) ?>" required autocomplete="name"></div>
    <div class="field"><label for="email">Email</label><input type="email" id="email" name="email" value="<?= e($email) ?>" required autocomplete="email"></div>
    <div class="field"><label for="password">Password</label><input type="password" id="password" name="password" required autocomplete="new-password"><span class="hint">At least 6 characters.</span></div>
    <div class="field"><label for="password2">Confirm password</label><input type="password" id="password2" name="password2" required autocomplete="new-password"></div>
    <div class="form-actions"><button class="btn" type="submit">Create account</button>
      <span class="muted">Have an account? <a href="<?= e(url('login.php' . ($next !== '' ? '?next=' . urlencode($next) : ''))) ?>">Log in</a></span></div>
  </form>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
