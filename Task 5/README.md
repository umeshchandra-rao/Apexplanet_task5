## Internship4 PHP App

A simple PHP/MySQL CRUD app with authentication, likes, and basic access control. Runs locally on XAMPP for Windows.

### What's included (this folder)
- `index.php`: Landing/listing page
- `create.php`: Create a record
- `edit.php`: Edit a record
- `delete.php`: Delete a record
- `login.php`: User sign in
- `register.php`: User sign up
- `logout.php`: Destroy session and log out
- `like.php`: Like/unlike endpoint
- `access_control.php`: Route/role protection helpers
- `unauthorized.php`: Fallback page when access is denied
- `config.php`: App configuration (DB name, credentials, settings)
- `db.php`: Database connection helper
- `secure_queries.php`: Prepared statements and safe DB utilities
- `validation.php`: Input validation helpers
- `header.php` / `footer.php`: Shared layout partials
- `styles.css`: Base styles
- `js/`: Frontend scripts
- `database.sql`: MySQL schema (import into phpMyAdmin)
- `seed.php`: Optional sample data seed
- `README.md`: This guide

### Requirements
- XAMPP (Apache + MySQL) on Windows
- PHP 8.x (bundled with recent XAMPP)

### Step-by-step setup (Windows + XAMPP)
1) Put this folder at `C:\xampp\htdocs\internship4`.
2) Start Apache and MySQL from the XAMPP Control Panel.
3) Create the database:
   - Open `http://localhost/phpmyadmin/`.
   - Create a database named `internship4` (or your preferred name).
4) Import the schema:
   - In phpMyAdmin, select the database you created.
   - Go to Import and choose `database.sql`, then run the import.
5) Configure the database connection:
   - Open `config.php` (and/or `db.php`).
   - Set host, username, password, and database name.
   - On fresh XAMPP: host `localhost`, user `root`, password empty, db `internship4`.
6) (Optional) Seed sample data:
   - Visit `http://localhost/internship4/seed.php` once.
7) Verify the app loads:
   - Visit `http://localhost/internship4/`.
8) Create an account and sign in:
   - Register at `register.php`, then log in at `login.php`.

### How to use (basic flow)
1) After login, use the UI to create, edit, and delete records.
2) Use like actions via the interface (handled by `like.php`).
3) Log out via `logout.php` when done.

### Security notes
- Use prepared statements for all DB calls (`secure_queries.php`).
- Validate and sanitize input (`validation.php`).
- Protect restricted routes with `access_control.php`; send unauthorized users to `unauthorized.php`.
- Start sessions early on pages that need them and regenerate IDs on login.

### Troubleshooting
- Database connection errors:
  - Ensure MySQL is running, credentials in `config.php`/`db.php` are correct, and `database.sql` is imported.
- CSS/JS not loading:
  - Check paths in `header.php`/`footer.php` and file existence.
- Redirects back to login:
  - Verify `session_start()` runs before output on pages that require sessions.

### License
For educational use. Add a license if you plan to distribute.


