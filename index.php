<?php
$pageTitle = 'Upcoming shows';
require __DIR__ . '/includes/header.php';

$city = trim($_GET['city'] ?? '');
$sql = SHOW_SELECT . 'WHERE s.show_date >= NOW() ';
$types = ''; $params = [];
if ($city !== '') { $sql .= 'AND s.city = ? '; $types = 's'; $params[] = $city; }
$sql .= 'ORDER BY s.show_date ASC';
$shows = db_all($sql, $types, $params);
?>
<section class="hero">
  <h1>Find your next live night</h1>
  <p>Browse upcoming concerts, check how many seats are left, and book in a minute.</p>
  <form method="get" class="filter" action="<?= e(url('index.php')) ?>">
    <label for="city">City</label>
    <select name="city" id="city" onchange="this.form.submit()">
      <option value="">All cities</option>
      <?php foreach (cities() as $c): ?>
        <option value="<?= e($c) ?>" <?= $c === $city ? 'selected' : '' ?>><?= e($c) ?></option>
      <?php endforeach; ?>
    </select>
    <noscript><button class="btn" type="submit">Show</button></noscript>
  </form>
</section>

<div class="section-head">
  <h2><?= $city !== '' ? 'Shows in ' . e($city) : 'All upcoming shows' ?></h2>
  <span class="muted"><?= count($shows) ?> <?= count($shows) === 1 ? 'show' : 'shows' ?></span>
</div>

<?php if (!$shows): ?>
  <div class="empty"><strong>No shows found</strong>
    <?= $city !== '' ? 'There are no upcoming shows in ' . e($city) . '. Try another city.' : 'Check back soon for new concerts.' ?>
    <?php if ($city !== ''): ?><p><a class="btn btn-small" href="<?= e(url('index.php')) ?>">See all cities</a></p><?php endif; ?>
  </div>
<?php else: ?>
  <div class="ticket-list">
    <?php foreach ($shows as $s) include __DIR__ . '/includes/show_card.php'; ?>
  </div>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
