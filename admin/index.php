<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();
$pageTitle = 'Dashboard';
$c = db_one("SELECT
  (SELECT COUNT(*) FROM shows WHERE show_date >= NOW()) AS upcoming_shows,
  (SELECT COUNT(*) FROM artists) AS artists,
  (SELECT COUNT(*) FROM users) AS users,
  (SELECT COALESCE(SUM(quantity),0) FROM bookings WHERE status='confirmed') AS tickets,
  (SELECT COALESCE(SUM(quantity*unit_price),0) FROM bookings WHERE status='confirmed') AS revenue");
$recent = db_all("SELECT b.booking_ref, b.quantity, b.status, b.booked_at, u.name AS user_name, a.name AS artist_name, s.city
  FROM bookings b JOIN users u ON u.id=b.user_id JOIN shows s ON s.id=b.show_id JOIN artists a ON a.id=s.artist_id
  ORDER BY b.id DESC LIMIT 8");
require __DIR__ . '/../includes/admin_header.php';
?>
<h1>Dashboard</h1>
<div class="stat-grid">
  <div class="stat"><b><?= (int)$c['upcoming_shows'] ?></b><span>Upcoming shows</span></div>
  <div class="stat"><b><?= (int)$c['artists'] ?></b><span>Artists</span></div>
  <div class="stat"><b><?= (int)$c['users'] ?></b><span>Registered users</span></div>
  <div class="stat"><b><?= (int)$c['tickets'] ?></b><span>Tickets booked</span></div>
  <div class="stat"><b><?= e(money($c['revenue'])) ?></b><span>Booked value (no payments taken)</span></div>
</div>
<div class="form-actions" style="margin-bottom:26px">
  <a class="btn" href="<?= e(url('admin/show_form.php')) ?>">Add a show</a>
  <a class="btn btn-ghost" href="<?= e(url('admin/artist_form.php')) ?>">Add an artist</a>
</div>
<h2>Recent bookings</h2>
<?php if (!$recent): ?><div class="empty"><strong>No bookings yet</strong>Bookings will appear here as users book tickets.</div>
<?php else: ?>
<div class="table-wrap"><table>
  <thead><tr><th>Booking ID</th><th>User</th><th>Artist</th><th>City</th><th>Tickets</th><th>Status</th><th>Booked</th></tr></thead>
  <tbody><?php foreach ($recent as $r): ?>
    <tr><td><code><?= e($r['booking_ref']) ?></code></td><td><?= e($r['user_name']) ?></td><td><?= e($r['artist_name']) ?></td><td><?= e($r['city']) ?></td>
      <td><?= (int)$r['quantity'] ?></td>
      <td><span class="badge <?= $r['status'] === 'confirmed' ? 'badge-ok' : 'badge-off' ?>"><?= e(ucfirst($r['status'])) ?></span></td>
      <td><?= e(date('d M Y, g:i A', strtotime($r['booked_at']))) ?></td></tr>
  <?php endforeach; ?></tbody>
</table></div>
<?php endif; ?>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
