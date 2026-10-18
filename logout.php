<?php
require_once __DIR__ . '/includes/functions.php';
unset($_SESSION['user_id']);          // keeps an admin session (if any) untouched
session_regenerate_id(true);
flash('info', 'You have been logged out.');
redirect('index.php');
