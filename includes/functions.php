<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/db.php';

/* Fallbacks in case the mbstring extension is disabled (it is enabled by default in XAMPP). */
if (!function_exists('mb_strlen'))      { function mb_strlen($s)            { return strlen($s); } }
if (!function_exists('mb_substr'))      { function mb_substr($s, $a, $l = null) { return $l === null ? substr($s, $a) : substr($s, $a, $l); } }
if (!function_exists('mb_strtoupper'))  { function mb_strtoupper($s)       { return strtoupper($s); } }

function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function url(string $path = ''): string { return BASE_URL . '/' . ltrim($path, '/'); }
function redirect(string $path): never {
    header('Location: ' . (preg_match('#^https?://#', $path) ? $path : url($path)));
    exit;
}
function money($n): string { return '₹' . number_format((float)$n, 0); }
function fmt_date($d): string { return date('D, d M Y', strtotime($d)); }
function fmt_time($d): string { return date('g:i A', strtotime($d)); }

/* ---------- Flash messages ---------- */
function flash(string $type, string $msg): void { $_SESSION['flash'][] = ['type' => $type, 'msg' => $msg]; }
function flashes(): array { $f = $_SESSION['flash'] ?? []; unset($_SESSION['flash']); return $f; }

/* ---------- CSRF (basic form checks) ---------- */
function csrf_token(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function csrf_field(): string { return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">'; }
function csrf_check(): void {
    $ok = isset($_POST['csrf'], $_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $_POST['csrf']);
    if (!$ok) { http_response_code(400); die('Invalid form submission. Go back, refresh the page and try again.'); }
}
function require_post(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('index.php'); }
    csrf_check();
}

/* ---------- Auth: users ---------- */
function is_logged_in(): bool { return !empty($_SESSION['user_id']); }
function current_user(): ?array {
    static $u = false;
    if ($u === false) $u = is_logged_in() ? db_one('SELECT id,name,email FROM users WHERE id=?', 'i', [$_SESSION['user_id']]) : null;
    return $u;
}
/** Only allow redirects to paths inside this site. */
function safe_next(?string $next): string {
    $next = (string)$next;
    if ($next === '' || str_contains($next, '//') || str_contains($next, '\\') || preg_match('#^[a-z]+:#i', $next)) return '';
    $next = ltrim($next, '/');
    return str_starts_with($next, 'admin') ? '' : $next;
}
function require_login(string $returnTo = ''): void {
    if (!is_logged_in()) {
        $returnTo = $returnTo ?: ltrim(substr($_SERVER['REQUEST_URI'], strlen(BASE_URL)), '/');
        flash('info', 'Please log in or sign up to continue.');
        redirect('login.php?next=' . urlencode($returnTo));
    }
}

/* ---------- Auth: admin (separate session key) ---------- */
function is_admin(): bool { return !empty($_SESSION['admin_id']); }
function require_admin(): void {
    if (!is_admin()) { redirect('admin/login.php'); }
}

/* ---------- Shows ---------- */
const SHOW_SELECT = "SELECT s.*, a.name AS artist_name, a.genre,
    (s.total_seats - COALESCE((SELECT SUM(b.quantity) FROM bookings b WHERE b.show_id = s.id AND b.status = 'confirmed'), 0)) AS seats_remaining
  FROM shows s JOIN artists a ON a.id = s.artist_id ";

function seats_sold(array $show): int { return (int)$show['total_seats'] - (int)$show['seats_remaining']; }

function cities(): array {
    return array_column(db_all('SELECT DISTINCT city FROM shows ORDER BY city'), 'city');
}
function initials(string $name): string {
    $p = preg_split('/\s+/', trim($name)); $s = '';
    foreach (array_slice($p, 0, 2) as $w) $s .= mb_strtoupper(mb_substr($w, 0, 1));
    return $s;
}
