<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$editing = $id > 0;
$a = $editing ? db_one('SELECT * FROM artists WHERE id = ?', 'i', [$id]) : null;
if ($editing && !$a) { flash('error', 'Artist not found.'); redirect('admin/artists.php'); }
$f = $a ? ['name' => $a['name'], 'genre' => $a['genre'], 'bio' => (string)$a['bio']] : ['name' => '', 'genre' => '', 'bio' => ''];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    foreach ($f as $k => $_) $f[$k] = trim($_POST[$k] ?? '');
    if ($f['name'] === '' || mb_strlen($f['name']) > 120) $errors[] = 'Enter the artist name (up to 120 characters).';
    if (mb_strlen($f['genre']) > 60) $errors[] = 'Genre must be 60 characters or fewer.';
    if (mb_strlen($f['bio']) > 2000) $errors[] = 'Bio must be 2000 characters or fewer.';
    if (!$errors) {
        if ($editing) {
            db_exec('UPDATE artists SET name=?, genre=?, bio=? WHERE id=?', 'sssi', [$f['name'], $f['genre'], $f['bio'], $id]);
            flash('success', 'Artist updated.');
        } else {
            db_exec('INSERT INTO artists (name, genre, bio) VALUES (?,?,?)', 'sss', [$f['name'], $f['genre'], $f['bio']]);
            flash('success', 'Artist added.');
        }
        redirect('admin/artists.php');
    }
}
$pageTitle = $editing ? 'Edit artist' : 'Add artist';
require __DIR__ . '/../includes/admin_header.php';
?>
<p><a href="<?= e(url('admin/artists.php')) ?>">&larr; All artists</a></p>
<div class="form-card wide">
  <h1><?= $editing ? 'Edit artist' : 'Add an artist' ?></h1>
  <?php foreach ($errors as $er): ?><div class="alert alert-error"><?= e($er) ?></div><?php endforeach; ?>
  <form method="post">
    <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$id ?>">
    <div class="field"><label for="name">Name</label><input type="text" id="name" name="name" value="<?= e($f['name']) ?>" required maxlength="120"></div>
    <div class="field"><label for="genre">Genre</label><input type="text" id="genre" name="genre" value="<?= e($f['genre']) ?>" maxlength="60"></div>
    <div class="field"><label for="bio">Bio</label><textarea id="bio" name="bio" rows="5" maxlength="2000"><?= e($f['bio']) ?></textarea></div>
    <div class="form-actions"><button class="btn" type="submit"><?= $editing ? 'Save changes' : 'Add artist' ?></button>
      <a class="btn btn-ghost" href="<?= e(url('admin/artists.php')) ?>">Cancel</a></div>
  </form>
</div>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
