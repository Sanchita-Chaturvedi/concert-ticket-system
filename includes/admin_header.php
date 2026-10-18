<?php
require_once __DIR__ . '/functions.php';
$pageTitle = $pageTitle ?? 'Admin';
$here = basename($_SERVER['SCRIPT_NAME']);
function admin_link(array $files, string $label): string {
    global $here;
    $cls = in_array($here, $files, true) ? ' class="active"' : '';
    return '<a href="' . e(url('admin/' . $files[0])) . '"' . $cls . '>' . e($label) . '</a>';
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?> · Admin · <?= e(SITE_NAME) ?></title>
<link rel="stylesheet" href="<?= e(url('assets/css/style.css')) ?>">
</head>
<body>
<header class="admin-bar">
  <div class="wrap bar">
    <a class="brand" href="<?= e(url('admin/index.php')) ?>"><span class="brand-mark" aria-hidden="true"></span>Admin</a>
    <?php if (is_admin()): ?>
      <?= admin_link(['index.php'], 'Dashboard') ?>
      <?= admin_link(['shows.php', 'show_form.php'], 'Shows') ?>
      <?= admin_link(['artists.php', 'artist_form.php'], 'Artists') ?>
      <?= admin_link(['bookings.php'], 'Bookings') ?>
      <span class="spacer"></span>
      <a href="<?= e(url('index.php')) ?>">View site</a>
      <a href="<?= e(url('admin/logout.php')) ?>">Log out</a>
    <?php endif; ?>
  </div>
</header>
<main class="wrap main">
<?php foreach (flashes() as $f): ?>
  <div class="alert alert-<?= e($f['type']) ?>" role="status"><?= e($f['msg']) ?></div>
<?php endforeach; ?>
