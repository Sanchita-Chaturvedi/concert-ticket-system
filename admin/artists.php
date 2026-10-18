<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {   // delete an artist (REQ-5.3)
    csrf_check();
    if (($_POST['action'] ?? '') === 'delete') {
        db_exec('DELETE FROM artists WHERE id = ?', 'i', [(int)($_POST['id'] ?? 0)]);
        flash('success', 'Artist deleted along with their shows.');
    }
    redirect('admin/artists.php');
}
$pageTitle = 'Manage artists';
$artists = db_all('SELECT a.*, (SELECT COUNT(*) FROM shows s WHERE s.artist_id=a.id) AS show_count FROM artists a ORDER BY a.name');
require __DIR__ . '/../includes/admin_header.php';
?>
<div class="section-head"><h1>Artists</h1><a class="btn" href="<?= e(url('admin/artist_form.php')) ?>">Add an artist</a></div>
<?php if (!$artists): ?><div class="empty"><strong>No artists yet</strong>Add an artist, then create shows for them.</div>
<?php else: ?>
<div class="table-wrap"><table>
  <thead><tr><th>Name</th><th>Genre</th><th>Shows</th><th></th></tr></thead>
  <tbody><?php foreach ($artists as $a): ?>
    <tr><td><strong><?= e($a['name']) ?></strong></td><td><?= e($a['genre']) ?></td><td><?= (int)$a['show_count'] ?></td>
      <td class="row-actions">
        <a href="<?= e(url('admin/artist_form.php?id=' . (int)$a['id'])) ?>">Edit</a>
        <form method="post" data-confirm="Delete this artist? All their shows and bookings will be deleted too.">
          <?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
          <button class="link-btn" type="submit">Delete</button>
        </form></td></tr>
  <?php endforeach; ?></tbody>
</table></div>
<?php endif; ?>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
