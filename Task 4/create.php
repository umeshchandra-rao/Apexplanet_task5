<?php
require __DIR__ . '/db.php';
require_once __DIR__ . '/access_control.php';
require_once __DIR__ . '/auth.php';
$pdo = get_pdo();

// Require authentication (any logged-in role can create per RBAC rules)
require_auth();

$errors = [];
$title = '';
$content = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');

    if ($title === '') { $errors[] = 'Title is required.'; }
    if ($content === '') { $errors[] = 'Content is required.'; }

    if (!$errors) {
        $current = get_logged_in_user();
        $stmt = $pdo->prepare('INSERT INTO posts (title, content, user_id) VALUES (:title, :content, :user_id)');
        $stmt->execute([
            ':title' => $title,
            ':content' => $content,
            ':user_id' => $current ? (int)$current['id'] : null,
        ]);
        header('Location: index.php');
        exit;
    }
}

$appTitle = 'Create Post';
require __DIR__ . '/header.php';
?>

<div>
  <div class="nav-inner" style="margin:12px 0 16px;">
    <h1 class="page-title">Create Post</h1>
    <a href="index.php" class="btn">Back</a>
  </div>

  <?php if ($errors) { ?>
    <div class="alert error">
      <ul style="margin:0; padding-left:16px;">
        <?php foreach ($errors as $e) { ?><li><?php echo htmlspecialchars($e); ?></li><?php } ?>
      </ul>
    </div>
  <?php } ?>

  <form method="post" class="form" style="display:grid; gap:12px;">
    <div>
      <label>Title</label>
      <input type="text" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
    </div>
    <div>
      <label>Content</label>
      <textarea name="content" rows="8" required><?php echo htmlspecialchars($content); ?></textarea>
    </div>
    <div style="display:flex; gap:8px;">
      <button type="submit" class="btn primary">Create</button>
      <a href="index.php" class="btn">Cancel</a>
    </div>
  </form>
</div>

<?php require __DIR__ . '/footer.php'; ?>


