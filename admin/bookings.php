<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();
$showId = (int)($_GET['show_id'] ?? 0);
$shows = db_all(SHOW_SELECT . 'ORDER BY s.show_date DESC');
$show = $showId ? db_one(SHOW_SELECT . 'WHERE s.id = ?', 'i', [$showId]) : null;
$rows = [];
if ($show) {
    $rows = db_all('SELECT b.*, u.name AS user_name, u.email FROM bookings b JOIN users u ON u.id=b.user_id
                    WHERE b.show_id = ? ORDER BY b.id DESC', 'i', [$showId]);   // REQ-5.4
}
$pageTitle = 'Bookings by show';
require __DIR__ . '/../includes/admin_header.php';
?>
<h1>Bookings by show</h1>
<form method="get" class="filter" style="margin-bottom:22px">
  <label for="show_id">Show</label>
  <select name="show_id" id="show_id" onchange="this.form.submit()" style="min-width:320px">
    <option value="">Choose a show</option>
    <?php foreach ($shows as $s): ?>
      <option value="<?= (int)$s['id'] ?>" <?= $s['id'] == $showId ? 'selected' : '' ?>><?= e($s['artist_name']) ?> · <?= e($s['city']) ?> · <?= e(date('d M Y', strtotime($s['show_date']))) ?></option>
    <?php endforeach; ?>
  </select>
  <noscript><button class="btn" type="submit">Show bookings</button></noscript>
</form>

<?php if (!$show): ?>
  <div class="empty"><strong>Pick a show</strong>Choose a show above to see everyone who booked it.</div>
<?php else: ?>
  <div class="stat-grid">
    <div class="stat"><b><?= (int)$show['total_seats'] ?></b><span>Total seats</span></div>
    <div class="stat"><b><?= e(seats_sold($show)) ?></b><span>Sold</span></div>
    <div class="stat"><b><?= (int)$show['seats_remaining'] ?></b><span>Remaining</span></div>
  </div>
  <?php if (!$rows): ?>
    <div class="empty"><strong>No bookings yet</strong>Nobody has booked this show.</div>
  <?php else: ?>
  <div class="table-wrap"><table>
    <thead><tr><th>Booking ID</th><th>Name</th><th>Email</th><th>Tickets</th><th>Total</th><th>Status</th><th>Booked on</th></tr></thead>
    <tbody><?php foreach ($rows as $b): ?>
      <tr><td><code><?= e($b['booking_ref']) ?></code></td><td><?= e($b['user_name']) ?></td><td><?= e($b['email']) ?></td>
        <td><?= (int)$b['quantity'] ?></td><td><?= e(money($b['quantity'] * $b['unit_price'])) ?></td>
        <td><span class="badge <?= $b['status'] === 'confirmed' ? 'badge-ok' : 'badge-off' ?>"><?= e(ucfirst($b['status'])) ?></span></td>
        <td><?= e(date('d M Y, g:i A', strtotime($b['booked_at']))) ?></td></tr>
    <?php endforeach; ?></tbody>
  </table></div>
  <?php endif; ?>
<?php endif; ?>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
