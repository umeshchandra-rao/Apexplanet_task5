PHP CRUD Blog — Internship Task

Task: Set up and run a secure PHP blog with user authentication and full CRUD for posts.

🏁 Timeline:
3 Days

🎯 Objectives
• Configure a local PHP + MySQL environment
• Import the provided database schema
• Configure the application and run it locally
• Commit the project to a Git repository

🛠️ Steps to Success
1️⃣ Install a Local Server Environment
• Install XAMPP/WAMP/MAMP
• Start Apache and MySQL

2️⃣ Prepare the Database
• Open a MySQL client (phpMyAdmin or MySQL CLI)
• Import `database.sql` (creates database `blog` with `users` and `posts`)

3️⃣ Configure the Application
• Open `config.php`
• Set `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`
• Set `APP_BASE_URL` to the public base path

4️⃣ Run the Application
• Serve this project with your web server
• Visit the base URL configured in `APP_BASE_URL`

5️⃣ Use the Blog
• Register at `register.php`, then log in at `login.php`
• Create, edit, delete, and view posts from the home page

6️⃣ Version Control
• Initialize Git in the project folder
• Commit core files (e.g., `index.php`, `config.php`, `README.md`)
• Create a remote repository and push your commits

📦 Deliverables
• A running local instance of the blog
• Database created from `database.sql`
• Git repository containing the project and this README

🔒 Notes on Security
• Password hashing (bcrypt), CSRF protection, prepared statements, and output escaping are implemented

Happy Coding!
