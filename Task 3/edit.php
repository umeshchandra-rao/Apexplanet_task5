<?php
require __DIR__ . '/db.php';
$pdo = get_pdo();

$id = isset($_GET['id']) && ctype_digit($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: index.php'); exit; }

$stmt = $pdo->prepare('SELECT * FROM posts WHERE id = :id');
$stmt->execute([':id' => $id]);
$post = $stmt->fetch();
if (!$post) { header('Location: index.php'); exit; }

$errors = [];
$title = $post['title'];
$content = $post['content'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');

    if ($title === '') { $errors[] = 'Title is required.'; }
    if ($content === '') { $errors[] = 'Content is required.'; }

    if (!$errors) {
        $stmt = $pdo->prepare('UPDATE posts SET title = :title, content = :content WHERE id = :id');
        $stmt->execute([':title' => $title, ':content' => $content, ':id' => $id]);
        header('Location: index.php');
        exit;
    }
}

$appTitle = 'Edit Post';
require __DIR__ . '/header.php';
?>

<div>
  <div class="nav-inner" style="margin:12px 0 16px;">
    <h1 class="page-title">Edit Post</h1>
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
      <button type="submit" class="btn primary">Save Changes</button>
      <a href="index.php" class="btn">Cancel</a>
    </div>
  </form>
</div>

<?php require __DIR__ . '/footer.php'; ?>


