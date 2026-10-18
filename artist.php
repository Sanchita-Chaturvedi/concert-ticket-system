<?php
require_once __DIR__ . '/includes/functions.php';
$id = (int)($_GET['id'] ?? 0);
$a = db_one('SELECT * FROM artists WHERE id = ?', 'i', [$id]);
if (!$a) { flash('error', 'Artist not found.'); redirect('artists.php'); }
$isFav = is_logged_in() && db_one('SELECT 1 AS x FROM favourites WHERE user_id=? AND artist_id=?', 'ii', [$_SESSION['user_id'], $id]);
$shows = db_all(SHOW_SELECT . 'WHERE s.artist_id = ? AND s.show_date >= NOW() ORDER BY s.show_date', 'i', [$id]);
$pageTitle = $a['name'];
require __DIR__ . '/includes/header.php';
?>
<p><a href="<?= e(url('artists.php')) ?>">&larr; All artists</a></p>
<div class="artist-head">
  <div class="avatar big" aria-hidden="true"><?= e(initials($a['name'])) ?></div>
  <div>
    <h1><?= e($a['name']) ?></h1>
    <p class="muted" style="margin:4px 0 12px"><?= e($a['genre']) ?></p>
    <form method="post" action="<?= e(url('favourite_toggle.php')) ?>">
      <?= csrf_field() ?><input type="hidden" name="artist_id" value="<?= (int)$a['id'] ?>"><input type="hidden" name="back" value="artist.php?id=<?= (int)$a['id'] ?>">
      <button class="btn <?= $isFav ? 'btn-ghost' : '' ?>" type="submit"><?= $isFav ? 'Remove from favourites' : 'Add to favourites' ?></button>
    </form>
  </div>
</div>
<?php if (!empty($a['bio'])): ?><p style="max-width:70ch"><?= nl2br(e($a['bio'])) ?></p><?php endif; ?>

<h2 style="margin-top:28px">Upcoming shows</h2>
<?php if (!$shows): ?>
  <div class="empty"><strong>No upcoming shows</strong>Add <?= e($a['name']) ?> to your favourites and check back later.</div>
<?php else: ?>
  <div class="ticket-list"><?php foreach ($shows as $s) include __DIR__ . '/includes/show_card.php'; ?></div>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
