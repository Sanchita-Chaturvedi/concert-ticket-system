<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {   // delete a show (REQ-5.2)
    csrf_check();
    if (($_POST['action'] ?? '') === 'delete') {
        db_exec('DELETE FROM shows WHERE id = ?', 'i', [(int)($_POST['id'] ?? 0)]);
        flash('success', 'Show deleted.');
    }
    redirect('admin/shows.php');
}
$pageTitle = 'Manage shows';
$shows = db_all(SHOW_SELECT . 'ORDER BY s.show_date DESC');
require __DIR__ . '/../includes/admin_header.php';
?>
<div class="section-head"><h1>Shows</h1><a class="btn" href="<?= e(url('admin/show_form.php')) ?>">Add a show</a></div>
<?php if (!$shows): ?><div class="empty"><strong>No shows yet</strong>Add your first show so users can book it.</div>
<?php else: ?>
<div class="table-wrap"><table>
  <thead><tr><th>Artist</th><th>City / Venue</th><th>Date</th><th>Price</th><th>Seats (sold / total)</th><th></th></tr></thead>
  <tbody><?php foreach ($shows as $s): $past = strtotime($s['show_date']) < time(); ?>
    <tr>
      <td><strong><?= e($s['artist_name']) ?></strong></td>
      <td><?= e($s['city']) ?><br><span class="muted"><?= e($s['venue']) ?></span></td>
      <td><?= e(date('d M Y, g:i A', strtotime($s['show_date']))) ?> <?= $past ? '<span class="badge badge-past">Past</span>' : '' ?></td>
      <td><?= e(money($s['price'])) ?></td>
      <td><?= e(seats_sold($s)) ?> / <?= (int)$s['total_seats'] ?></td>
      <td class="row-actions">
        <a href="<?= e(url('admin/bookings.php?show_id=' . (int)$s['id'])) ?>">Bookings</a>
        <a href="<?= e(url('admin/show_form.php?id=' . (int)$s['id'])) ?>">Edit</a>
        <form method="post" data-confirm="Delete this show and all its bookings? This cannot be undone.">
          <?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
          <button class="link-btn" type="submit">Delete</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?></tbody>
</table></div>
<?php endif; ?>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
