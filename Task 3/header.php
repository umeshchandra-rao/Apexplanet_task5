<?php
  $appTitle = $appTitle ?? 'Internship Blog';
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($appTitle); ?></title>
    <link rel="stylesheet" href="/internship3/styles.css">
  </head>
  <body>
    <div class="navbar">
      <div class="container nav-inner">
        <a class="brand" href="/internship3/index.php">Internship Blog</a>
        <div class="nav-links">
          <a href="/internship3/index.php">Home</a>
          <a class="btn primary" href="/internship3/create.php">Create</a>
        </div>
      </div>
    </div>
    <main class="container narrow">


