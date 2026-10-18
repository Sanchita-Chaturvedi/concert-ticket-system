<?php
require_once __DIR__ . '/includes/functions.php';
require_post();
$artistId = (int)($_POST['artist_id'] ?? 0);
$back = safe_next($_POST['back'] ?? '') ?: 'artists.php';

// REQ-2.3: guests are sent to login first, then returned to the artist page.
if (!is_logged_in()) require_login($back);

$artist = db_one('SELECT id, name FROM artists WHERE id = ?', 'i', [$artistId]);
if (!$artist) { flash('error', 'Artist not found.'); redirect('artists.php'); }

if (db_one('SELECT 1 AS x FROM favourites WHERE user_id=? AND artist_id=?', 'ii', [$_SESSION['user_id'], $artistId])) {
    db_exec('DELETE FROM favourites WHERE user_id=? AND artist_id=?', 'ii', [$_SESSION['user_id'], $artistId]);
    flash('info', $artist['name'] . ' removed from your favourites.');
} else {
    db_exec('INSERT INTO favourites (user_id, artist_id) VALUES (?,?)', 'ii', [$_SESSION['user_id'], $artistId]);
    flash('success', $artist['name'] . ' added to your favourites.');
}
redirect($back);
