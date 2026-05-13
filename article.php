<?php
require_once __DIR__ . '/includes/helpers.php';

$slug = trim($_GET['slug'] ?? '');
if (!$slug) { header('Location: /index.php'); exit; }

$article = getArticleBySlug($slug);
if (!$article || $article['status'] !== 'published') {
    http_response_code(404);
    echo '<h1>404 — Article not found</h1><p><a href="/index.php">← Back</a></p>';
    exit;
}

incrementViews($article['id']);

// Related articles
$all = getArticles(true);
$related = array_values(array_filter($all, fn($a) =>
    $a['id'] !== $article['id'] && $a['category'] === $article['category']
));
$related = array_slice($related, 0, 3);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= h($article['title']) ?> — Inkwell</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,400&family=DM+Sans:wght@300;400;500&family=DM+Serif+Text:ital@0;1&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  :root {
    --ink: #0f0e0c; --paper: #faf8f4; --cream: #f2ede4;
    --gold: #c9963a; --muted: #8a8070; --border: #ddd6c8;
  }
  body { font-family: 'DM Sans', sans-serif; background: var(--paper); color: var(--ink); }

  nav {
    position: sticky; top: 0; z-index: 100;
    background: rgba(250,248,244,.92); backdrop-filter: blur(12px);
    border-bottom: 1px solid var(--border);
  }
  .nav-inner {
    max-width: 900px; margin: 0 auto; padding: 1rem 2rem;
    display: flex; align-items: center; justify-content: space-between;
  }
  .nav-logo { font-family: 'Playfair Display', serif; font-size: 1.3rem; text-decoration: none; color: var(--ink); }
  .nav-logo span { color: var(--gold); }
  .nav-back { font-size: .875rem; color: var(--muted); text-decoration: none; }
  .nav-back:hover { color: var(--gold); }

  .article-wrap { max-width: 720px; margin: 0 auto; padding: 3rem 2rem 5rem; }

  .article-category {
    font-size: .78rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase;
    color: var(--gold); margin-bottom: .8rem; display: block;
  }
  .article-title {
    font-family: 'Playfair Display', serif;
    font-size: clamp(1.8rem, 5vw, 2.8rem);
    line-height: 1.15; margin-bottom: 1.2rem;
  }
  .article-meta {
    display: flex; gap: 1.2rem; align-items: center; flex-wrap: wrap;
    padding: 1rem 0; border-top: 1px solid var(--border); border-bottom: 1px solid var(--border);
    margin-bottom: 2.5rem; font-size: .85rem; color: var(--muted);
  }
  .article-meta strong { color: var(--ink); }
  .tags { display: flex; gap: .4rem; flex-wrap: wrap; margin-bottom: 2rem; }
  .tag {
    font-size: .78rem; padding: .25rem .65rem; border-radius: 50px;
    background: var(--cream); color: var(--muted); border: 1px solid var(--border);
  }

  .article-body {
    font-family: 'DM Serif Text', serif;
    font-size: 1.1rem; line-height: 1.85; color: #2a2720;
  }
  .article-body p { margin-bottom: 1.5em; }

  .related-section { max-width: 900px; margin: 0 auto; padding: 2rem 2rem 4rem; }
  .related-heading {
    font-family: 'Playfair Display', serif;
    font-size: 1.3rem; margin-bottom: 1.5rem;
    padding-top: 2rem; border-top: 1px solid var(--border);
  }
  .related-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1rem; }
  .related-card {
    background: #fff; border: 1px solid var(--border); border-radius: 12px; padding: 1.25rem;
    text-decoration: none; color: inherit; transition: transform .2s;
  }
  .related-card:hover { transform: translateY(-2px); }
  .related-card .cat { font-size: .75rem; font-weight: 700; color: var(--gold); text-transform: uppercase; letter-spacing: .08em; }
  .related-card h4 { font-family: 'Playfair Display', serif; font-size: 1rem; margin-top: .4rem; }

  footer { text-align: center; padding: 2rem; font-size: .82rem; color: var(--muted); border-top: 1px solid var(--border); }
  footer a { color: var(--gold); text-decoration: none; }
</style>
</head>
<body>

<nav>
  <div class="nav-inner">
    <a class="nav-logo" href="/index.php">✒ Ink<span>well</span></a>
    <a class="nav-back" href="/index.php">← All Articles</a>
  </div>
</nav>

<div class="article-wrap">
  <span class="article-category"><?= h($article['category']) ?></span>
  <h1 class="article-title"><?= h($article['title']) ?></h1>

  <div class="article-meta">
    <span>By <strong><?= h($article['author_name']) ?></strong></span>
    <span><?= date('F j, Y', strtotime($article['created_at'])) ?></span>
    <span>👁 <?= $article['views'] + 1 ?> views</span>
    <?php if (isLoggedIn()): ?>
      <a href="/admin/article-edit.php?id=<?= $article['id'] ?>" style="margin-left:auto;color:var(--gold);font-size:.82rem;">✏ Edit</a>
    <?php endif; ?>
  </div>

  <?php if (!empty($article['tags'])): ?>
    <div class="tags">
      <?php foreach ($article['tags'] as $tag): ?>
        <span class="tag">#<?= h($tag) ?></span>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <div class="article-body">
    <?php foreach (explode("\n", $article['content']) as $para): ?>
      <?php if (trim($para) !== ''): ?>
        <p><?= nl2br(h($para)) ?></p>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>
</div>

<?php if (!empty($related)): ?>
  <div class="related-section">
    <h2 class="related-heading">Related Articles</h2>
    <div class="related-grid">
      <?php foreach ($related as $r): ?>
        <a class="related-card" href="/article.php?slug=<?= urlencode($r['slug']) ?>">
          <div class="cat"><?= h($r['category']) ?></div>
          <h4><?= h($r['title']) ?></h4>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
<?php endif; ?>

<footer>&copy; <?= date('Y') ?> Inkwell &mdash; <a href="/index.php">Home</a></footer>
</body>
</html>
