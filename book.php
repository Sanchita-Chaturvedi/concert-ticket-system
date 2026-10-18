<?php
require_once __DIR__ . '/includes/functions.php';
require_post();

$showId = (int)($_POST['show_id'] ?? 0);
$qty = (int)($_POST['quantity'] ?? 0);
$back = 'show.php?id=' . $showId;

// REQ-4.4: must be logged in; come back to the show afterwards.
if (!is_logged_in()) { require_login($back); }

if ($qty < 1 || $qty > MAX_TICKETS_PER_BOOKING) {
    flash('error', 'Choose between 1 and ' . MAX_TICKETS_PER_BOOKING . ' tickets.');
    redirect($back);
}

$ref = '';
try {
    $db->begin_transaction();
    // Lock the show row so two people cannot take the last seat at the same time (TBD-2).
    $show = db_one('SELECT id, price, total_seats, show_date FROM shows WHERE id = ? FOR UPDATE', 'i', [$showId]);
    if (!$show) { $db->rollback(); flash('error', 'That show no longer exists.'); redirect('index.php'); }
    if (strtotime($show['show_date']) < time()) { $db->rollback(); flash('error', 'This show has already taken place.'); redirect($back); }

    $row = db_one("SELECT COALESCE(SUM(quantity),0) AS sold FROM bookings WHERE show_id = ? AND status = 'confirmed'", 'i', [$showId]);
    $remaining = (int)$show['total_seats'] - (int)$row['sold'];
    // REQ-4.3: never more than the seats remaining.
    if ($qty > $remaining) {
        $db->rollback();
        flash('error', $remaining > 0 ? "Only $remaining seat(s) left for this show." : 'Sorry, this show just sold out.');
        redirect($back);
    }

    $ref = 'CTM-' . strtoupper(bin2hex(random_bytes(4)));
    db_exec('INSERT INTO bookings (booking_ref, user_id, show_id, quantity, unit_price) VALUES (?,?,?,?,?)',
            'siiid', [$ref, $_SESSION['user_id'], $showId, $qty, $show['price']]);
    $db->commit();
} catch (Throwable $ex) {
    try { $db->rollback(); } catch (Throwable $e2) {}
    flash('error', 'Booking failed. Please try again.');
    redirect($back);
}
flash('success', 'Booking confirmed!');
redirect('booking_confirmation.php?ref=' . urlencode($ref));
