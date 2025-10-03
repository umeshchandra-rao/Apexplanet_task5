<?php
require __DIR__ . '/db.php';

$pdo = get_pdo();

// Pagination parameters
$perPage = 5; // posts per page
$page = isset($_GET['page']) && ctype_digit($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) { $page = 1; }
$offset = ($page - 1) * $perPage;

// Search parameters
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$where = '';
$params = [];

if ($q !== '') {
    // Try fulltext, fallback to LIKE if fulltext returns zero
    $where = 'WHERE MATCH(title, content) AGAINST (:q IN BOOLEAN MODE)';
    $params[':q'] = $q . '*';
    $countSql = "SELECT COUNT(*) FROM posts $where";
    $stmtCount = $pdo->prepare($countSql);
    $stmtCount->execute($params);
    $total = (int)$stmtCount->fetchColumn();

    if ($total === 0) {
        $where = 'WHERE title LIKE :likeq OR content LIKE :likeq';
        $params = [':likeq' => '%' . $q . '%'];
        $countSql = "SELECT COUNT(*) FROM posts $where";
        $stmtCount = $pdo->prepare($countSql);
        $stmtCount->execute($params);
        $total = (int)$stmtCount->fetchColumn();
    }
} else {
    $countSql = 'SELECT COUNT(*) FROM posts';
    $total = (int)$pdo->query($countSql)->fetchColumn();
}

$totalPages = max(1, (int)ceil($total / $perPage));
if ($page > $totalPages) { $page = $totalPages; $offset = ($page - 1) * $perPage; }

// Fetch posts
if ($q !== '' && strpos($where, 'MATCH(') !== false) {
    $sql = "SELECT * FROM posts $where ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    foreach ($params as $k => $v) { $stmt->bindValue($k, $v); }
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
} else if ($q !== '') {
    $sql = "SELECT * FROM posts $where ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    foreach ($params as $k => $v) { $stmt->bindValue($k, $v); }
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
} else {
    $sql = 'SELECT * FROM posts ORDER BY created_at DESC LIMIT :limit OFFSET :offset';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
}

$posts = $stmt->fetchAll();

$appTitle = 'Internship Blog';
require __DIR__ . '/header.php';
?>

<div>
  <div class="nav-inner" style="margin:12px 0 16px;">
    <h1 class="page-title">Posts</h1>
    <a href="create.php" class="btn primary">Create Post</a>
  </div>

  <form method="get" class="form" style="display:flex; gap:8px; align-items:center; margin-bottom:16px;">
    <input type="text" name="q" value="<?php echo htmlspecialchars($q); ?>" placeholder="Search posts by title or content...">
    <?php if ($page !== 1) { ?>
      <input type="hidden" name="page" value="1">
    <?php } ?>
    <button class="btn primary" type="submit">Search</button>
  </form>

  <?php if (count($posts) === 0) { ?>
    <div class="alert error">No posts found.</div>
  <?php } ?>

  <div class="card-list">
    <?php foreach ($posts as $post) { ?>
      <div class="card">
        <h3><?php echo htmlspecialchars($post['title']); ?></h3>
        <div class="muted" style="font-size:13px; margin-bottom:8px;">
          <?php echo date('M d, Y h:i A', strtotime($post['created_at'])); ?>
        </div>
        <div class="content"><?php echo nl2br(htmlspecialchars(mb_strimwidth($post['content'], 0, 240, '...'))); ?></div>
        <div style="margin-top:12px; display:flex; gap:8px;">
          <a href="edit.php?id=<?php echo (int)$post['id']; ?>" class="btn">Edit</a>
          <a href="delete.php?id=<?php echo (int)$post['id']; ?>" class="btn danger">Delete</a>
        </div>
      </div>
    <?php } ?>
  </div>

  <?php if ($totalPages > 1) { ?>
    <div style="display:flex; gap:8px; align-items:center; justify-content:center; margin-top:18px; flex-wrap:wrap;">
      <?php
        $queryBase = [];
        if ($q !== '') { $queryBase['q'] = $q; }
        $buildUrl = function(int $targetPage) use ($queryBase) {
          $query = http_build_query(array_merge($queryBase, ['page' => $targetPage]));
          return 'index.php' . ($query ? ('?' . $query) : '');
        };
      ?>
      <a class="btn<?php echo $page <= 1 ? '' : ''; ?>" href="<?php echo $page <= 1 ? '#' : $buildUrl($page - 1); ?>">Previous</a>
      <?php for ($p = 1; $p <= $totalPages; $p++) { ?>
        <a class="btn<?php echo $p === $page ? ' primary' : ''; ?>" href="<?php echo $buildUrl($p); ?>"><?php echo $p; ?></a>
      <?php } ?>
      <a class="btn" href="<?php echo $page >= $totalPages ? '#' : $buildUrl($page + 1); ?>">Next</a>
    </div>
  <?php } ?>
</div>

<?php require __DIR__ . '/footer.php'; ?>


