<?php
require __DIR__ . '/db.php';
require_once __DIR__ . '/access_control.php';
require_once __DIR__ . '/auth.php';
$pdo = get_pdo();

// Require auth to delete
require_auth();

$id = isset($_GET['id']) && ctype_digit($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: index.php'); exit; }

$stmt = $pdo->prepare('SELECT * FROM posts WHERE id = :id');
$stmt->execute([':id' => $id]);
$post = $stmt->fetch();
if (!$post) { header('Location: index.php'); exit; }

// Authorization: only admins can delete any; editors/users only own
if (!can_access_resource('delete', $post)) {
  header('Location: unauthorized.php');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
        $del = $pdo->prepare('DELETE FROM posts WHERE id = :id');
        $del->execute([':id' => $id]);
    }
    header('Location: index.php');
    exit;
}

$appTitle = 'Delete Post';
require __DIR__ . '/header.php';
?>

<div>
  <h1 class="page-title">Delete Post</h1>
  <div class="alert error">
    Are you sure you want to delete "<?php echo htmlspecialchars($post['title']); ?>"?
  </div>
  <form method="post" style="display:flex; gap:8px;">
    <input type="hidden" name="confirm" value="yes">
    <button type="submit" class="btn danger">Yes, delete</button>
    <a class="btn" href="index.php">Cancel</a>
  </form>
</div>

<?php require __DIR__ . '/footer.php'; ?>


