<?php
require_once __DIR__ . '/../includes/helpers.php';
requireLogin();

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    deleteArticle((int)$_POST['delete_id']);
    header('Location: /admin/articles.php?msg=' . urlencode('Article deleted.'));
    exit;
}

$pageTitle = 'Articles';
$activeNav = 'articles';

$filter = $_GET['status'] ?? 'all';
$search = trim($_GET['q'] ?? '');
$articles = getArticles();

if ($filter !== 'all') {
    $articles = array_values(array_filter($articles, fn($a) => $a['status'] === $filter));
}
if ($search) {
    $q = strtolower($search);
    $articles = array_values(array_filter($articles, fn($a) =>
        str_contains(strtolower($a['title']), $q) ||
        str_contains(strtolower($a['category']), $q)
    ));
}

include __DIR__ . '/../includes/admin-header.php';
?>
      <a href="/admin/article-edit.php" class="btn btn-gold">+ New Article</a>
<?php include __DIR__ . '/../includes/admin-body-open.php'; ?>

<?php if (isset($_GET['msg'])): ?>
  <div class="alert alert-success">✓ <?= h(urldecode($_GET['msg'])) ?></div>
<?php endif; ?>

<!-- Filters -->
<div style="display:flex;gap:1rem;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;">
  <div style="display:flex;gap:.4rem">
    <?php foreach (['all','published','draft'] as $s): ?>
      <a href="?status=<?= $s ?><?= $search ? '&q='.urlencode($search) : '' ?>"
         class="btn btn-outline btn-sm <?= $filter===$s ? 'btn-primary' : '' ?>"><?= ucfirst($s) ?></a>
    <?php endforeach; ?>
  </div>
  <form method="GET" style="display:flex;gap:.5rem;margin-left:auto">
    <input type="hidden" name="status" value="<?= h($filter) ?>">
    <input type="text" name="q" value="<?= h($search) ?>" placeholder="Search articles…"
           class="form-control" style="width:220px;padding:.45rem .9rem;font-size:.875rem">
    <button type="submit" class="btn btn-outline btn-sm">Search</button>
    <?php if ($search): ?><a href="?status=<?= h($filter) ?>" class="btn btn-outline btn-sm">✕</a><?php endif; ?>
  </form>
</div>

<div class="card">
  <div class="card-header">
    <h2><?= count($articles) ?> <?= $filter !== 'all' ? ucfirst($filter) . ' ' : '' ?>Article<?= count($articles)!==1?'s':'' ?></h2>
  </div>
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Title</th>
        <th>Category</th>
        <th>Status</th>
        <th>Author</th>
        <th>Views</th>
        <th>Updated</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($articles)): ?>
        <tr><td colspan="8" style="text-align:center;color:var(--muted);padding:3rem">No articles found.</td></tr>
      <?php else: ?>
      <?php foreach ($articles as $a): ?>
        <tr>
          <td style="color:var(--muted);font-size:.8rem"><?= $a['id'] ?></td>
          <td>
            <strong><?= h(mb_strimwidth($a['title'], 0, 55, '…')) ?></strong>
            <?php if ($a['featured']): ?> <span style="font-size:.75rem;color:var(--gold)">⭐</span><?php endif; ?>
          </td>
          <td><?= h($a['category']) ?></td>
          <td><span class="badge badge-<?= $a['status'] ?>"><?= h($a['status']) ?></span></td>
          <td><?= h($a['author_name']) ?></td>
          <td><?= $a['views'] ?></td>
          <td><?= timeAgo($a['updated_at']) ?></td>
          <td>
            <div style="display:flex;gap:.35rem">
              <a href="/admin/article-edit.php?id=<?= $a['id'] ?>" class="btn btn-outline btn-sm">Edit</a>
              <?php if ($a['status'] === 'published'): ?>
                <a href="/article.php?slug=<?= urlencode($a['slug']) ?>" target="_blank" class="btn btn-outline btn-sm">↗</a>
              <?php endif; ?>
              <form method="POST" onsubmit="return confirm('Delete this article?')">
                <input type="hidden" name="delete_id" value="<?= $a['id'] ?>">
                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
              </form>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
