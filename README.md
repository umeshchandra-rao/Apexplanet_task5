## Internship5 PHP App
An end-to-end PHP/MySQL CRUD app with authentication, authorization, input validation, secure queries, and a simple like feature. Designed to run locally on XAMPP.
### Tech Stack
- **Server**: PHP 8+ (works with PHP 7.x as well)
- **Database**: MySQL/MariaDB (via XAMPP)
- **Web Server**: Apache (XAMPP)
- **Frontend**: Vanilla PHP templates, CSS, minimal JS
### Project Structure
```
├─ access_control.php     # Authorization utilities (role/permission checks)
├─ auth.php               # Auth helpers (session, login-required, current user)
├─ config.php             # App configuration (DB credentials, constants)
├─ create.php             # Create form + handler (C in CRUD)
├─ db.php                 # Database connection (PDO) and bootstrap
├─ delete.php             # Delete handler (D in CRUD)
├─ edit.php               # Edit form + update handler (U in CRUD)
├─ footer.php             # Shared footer partial
├─ header.php             # Shared header/nav partial
├─ index.php              # List/landing page (R in CRUD)
├─ js/
│  └─ validation.js      # Client-side form validation
├─ like.php               # Like/unlike handler for items
├─ login.php              # Login page + handler
├─ logout.php             # Logout endpoint
├─ README.md              # This documentation
├─ register.php           # Registration page + handler
├─ secure_queries.php     # Parameterized query helpers (PDO prepared statements)
├─ seed.php               # Seed script to populate demo data
├─ styles.css             # Global styles
├─ unauthorized.php       # Access denied page (403 UX)
└─ validation.php         # Server-side validation utilities
```
### Setup (XAMPP on Windows)
1. Clone or copy this folder into `C:\xampp\htdocs\internship5`.
2. Start Apache and MySQL from the XAMPP Control Panel.
3. Create database and user:
   - Open phpMyAdmin (`http://localhost/phpmyadmin`).
   - Create a database (e.g., `internship5`).
   - Update `config.php` with your DB name, user, and password.
4. Initialize schema/data:
   - Visit `http://localhost/internship5/seed.php` once (or run any provided SQL inside it manually) to create tables and seed demo data.
5. Open the app: `http://localhost/internship5/`.
### Configuration
- `config.php`: Central place for environment-like settings.
  - DB host, name, user, password
  - Optional app constants (e.g., session settings)
- `db.php`: Creates a PDO instance using values from `config.php` and sets safe defaults (error modes, charset, etc.).
### Authentication & Authorization
- `register.php`: Creates a new user with server-side validation.
- `login.php`: Authenticates user, starts session, sets user context.
- `logout.php`: Destroys session.
- `auth.php`: Helpers like `require_login()` and `current_user()`.
- `access_control.php`: Role/permission checks; used to guard CRUD and admin-only actions.
- `unauthorized.php`: Friendly 403 page when access is denied.
### CRUD
- `index.php`: Lists items (Read). May include pagination/search if implemented.
- `create.php`: Displays form and inserts a new item (Create).
- `edit.php`: Displays form and updates an existing item (Update).
- `delete.php`: Deletes an item (Delete).
### Likes
- `like.php`: Handles POST/GET to like or unlike an item, typically requires login.
### Database Schema (reference)
The exact schema may vary by your implementation in `seed.php`. Below is a reference schema that fits the app’s features. Adjust names and types as in your environment.
```sql
-- Users
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(191) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('user','admin') DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
);
-- Items (CRUD target)
CREATE TABLE items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  description TEXT,
  user_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_items_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
-- Likes
CREATE TABLE likes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  item_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_user_item (user_id, item_id),
  CONSTRAINT fk_likes_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_likes_item FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE
);
```
If your `seed.php` already creates tables, keep that as the source of truth and only use the above as documentation.
### Forms and Field Lists
- Registration (`register.php`):
  - Fields: `name` (required), `email` (required, email), `password` (required, min length), `confirm_password` (matches)
  - Validation: Client-side (`js/validation.js`) + server-side (`validation.php`)
  - Side effects: Inserts into `users` with `password_hash`
- Login (`login.php`):
  - Fields: `email` (required), `password` (required)
  - Validation: Server-side; verifies with `password_verify()`
  - Side effects: Starts session, stores user context
- Create Item (`create.php`):
  - Fields: `title` (required), `description` (optional)
  - Auth: Requires login
  - Side effects: Inserts into `items` with `user_id = current_user.id`
- Edit Item (`edit.php`):
  - Fields: `id` (hidden), `title`, `description`
  - Auth: Requires login and ownership or proper role per `access_control.php`
  - Side effects: Updates `items`
- Delete Item (`delete.php`):
  - Fields/params: `id`
  - Auth: Requires login and ownership/admin
  - Side effects: Deletes row in `items`
- Like (`like.php`):
  - Params: `item_id` (required), optional `action` (`like`|`unlike`) if implemented separately
  - Auth: Requires login
  - Side effects: Inserts into or deletes from `likes`
### Endpoints and Parameters
All endpoints are PHP pages. The app typically uses form posts and simple query strings.
- `GET /internship5/` → `index.php`
  - Query params: optional paging/search if implemented
  - Auth: public
- `GET /internship5/register.php` / `POST /internship5/register.php`
  - Body (POST): `name`, `email`, `password`, `confirm_password`
- `GET /internship5/login.php` / `POST /internship5/login.php`
  - Body (POST): `email`, `password`
- `POST /internship5/logout.php`
  - No body; ends session
- `GET /internship5/create.php` / `POST /internship5/create.php`
  - Body (POST): `title`, `description`
  - Auth: user
- `GET /internship5/edit.php?id=...` / `POST /internship5/edit.php`
  - Body (POST): `id`, `title`, `description`
  - Auth: owner/admin
- `POST /internship5/delete.php`
  - Body: `id`
  - Auth: owner/admin
- `POST /internship5/like.php`
  - Body: `item_id` (and optionally `action`)
  - Auth: user
### Validation & Security
- `validation.php`: Server-side input validation utilities (presence, length, formats).
- `js/validation.js`: Client-side guardrails for faster feedback (non-authoritative).
- `secure_queries.php`: Thin wrappers around prepared statements to prevent SQL injection.
- `db.php`: Uses PDO with prepared statements and safe defaults.
- Additional best practices to consider:
  - CSRF tokens on state-changing requests (`create`, `edit`, `delete`, `like`).
  - Output escaping to prevent XSS in `index.php`, `edit.php`, etc.
  - Password hashing via `password_hash()` and `password_verify()`.
### Layout & Styling
- `header.php` / `footer.php`: Shared layout partials included across pages.
- `styles.css`: Global styling, responsive base.
### Running the App
1. Ensure Apache and MySQL are running.
2. Visit `http://localhost/internship5/`.
3. Register an account, then log in.
4. Create, edit, and delete items; try the like feature.
### Common Issues
- Blank page or fatal error: Enable PHP error reporting or check Apache error log.
- DB connection errors: Verify `config.php` credentials and that MySQL is running.
- Session issues: Confirm PHP sessions are enabled and writable.
### Developer Notes
- Use prepared statements for all DB access (see `secure_queries.php`).
- Keep server-side validation authoritative; client-side is optional convenience.
- Wrap protected routes with `require_login()` and role checks.
### Maintenance
- To reset demo data, re-run `seed.php` or truncate tables as needed.
- Keep `config.php` out of source control if it contains secrets; consider a template like `config.example.php` for teams.
