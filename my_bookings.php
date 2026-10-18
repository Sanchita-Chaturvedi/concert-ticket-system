<?php
$pageTitle = 'My bookings';
require_once __DIR__ . '/includes/functions.php';
require_login();
$rows = db_all('SELECT b.*, s.city, s.venue, s.show_date, a.name AS artist_name
                FROM bookings b JOIN shows s ON s.id=b.show_id JOIN artists a ON a.id=s.artist_id
                WHERE b.user_id = ? ORDER BY s.show_date DESC, b.id DESC', 'i', [$_SESSION['user_id']]);
$upcoming = array_filter($rows, fn($r) => strtotime($r['show_date']) >= time());
$past     = array_filter($rows, fn($r) => strtotime($r['show_date']) <  time());
require __DIR__ . '/includes/header.php';

function bookings_table(array $list, bool $canCancel): void { ?>
  <div class="table-wrap"><table>
    <thead><tr><th>Booking ID</th><th>Artist</th><th>Show</th><th>Tickets</th><th>Total</th><th>Status</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($list as $b): ?>
      <tr>
        <td><code><?= e($b['booking_ref']) ?></code></td>
        <td><?= e($b['artist_name']) ?></td>
        <td><?= e(fmt_date($b['show_date'])) ?><br><span class="muted"><?= e($b['venue']) ?>, <?= e($b['city']) ?></span></td>
        <td><?= (int)$b['quantity'] ?></td>
        <td><?= e(money($b['quantity'] * $b['unit_price'])) ?></td>
        <td><span class="badge <?= $b['status'] === 'confirmed' ? 'badge-ok' : 'badge-off' ?>"><?= e(ucfirst($b['status'])) ?></span></td>
        <td class="row-actions">
          <?php if ($canCancel && $b['status'] === 'confirmed'): ?>
            <form method="post" action="<?= e(url('cancel_booking.php')) ?>" data-confirm="Cancel this booking? The seats will be released.">
              <?= csrf_field() ?><input type="hidden" name="booking_id" value="<?= (int)$b['id'] ?>">
              <button class="link-btn" type="submit">Cancel booking</button>
            </form>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
<?php } ?>

<h1>My bookings</h1>
<h2>Upcoming</h2>
<?php if ($upcoming): bookings_table($upcoming, true); else: ?>
  <div class="empty"><strong>No upcoming bookings</strong>Find a show and grab your seats.
    <p><a class="btn btn-small" href="<?= e(url('index.php')) ?>">Browse shows</a></p></div>
<?php endif; ?>

<?php if ($past): ?>
  <h2 style="margin-top:32px">Past</h2>
  <?php bookings_table($past, false); ?>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
