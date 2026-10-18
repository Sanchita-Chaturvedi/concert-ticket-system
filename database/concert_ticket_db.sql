-- Concert Ticket Management System - database schema + sample data
-- Import via phpMyAdmin (http://localhost/phpmyadmin -> Import) or:
--   C:\xampp\mysql\bin\mysql.exe -u root < concert_ticket_db.sql

CREATE DATABASE IF NOT EXISTS concert_ticket_db
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE concert_ticket_db;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS favourites, bookings, shows, artists, admins, users;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(100) NOT NULL,
  email         VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE admins (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(100) NOT NULL,
  email         VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE artists (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name       VARCHAR(120) NOT NULL,
  genre      VARCHAR(60)  NOT NULL DEFAULT '',
  bio        TEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE shows (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  artist_id   INT UNSIGNED NOT NULL,
  city        VARCHAR(80)  NOT NULL,
  venue       VARCHAR(150) NOT NULL,
  show_date   DATETIME     NOT NULL,
  price       DECIMAL(10,2) NOT NULL,
  total_seats INT UNSIGNED NOT NULL,
  created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_city (city),
  INDEX idx_date (show_date),
  CONSTRAINT fk_show_artist FOREIGN KEY (artist_id) REFERENCES artists(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE bookings (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  booking_ref VARCHAR(30) NOT NULL UNIQUE,
  user_id     INT UNSIGNED NOT NULL,
  show_id     INT UNSIGNED NOT NULL,
  quantity    INT UNSIGNED NOT NULL,
  unit_price  DECIMAL(10,2) NOT NULL,
  status      ENUM('confirmed','cancelled') NOT NULL DEFAULT 'confirmed',
  booked_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  cancelled_at DATETIME NULL,
  INDEX idx_show_status (show_id, status),
  CONSTRAINT fk_booking_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_booking_show FOREIGN KEY (show_id) REFERENCES shows(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE favourites (
  user_id   INT UNSIGNED NOT NULL,
  artist_id INT UNSIGNED NOT NULL,
  added_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, artist_id),
  CONSTRAINT fk_fav_user   FOREIGN KEY (user_id)   REFERENCES users(id)   ON DELETE CASCADE,
  CONSTRAINT fk_fav_artist FOREIGN KEY (artist_id) REFERENCES artists(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------- Sample data ----------
-- Admin login:  admin@concert.local / Admin@123
INSERT INTO admins (name, email, password_hash) VALUES
('Site Admin', 'admin@concert.local', '$2y$10$db5oqnJFLoCMdUw9oMrQ8u9GnLz1AJOba42Dw3GPR8fHVL8T1JbcC');

-- Demo user:    user@example.com / User@123
INSERT INTO users (name, email, password_hash) VALUES
('Demo User', 'user@example.com', '$2y$10$vzkCxWiEeBmSoWUOPGHV0OutqgA.D.xikqalMjriHxXQLj8pxogaa');

INSERT INTO artists (name, genre, bio) VALUES
('Arijit Singh',   'Bollywood',  'Playback singer known for soulful romantic ballads and sold-out stadium tours.'),
('Diljit Dosanjh', 'Punjabi Pop','Singer and actor who blends Punjabi folk with high-energy pop and hip-hop.'),
('Prateek Kuhad',  'Indie Folk', 'Singer-songwriter famous for gentle, acoustic love songs.'),
('The Local Train','Rock',       'Hindi rock band from Chandigarh with a loyal live following.'),
('Nucleya',        'Electronic', 'Bass-heavy electronic producer mixing desi sounds with club beats.'),
('Ritviz',         'Electronic Pop', 'Pune-born producer and singer with an upbeat, melodic dance sound.');

-- Dates are relative to the import date so sample shows are always upcoming.
INSERT INTO shows (artist_id, city, venue, show_date, price, total_seats) VALUES
(1, 'Delhi',     'Jawaharlal Nehru Stadium',        DATE_ADD(NOW(), INTERVAL 20 DAY), 2500.00, 500),
(1, 'Mumbai',    'MMRDA Grounds, BKC',              DATE_ADD(NOW(), INTERVAL 35 DAY), 3000.00, 600),
(2, 'Delhi',     'Indira Gandhi Arena',             DATE_ADD(NOW(), INTERVAL 28 DAY), 2200.00, 400),
(2, 'Bengaluru', 'Palace Grounds',                  DATE_ADD(NOW(), INTERVAL 50 DAY), 2400.00, 450),
(3, 'Bengaluru', 'Phoenix Marketcity Amphitheatre', DATE_ADD(NOW(), INTERVAL 15 DAY), 1500.00, 200),
(3, 'Manipal',   'Manipal Auditorium',              DATE_ADD(NOW(), INTERVAL 40 DAY), 800.00,  150),
(4, 'Mumbai',    'Famous Studios, Mahalaxmi',       DATE_ADD(NOW(), INTERVAL 22 DAY), 1200.00, 250),
(4, 'Delhi',     'Hard Rock Cafe, Connaught Place', DATE_ADD(NOW(), INTERVAL 12 DAY), 900.00,  120),
(5, 'Mumbai',    'NSCI Dome, Worli',                DATE_ADD(NOW(), INTERVAL 60 DAY), 1800.00, 500),
(5, 'Bengaluru', 'Embassy International Riding School', DATE_ADD(NOW(), INTERVAL 45 DAY), 1700.00, 350),
(6, 'Manipal',   'MIT Open Air Theatre',            DATE_ADD(NOW(), INTERVAL 30 DAY), 600.00,  300),
(6, 'Delhi',     'Siri Fort Auditorium',            DATE_ADD(NOW(), INTERVAL 55 DAY), 1400.00, 280);

-- Sample bookings and favourites for the demo user
INSERT INTO bookings (booking_ref, user_id, show_id, quantity, unit_price, status) VALUES
('CTM-DEMO0001', 1, 5, 2, 1500.00, 'confirmed'),
('CTM-DEMO0002', 1, 8, 1, 900.00,  'confirmed');
INSERT INTO favourites (user_id, artist_id) VALUES (1, 3), (1, 4);
