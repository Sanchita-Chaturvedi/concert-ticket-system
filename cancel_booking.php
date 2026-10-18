<?php
require_once __DIR__ . '/includes/functions.php';
require_post();
require_login('my_bookings.php');

$id = (int)($_POST['booking_id'] ?? 0);
$b = db_one('SELECT b.id, s.show_date FROM bookings b JOIN shows s ON s.id=b.show_id
             WHERE b.id = ? AND b.user_id = ? AND b.status = "confirmed"', 'ii', [$id, $_SESSION['user_id']]);
if (!$b) {
    flash('error', 'That booking could not be found or is already cancelled.');
} elseif (strtotime($b['show_date']) < time()) {
    flash('error', 'Past shows cannot be cancelled.');
} else {
    // Seats go back automatically: availability is calculated from confirmed bookings only (REQ-4.7).
    db_exec('UPDATE bookings SET status="cancelled", cancelled_at=NOW() WHERE id = ? AND user_id = ?', 'ii', [$id, $_SESSION['user_id']]);
    flash('success', 'Booking cancelled. Your seats have been released.');
}
redirect('my_bookings.php');
