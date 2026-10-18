<?php
$pageTitle = 'My favourite artists';
require_once __DIR__ . '/includes/functions.php';
require_login();
$artists = db_all('SELECT a.* FROM favourites f JOIN artists a ON a.id = f.artist_id WHERE f.user_id = ? ORDER BY a.name', 'i', [$_SESSION['user_id']]);
require __DIR__ . '/includes/header.php';
?>
<h1>My favourite artists</h1>
<?php if (!$artists): ?>
  <div class="empty"><strong>No favourites yet</strong>Follow artists to see all their upcoming shows here.
    <p><a class="btn btn-small" href="<?= e(url('artists.php')) ?>">Find artists</a></p></div>
<?php else: foreach ($artists as $a):
  $shows = db_all(SHOW_SELECT . 'WHERE s.artist_id = ? AND s.show_date >= NOW() ORDER BY s.show_date', 'i', [$a['id']]); ?>
  <section style="margin-bottom:30px">
    <div class="section-head">
      <h2 style="margin:0"><a style="color:inherit" href="<?= e(url('artist.php?id=' . (int)$a['id'])) ?>"><?= e($a['name']) ?></a> <span class="muted" style="font-size:1rem;font-family:var(--body)"><?= e($a['genre']) ?></span></h2>
      <form method="post" action="<?= e(url('favourite_toggle.php')) ?>">
        <?= csrf_field() ?><input type="hidden" name="artist_id" value="<?= (int)$a['id'] ?>"><input type="hidden" name="back" value="favourites.php">
        <button class="btn btn-small btn-ghost" type="submit">Remove</button>
      </form>
    </div>
    <?php if (!$shows): ?><p class="muted">No upcoming shows right now.</p>
    <?php else: ?><div class="ticket-list"><?php foreach ($shows as $s) include __DIR__ . '/includes/show_card.php'; ?></div><?php endif; ?>
  </section>
<?php endforeach; endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
