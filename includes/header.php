<?php
require_once __DIR__ . '/functions.php';
$pageTitle = $pageTitle ?? SITE_NAME;
$user = current_user();
$here = basename($_SERVER['SCRIPT_NAME']);
function nav_link(string $file, string $label, string $here): string {
    $cls = $here === $file ? ' class="active" aria-current="page"' : '';
    return '<a href="' . e(url($file)) . '"' . $cls . '>' . e($label) . '</a>';
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?> · <?= e(SITE_NAME) ?></title>
<link rel="stylesheet" href="<?= e(url('assets/css/style.css')) ?>">
</head>
<body>
<header class="site-header">
  <div class="wrap bar">
    <a class="brand" href="<?= e(url('index.php')) ?>"><span class="brand-mark" aria-hidden="true"></span><?= e(SITE_NAME) ?></a>
    <button class="nav-toggle" type="button" aria-label="Menu" aria-expanded="false" data-nav-toggle>Menu</button>
    <nav class="nav" data-nav>
      <?= nav_link('index.php', 'Shows', $here) ?>
      <?= nav_link('artists.php', 'Artists', $here) ?>
      <?php if ($user): ?>
        <?= nav_link('favourites.php', 'My Favourite Artists', $here) ?>
        <?= nav_link('my_bookings.php', 'My Bookings', $here) ?>
        <span class="nav-user">Hi, <?= e(explode(' ', $user['name'])[0]) ?></span>
        <a class="btn btn-small btn-ghost" href="<?= e(url('logout.php')) ?>">Log out</a>
      <?php else: ?>
        <?= nav_link('login.php', 'Log in', $here) ?>
        <a class="btn btn-small" href="<?= e(url('register.php')) ?>">Sign up</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
<main class="wrap main">
<?php foreach (flashes() as $f): ?>
  <div class="alert alert-<?= e($f['type']) ?>" role="status"><?= e($f['msg']) ?></div>
<?php endforeach; ?>
