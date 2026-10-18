<?php
/**
 * Database + site configuration.
 * Defaults match a fresh XAMPP install (user "root", empty password).
 * If you changed your MySQL password, edit DB_PASS below.
 */
define('DB_HOST', 'localhost');
define('DB_PORT', 3306);          // change to 3307 etc. if XAMPP MySQL uses another port
define('DB_USER', 'root');
define('DB_PASS', '');            // XAMPP default: empty
define('DB_NAME', 'concert_ticket_db');

// URL path of this project. Folder is C:\xampp\htdocs\concert_tickets
define('BASE_URL', '/concert_tickets');

define('SITE_NAME', 'StageDoor');
define('MAX_TICKETS_PER_BOOKING', 10);
date_default_timezone_set('Asia/Kolkata');
