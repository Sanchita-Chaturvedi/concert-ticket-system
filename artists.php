<?php
$pageTitle = 'Artists';
require_once __DIR__ . '/includes/functions.php';
$artists = db_all('SELECT a.*, (SELECT COUNT(*) FROM shows s WHERE s.artist_id=a.id AND s.show_date >= NOW()) AS upcoming FROM artists a ORDER BY a.name');
$favIds = is_logged_in() ? array_column(db_all('SELECT artist_id FROM favourites WHERE user_id=?', 'i', [$_SESSION['user_id']]), 'artist_id') : [];
require __DIR__ . '/includes/header.php';
?>
<h1>Artists</h1>
<?php if (!$artists): ?>
  <div class="empty"><strong>No artists yet</strong>Artists added by the admin will appear here.</div>
<?php else: ?>
<div class="grid-artists">
  <?php foreach ($artists as $a): $isFav = in_array($a['id'], $favIds); ?>
  <article class="artist-card">
    <div class="avatar" aria-hidden="true"><?= e(initials($a['name'])) ?></div>
    <h3><a href="<?= e(url('artist.php?id=' . (int)$a['id'])) ?>"><?= e($a['name']) ?></a></h3>
    <p><?= e($a['genre']) ?> &middot; <?= (int)$a['upcoming'] ?> upcoming <?= (int)$a['upcoming'] === 1 ? 'show' : 'shows' ?></p>
    <div class="actions">
      <a class="btn btn-small btn-ghost" href="<?= e(url('artist.php?id=' . (int)$a['id'])) ?>">View artist</a>
      <form method="post" action="<?= e(url('favourite_toggle.php')) ?>">
        <?= csrf_field() ?><input type="hidden" name="artist_id" value="<?= (int)$a['id'] ?>"><input type="hidden" name="back" value="artists.php">
        <button class="btn btn-small <?= $isFav ? 'btn-ghost' : '' ?>" type="submit"><?= $isFav ? 'Remove from favourites' : 'Add to favourites' ?></button>
      </form>
    </div>
  </article>
  <?php endforeach; ?>
</div>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
