<?php
require_once __DIR__ . '/includes/functions.php';
$id = (int)($_GET['id'] ?? 0);
$s = db_one(SHOW_SELECT . 'WHERE s.id = ?', 'i', [$id]);
if (!$s) { http_response_code(404); $pageTitle = 'Show not found'; require __DIR__ . '/includes/header.php';
  echo '<div class="empty"><strong>Show not found</strong>It may have been removed. <p><a class="btn btn-small" href="' . e(url('index.php')) . '">Back to shows</a></p></div>';
  require __DIR__ . '/includes/footer.php'; exit; }

$pageTitle = $s['artist_name'] . ' in ' . $s['city'];
$remaining = (int)$s['seats_remaining'];
$sold = seats_sold($s);
$pct = $s['total_seats'] > 0 ? round($remaining / $s['total_seats'] * 100) : 0;
$isPast = strtotime($s['show_date']) < time();
$maxQty = min(MAX_TICKETS_PER_BOOKING, $remaining);
require __DIR__ . '/includes/header.php';
?>
<p><a href="<?= e(url('index.php')) ?>">&larr; All shows</a></p>
<div class="show-layout">
  <section class="panel">
    <p class="ticket-genre"><?= e($s['genre']) ?></p>
    <h1><a style="color:inherit;text-decoration:none" href="<?= e(url('artist.php?id=' . (int)$s['artist_id'])) ?>"><?= e($s['artist_name']) ?></a></h1>
    <dl class="facts">
      <dt>City</dt><dd><?= e($s['city']) ?></dd>
      <dt>Venue</dt><dd><?= e($s['venue']) ?></dd>
      <dt>Date</dt><dd><?= e(fmt_date($s['show_date'])) ?></dd>
      <dt>Starts</dt><dd><?= e(fmt_time($s['show_date'])) ?></dd>
      <dt>Ticket price</dt><dd><?= e(money($s['price'])) ?> each</dd>
    </dl>
    <h3>Seat availability</h3>
    <div class="meter <?= $pct <= 15 ? 'low' : '' ?>" role="img" aria-label="<?= e($remaining) ?> of <?= e($s['total_seats']) ?> seats left"><span style="width:<?= e($pct) ?>%"></span></div>
    <div class="seat-stats">
      <div><b><?= e($s['total_seats']) ?></b><span>Total seats</span></div>
      <div><b><?= e($sold) ?></b><span>Sold</span></div>
      <div><b><?= e($remaining) ?></b><span>Remaining</span></div>
    </div>
  </section>

  <section class="panel">
    <h2>Book tickets</h2>
    <?php if ($isPast): ?>
      <div class="alert alert-info">This show has already taken place.</div>
    <?php elseif ($remaining <= 0): ?>
      <div class="alert alert-error">This show is sold out.</div>
    <?php else: ?>
      <?php if (!is_logged_in()): ?>
        <p class="muted">You can pick your tickets now. You'll be asked to log in or sign up before booking is completed.</p>
      <?php endif; ?>
      <form method="post" action="<?= e(url('book.php')) ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="show_id" value="<?= (int)$s['id'] ?>">
        <div class="field">
          <label for="quantity">Number of tickets</label>
          <input type="number" id="quantity" name="quantity" min="1" max="<?= (int)$maxQty ?>" value="1" required
                 data-qty data-price="<?= e($s['price']) ?>">
          <span class="hint">Up to <?= (int)$maxQty ?> per booking.</span>
        </div>
        <div class="total-line"><span>Total</span><strong data-total><?= e(money($s['price'])) ?></strong></div>
        <button class="btn" type="submit">Book now</button>
        <p class="hint muted" style="margin-top:12px;font-size:.88rem">No payment is taken. Booking confirms your seats instantly.</p>
      </form>
    <?php endif; ?>
  </section>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
