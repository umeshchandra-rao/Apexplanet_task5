<?php
  $appTitle = $appTitle ?? 'Internship Blog';
  require_once __DIR__ . '/auth.php';
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($appTitle); ?></title>
    <link rel="stylesheet" href="/internship4/styles.css">
  </head>
  <body>
    <div class="navbar">
      <div class="container nav-inner">
        <a class="brand" href="/internship4/index.php">Internship Blog</a>
        <div class="nav-links">
          <?php if (is_logged_in()) { ?>
            <a class="btn primary" href="/internship4/logout.php">Logout</a>
          <?php } else { ?>
            <a href="/internship4/login.php">Login</a>
            <a href="/internship4/register.php">Register</a>
          <?php } ?>
        </div>
      </div>
    </div>
    <main class="container narrow">


