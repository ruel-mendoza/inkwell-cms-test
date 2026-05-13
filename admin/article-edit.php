<?php
require_once __DIR__ . '/../includes/helpers.php';
requireLogin();

$id      = (int)($_GET['id'] ?? 0);
$article = $id ? getArticleById($id) : null;
$isNew   = $article === null;
$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title    = trim($_POST['title'] ?? '');
    $content  = trim($_POST['content'] ?? '');
    $excerpt  = trim($_POST['excerpt'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $status   = in_array($_POST['status'] ?? '', ['published','draft']) ? $_POST['status'] : 'draft';
    $featured = isset($_POST['featured']) ? true : false;
    $tagsRaw  = trim($_POST['tags'] ?? '');
    $tags     = array_values(array_filter(array_map('trim', explode(',', $tagsRaw))));

    if (!$title)   $error = 'Title is required.';
    if (!$content) $error = 'Content is required.';
    if (!$category) $error = 'Category is required.';

    if (!$error) {
        $user = currentUser();
        $slug = slugify($title);
        // Ensure unique slug
        $existing = getArticleBySlug($slug);
        if ($existing && (!$article || $existing['id'] !== $article['id'])) {
            $slug .= '-' . ($id ?: time());
        }
        $data = [
            'id'          => $id ?: 0,
            'title'       => $title,
            'slug'        => $slug,
            'excerpt'     => $excerpt ?: mb_strimwidth(strip_tags($content), 0, 160, '…'),
            'content'     => $content,
            'category'    => $category,
            'tags'        => $tags,
            'status'      => $status,
            'featured'    => $featured,
            'author_id'   => $user['id'],
            'author_name' => $user['name'],
        ];
        saveArticle($data);
        header('Location: /admin/articles.php?msg=' . urlencode($isNew ? 'Article created!' : 'Article updated!'));
        exit;
    }
}

$pageTitle = $isNew ? 'New Article' : 'Edit Article';
$activeNav = $isNew ? 'new-article' : 'articles';

include __DIR__ . '/../includes/admin-header.php';
?>
      <a href="/admin/articles.php" class="btn btn-outline">← Back</a>
      <?php if (!$isNew && $article['status'] === 'published'): ?>
        <a href="/article.php?slug=<?= urlencode($article['slug']) ?>" target="_blank" class="btn btn-outline">↗ View</a>
      <?php endif; ?>
<?php include __DIR__ . '/../includes/admin-body-open.php'; ?>

<?php if ($error): ?>
  <div class="alert alert-error">⚠ <?= h($error) ?></div>
<?php endif; ?>

<form method="POST" action="">
  <?php if (!$isNew): ?>
    <input type="hidden" name="id" value="<?= $article['id'] ?>">
  <?php endif; ?>

  <div class="grid-2" style="gap:2rem;align-items:start">
    <!-- Left: main content -->
    <div>
      <div class="form-group">
        <label class="form-label">Title *</label>
        <input type="text" name="title" class="form-control"
               value="<?= h($_POST['title'] ?? $article['title'] ?? '') ?>"
               placeholder="Enter article title…" required autofocus>
      </div>

      <div class="form-group">
        <label class="form-label">Excerpt (short summary)</label>
        <textarea name="excerpt" class="form-control" rows="3"
                  placeholder="A brief description shown on the listing page…"><?= h($_POST['excerpt'] ?? $article['excerpt'] ?? '') ?></textarea>
      </div>

      <div class="form-group">
        <label class="form-label">Content *</label>
        <textarea name="content" class="form-control" rows="16"
                  style="font-size:.95rem;font-family:Georgia,serif;line-height:1.8"
                  placeholder="Write your article content here…" required><?= h($_POST['content'] ?? $article['content'] ?? '') ?></textarea>
        <small style="color:var(--muted);font-size:.8rem">Plain text. Use blank lines to separate paragraphs.</small>
      </div>
    </div>

    <!-- Right: meta -->
    <div>
      <div class="card" style="padding:1.25rem">
        <h3 style="font-size:.9rem;font-weight:600;margin-bottom:1rem;color:var(--muted);text-transform:uppercase;letter-spacing:.06em">Publish Settings</h3>

        <div class="form-group">
          <label class="form-label">Status</label>
          <select name="status" class="form-control">
            <option value="draft"     <?= ($_POST['status'] ?? $article['status'] ?? 'draft') === 'draft'     ? 'selected' : '' ?>>Draft</option>
            <option value="published" <?= ($_POST['status'] ?? $article['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
          </select>
        </div>

        <div class="form-group">
          <label style="display:flex;align-items:center;gap:.6rem;cursor:pointer">
            <input type="checkbox" name="featured" value="1"
                   <?= ($_POST['featured'] ?? $article['featured'] ?? false) ? 'checked' : '' ?>
                   style="width:16px;height:16px;accent-color:var(--gold)">
            <span style="font-size:.9rem">⭐ Featured article</span>
          </label>
        </div>

        <hr style="border:none;border-top:1px solid var(--border);margin:1rem 0">

        <div class="form-group">
          <label class="form-label">Category *</label>
          <input type="text" name="category" class="form-control" list="cat-suggestions"
                 value="<?= h($_POST['category'] ?? $article['category'] ?? '') ?>"
                 placeholder="e.g. Technology" required>
          <datalist id="cat-suggestions">
            <?php foreach (getCategories() as $cat): ?>
              <option value="<?= h($cat) ?>">
            <?php endforeach; ?>
          </datalist>
        </div>

        <div class="form-group">
          <label class="form-label">Tags (comma-separated)</label>
          <input type="text" name="tags" class="form-control"
                 value="<?= h($_POST['tags'] ?? implode(', ', $article['tags'] ?? [])) ?>"
                 placeholder="tag1, tag2, tag3">
        </div>

        <hr style="border:none;border-top:1px solid var(--border);margin:1rem 0">

        <?php if (!$isNew): ?>
          <div style="font-size:.8rem;color:var(--muted);margin-bottom:1rem;line-height:1.7">
            <div>Created: <?= date('M j, Y g:i A', strtotime($article['created_at'])) ?></div>
            <div>Updated: <?= date('M j, Y g:i A', strtotime($article['updated_at'])) ?></div>
            <div>Views: <?= $article['views'] ?></div>
            <div>Author: <?= h($article['author_name']) ?></div>
          </div>
        <?php endif; ?>

        <button type="submit" class="btn btn-gold" style="width:100%">
          <?= $isNew ? '✓ Publish Article' : '✓ Save Changes' ?>
        </button>

        <?php if (!$isNew): ?>
          <div style="margin-top:.75rem;text-align:center">
            <form method="POST" action="/admin/articles.php" onsubmit="return confirm('Delete this article permanently?')" style="display:inline">
              <input type="hidden" name="delete_id" value="<?= $article['id'] ?>">
              <button type="submit" class="btn btn-danger btn-sm">Delete Article</button>
            </form>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</form>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
