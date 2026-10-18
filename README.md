# StageDoor – Concert Ticket Management System

PHP + MySQL project for XAMPP (Windows). No external services, no cloud database, no Composer.
Built from the SRS "Concert Ticket Management System" v1.0.

## 1. Install (about 3 minutes)

1. **Copy the project folder** so the path is exactly:
   `C:\xampp\htdocs\concert_tickets`
   (the folder that contains `index.php`, `config`, `admin`, ... must be named `concert_tickets`).
2. Open the **XAMPP Control Panel** and **Start** both **Apache** and **MySQL**.
3. **Create the database:** open http://localhost/phpmyadmin → **Import** tab → choose
   `C:\xampp\htdocs\concert_tickets\database\concert_ticket_db.sql` → **Import**.
   (The file creates the `concert_ticket_db` database, all tables and sample data.)
4. Open **http://localhost/concert_tickets/**

Requires PHP 8.1 or newer (any current XAMPP download).

## 2. Database connection settings

All connection settings live in one file: `config/config.php`

| Setting      | Default             | Change it when…                                   |
|--------------|---------------------|---------------------------------------------------|
| `DB_HOST`    | `localhost`         | MySQL is on another machine                       |
| `DB_PORT`    | `3306`              | XAMPP MySQL runs on another port (e.g. `3307`)    |
| `DB_USER`    | `root`              | you use a different MySQL user                    |
| `DB_PASS`    | *(empty)*           | you set a MySQL password                          |
| `DB_NAME`    | `concert_ticket_db` | you imported the database under another name      |
| `BASE_URL`   | `/concert_tickets`  | you renamed the folder (use `''` if at web root)  |

## 3. Logins

| Role  | URL                                          | Email                | Password   |
|-------|----------------------------------------------|----------------------|------------|
| User  | http://localhost/concert_tickets/login.php   | user@example.com     | User@123   |
| Admin | http://localhost/concert_tickets/admin/login.php | admin@concert.local | Admin@123 |

You can also sign up as a new user. Change the admin password hash in the `admins` table for real use.

## 4. What is included (mapped to the SRS)

| SRS feature | Where |
|---|---|
| 4.1 Browse shows by city (REQ-1.1 – 1.3) | `index.php` (city filter, "No shows found" message) |
| 4.2 Favourite artists (REQ-2.1 – 2.3) | `artists.php`, `artist.php`, `favourite_toggle.php`, `favourites.php` |
| 4.3 Registration and login (REQ-3.1 – 3.4) | `register.php`, `login.php`, `logout.php` (passwords hashed with `password_hash`) |
| 4.4 Ticket booking (REQ-4.1 – 4.7) | `show.php`, `book.php`, `booking_confirmation.php`, `my_bookings.php`, `cancel_booking.php` |
| 4.5 Admin: shows and artists (REQ-5.1 – 5.4) | `admin/` (dashboard, shows, artists, bookings per show) |

Behaviour notes
* Guests can browse freely. Clicking **Book now** or **Add to favourites** while logged out sends them to the
  login page and returns them to the same page afterwards.
* Seats remaining = total seats − confirmed bookings. Cancelling a booking releases its seats automatically.
* **TBD-2 (last-seat race) is handled:** `book.php` runs in a transaction and locks the show row, so two
  people cannot both take the final seats.
* **No payment (TBD-1):** booking simply confirms. Each booking gets a unique ID like `CTM-4631F45A`.
* A single booking is limited to 10 tickets (`MAX_TICKETS_PER_BOOKING` in `config/config.php`).
* Security: prepared statements everywhere, hashed passwords, CSRF token on every form, output escaping,
  separate admin session, safe post-login redirects.

## 5. Folder layout

```
concert_tickets/
├─ index.php  show.php  book.php  booking_confirmation.php  my_bookings.php  cancel_booking.php
├─ artists.php  artist.php  favourites.php  favourite_toggle.php
├─ login.php  register.php  logout.php
├─ admin/        login, dashboard, shows, show_form, artists, artist_form, bookings
├─ includes/     db.php, functions.php, header/footer partials
├─ config/       config.php   <-- database settings
├─ database/     concert_ticket_db.sql
└─ assets/       css/style.css, js/app.js
```

## 6. Troubleshooting

* **"Database connection failed"** – MySQL is not started, the SQL file was not imported, or `config/config.php` has the wrong port/password.
* **Pages load without styling / links go to a 404** – the folder is not named `concert_tickets`; rename it or update `BASE_URL`.
* **Blank page** – enable errors in `C:\xampp\php\php.ini` (`display_errors=On`) and check `C:\xampp\apache\logs\error.log`.
* Re-importing the SQL file **resets all data** (it drops and recreates the tables).
