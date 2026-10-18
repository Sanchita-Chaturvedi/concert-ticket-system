<?php
/** Renders one show as a ticket. Expects $s from SHOW_SELECT. */
$remaining = (int)$s['seats_remaining'];
$pct = $s['total_seats'] > 0 ? round(($remaining / $s['total_seats']) * 100) : 0;
$low = $remaining > 0 && $pct <= 15;
?>
<article class="ticket">
  <div class="ticket-main">
    <p class="ticket-genre"><?= e($s['genre']) ?></p>
    <h3><a href="<?= e(url('artist.php?id=' . (int)$s['artist_id'])) ?>"><?= e($s['artist_name']) ?></a></h3>
    <p class="ticket-where"><?= e($s['venue']) ?>, <?= e($s['city']) ?></p>
    <p class="ticket-when"><?= e(fmt_date($s['show_date'])) ?> at <?= e(fmt_time($s['show_date'])) ?></p>
  </div>
  <div class="ticket-stub">
    <p class="ticket-price"><small>from</small><?= e(money($s['price'])) ?></p>
    <p class="ticket-left <?= $remaining === 0 ? 'is-out' : ($low ? 'is-low' : '') ?>">
      <?= $remaining === 0 ? 'Sold out' : e($remaining) . ' seats left' ?>
    </p>
    <a class="btn btn-small" href="<?= e(url('show.php?id=' . (int)$s['id'])) ?>">View show</a>
  </div>
</article>
