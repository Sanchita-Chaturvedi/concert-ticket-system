<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$editing = $id > 0;
$show = $editing ? db_one(SHOW_SELECT . 'WHERE s.id = ?', 'i', [$id]) : null;
if ($editing && !$show) { flash('error', 'Show not found.'); redirect('admin/shows.php'); }
$artists = db_all('SELECT id, name FROM artists ORDER BY name');
$errors = [];
$f = $show ? [
    'artist_id' => $show['artist_id'], 'city' => $show['city'], 'venue' => $show['venue'],
    'show_date' => date('Y-m-d\TH:i', strtotime($show['show_date'])), 'price' => $show['price'], 'total_seats' => $show['total_seats'],
] : ['artist_id' => '', 'city' => '', 'venue' => '', 'show_date' => '', 'price' => '', 'total_seats' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    foreach ($f as $k => $_) $f[$k] = trim($_POST[$k] ?? '');
    $ts = strtotime($f['show_date']);
    if (!db_one('SELECT id FROM artists WHERE id = ?', 'i', [(int)$f['artist_id']])) $errors[] = 'Choose an artist. Add one first if the list is empty.';
    if ($f['city'] === '' || mb_strlen($f['city']) > 80) $errors[] = 'Enter the city (up to 80 characters).';
    if ($f['venue'] === '' || mb_strlen($f['venue']) > 150) $errors[] = 'Enter the venue (up to 150 characters).';
    if (!$ts) $errors[] = 'Enter a valid date and time.';
    elseif (!$editing && $ts < time()) $errors[] = 'A new show must be in the future.';
    if (!is_numeric($f['price']) || (float)$f['price'] < 0) $errors[] = 'Enter a ticket price of 0 or more.';
    if (!ctype_digit($f['total_seats']) || (int)$f['total_seats'] < 1) $errors[] = 'Enter a number of seats of at least 1.';
    elseif ($editing && (int)$f['total_seats'] < seats_sold($show)) $errors[] = 'Seats cannot be lower than the ' . seats_sold($show) . ' already sold.';

    if (!$errors) {
        $date = date('Y-m-d H:i:s', $ts);
        if ($editing) {
            db_exec('UPDATE shows SET artist_id=?, city=?, venue=?, show_date=?, price=?, total_seats=? WHERE id=?', 'isssdii',
                [(int)$f['artist_id'], $f['city'], $f['venue'], $date, (float)$f['price'], (int)$f['total_seats'], $id]);
            flash('success', 'Show updated.');
        } else {
            db_exec('INSERT INTO shows (artist_id, city, venue, show_date, price, total_seats) VALUES (?,?,?,?,?,?)', 'isssdi',
                [(int)$f['artist_id'], $f['city'], $f['venue'], $date, (float)$f['price'], (int)$f['total_seats']]);
            flash('success', 'Show added. It is now visible to users.');
        }
        redirect('admin/shows.php');
    }
}
$pageTitle = $editing ? 'Edit show' : 'Add show';
require __DIR__ . '/../includes/admin_header.php';
?>
<p><a href="<?= e(url('admin/shows.php')) ?>">&larr; All shows</a></p>
<div class="form-card wide">
  <h1><?= $editing ? 'Edit show' : 'Add a show' ?></h1>
  <?php foreach ($errors as $er): ?><div class="alert alert-error"><?= e($er) ?></div><?php endforeach; ?>
  <form method="post">
    <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$id ?>">
    <div class="field"><label for="artist_id">Artist</label>
      <select id="artist_id" name="artist_id" required>
        <option value="">Choose an artist</option>
        <?php foreach ($artists as $a): ?><option value="<?= (int)$a['id'] ?>" <?= (string)$a['id'] === (string)$f['artist_id'] ? 'selected' : '' ?>><?= e($a['name']) ?></option><?php endforeach; ?>
      </select></div>
    <div class="field"><label for="city">City</label><input type="text" id="city" name="city" value="<?= e($f['city']) ?>" required maxlength="80"></div>
    <div class="field"><label for="venue">Venue</label><input type="text" id="venue" name="venue" value="<?= e($f['venue']) ?>" required maxlength="150"></div>
    <div class="field"><label for="show_date">Date and time</label><input type="datetime-local" id="show_date" name="show_date" value="<?= e($f['show_date']) ?>" required></div>
    <div class="field"><label for="price">Ticket price (₹)</label><input type="number" id="price" name="price" value="<?= e($f['price']) ?>" min="0" step="0.01" required></div>
    <div class="field"><label for="total_seats">Total seats</label><input type="number" id="total_seats" name="total_seats" value="<?= e($f['total_seats']) ?>" min="1" step="1" required>
      <?php if ($editing): ?><span class="hint"><?= e(seats_sold($show)) ?> seats already sold.</span><?php endif; ?></div>
    <div class="form-actions"><button class="btn" type="submit"><?= $editing ? 'Save changes' : 'Add show' ?></button>
      <a class="btn btn-ghost" href="<?= e(url('admin/shows.php')) ?>">Cancel</a></div>
  </form>
</div>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
