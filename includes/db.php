<?php
require_once __DIR__ . '/../config/config.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    $db->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    die('<h2>Database connection failed</h2><p>Make sure MySQL is running in the XAMPP Control Panel, '
      . 'the database <code>' . htmlspecialchars(DB_NAME) . '</code> has been imported '
      . '(database/concert_ticket_db.sql), and the settings in <code>config/config.php</code> are correct.</p>');
}

/** Run a SELECT and return all rows. */
function db_all(string $sql, string $types = '', array $params = []): array {
    global $db;
    $st = $db->prepare($sql);
    if ($types !== '') $st->bind_param($types, ...$params);
    $st->execute();
    $res = $st->get_result();
    $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    $st->close();
    return $rows;
}

/** Run a SELECT and return the first row or null. */
function db_one(string $sql, string $types = '', array $params = []): ?array {
    $rows = db_all($sql, $types, $params);
    return $rows[0] ?? null;
}

/** Run INSERT/UPDATE/DELETE; returns affected rows. */
function db_exec(string $sql, string $types = '', array $params = []): int {
    global $db;
    $st = $db->prepare($sql);
    if ($types !== '') $st->bind_param($types, ...$params);
    $st->execute();
    $n = $st->affected_rows;
    $st->close();
    return $n;
}
