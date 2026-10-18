<?php
$pageTitle = 'Booking confirmed';
require_once __DIR__ . '/includes/functions.php';
require_login();
$b = db_one('SELECT b.*, s.city, s.venue, s.show_date, a.name AS artist_name
             FROM bookings b JOIN shows s ON s.id=b.show_id JOIN artists a ON a.id=s.artist_id
             WHERE b.booking_ref = ? AND b.user_id = ?', 'si', [$_GET['ref'] ?? '', $_SESSION['user_id']]);
if (!$b) { flash('error', 'Booking not found.'); redirect('my_bookings.php'); }
require __DIR__ . '/includes/header.php';
?>
<div class="confirm-ticket">
  <div class="top">
    <h1>You're going to see <?= e($b['artist_name']) ?></h1>
    <p>Your booking is confirmed.</p>
  </div>
  <div class="body">
    <p>Booking ID<br><span class="ref"><?= e($b['booking_ref']) ?></span></p>
    <dl class="facts">
      <dt>Venue</dt><dd><?= e($b['venue']) ?>, <?= e($b['city']) ?></dd>
      <dt>Date</dt><dd><?= e(fmt_date($b['show_date'])) ?> at <?= e(fmt_time($b['show_date'])) ?></dd>
      <dt>Tickets</dt><dd><?= (int)$b['quantity'] ?> &times; <?= e(money($b['unit_price'])) ?></dd>
      <dt>Total</dt><dd><?= e(money($b['quantity'] * $b['unit_price'])) ?> (no payment taken)</dd>
      <dt>Status</dt><dd><?= e(ucfirst($b['status'])) ?></dd>
    </dl>
    <div class="form-actions">
      <a class="btn" href="<?= e(url('my_bookings.php')) ?>">View my bookings</a>
      <a class="btn btn-ghost" href="<?= e(url('index.php')) ?>">Browse more shows</a>
    </div>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
