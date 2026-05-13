<?php
require_once __DIR__ . '/../includes/helpers.php';
requireLogin();

$pageTitle = 'Dashboard';
$activeNav = 'dashboard';

$stats    = getStats();
$articles = getArticles();
$recent   = array_slice($articles, 0, 5);

include __DIR__ . '/../includes/admin-header.php';
?>
      <a href="/admin/article-edit.php" class="btn btn-gold">+ New Article</a>
<?php include __DIR__ . '/../includes/admin-body-open.php'; ?>

<?php if (isset($_GET['msg'])): ?>
  <div class="alert alert-success">✓ <?= h(urldecode($_GET['msg'])) ?></div>
<?php endif; ?>

<!-- Stats -->
<div class="grid-4" style="margin-bottom:2rem">
  <div class="stat-card">
    <div class="stat-icon">📄</div>
    <div class="stat-num"><?= $stats['total'] ?></div>
    <div class="stat-label">Total Articles</div>
  </div>
  <div class="stat-card">
    <div class="stat-icon">🌐</div>
    <div class="stat-num"><?= $stats['published'] ?></div>
    <div class="stat-label">Published</div>
  </div>
  <div class="stat-card">
    <div class="stat-icon">📝</div>
    <div class="stat-num"><?= $stats['drafts'] ?></div>
    <div class="stat-label">Drafts</div>
  </div>
  <div class="stat-card">
    <div class="stat-icon">👁</div>
    <div class="stat-num"><?= number_format($stats['views']) ?></div>
    <div class="stat-label">Total Views</div>
  </div>
</div>

<!-- Recent Articles -->
<div class="card">
  <div class="card-header">
    <h2>Recent Articles</h2>
    <a href="/admin/articles.php" class="btn btn-outline btn-sm">View all</a>
  </div>
  <table>
    <thead>
      <tr>
        <th>Title</th>
        <th>Category</th>
        <th>Status</th>
        <th>Views</th>
        <th>Date</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($recent as $a): ?>
        <tr>
          <td>
            <strong><?= h(mb_strimwidth($a['title'], 0, 50, '…')) ?></strong>
          </td>
          <td><?= h($a['category']) ?></td>
          <td>
            <span class="badge badge-<?= $a['status'] ?>"><?= h($a['status']) ?></span>
            <?php if ($a['featured']): ?><span class="badge" style="background:#fff3cd;color:#856404">⭐</span><?php endif; ?>
          </td>
          <td><?= $a['views'] ?></td>
          <td><?= timeAgo($a['created_at']) ?></td>
          <td>
            <a href="/admin/article-edit.php?id=<?= $a['id'] ?>" class="btn btn-outline btn-sm">Edit</a>
            <?php if ($a['status'] === 'published'): ?>
              <a href="/article.php?slug=<?= urlencode($a['slug']) ?>" target="_blank" class="btn btn-outline btn-sm">View</a>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
