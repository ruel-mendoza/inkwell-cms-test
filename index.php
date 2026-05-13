<?php
require_once __DIR__ . '/includes/helpers.php';

$category = trim($_GET['cat'] ?? '');
$search   = trim($_GET['q']   ?? '');
$articles = getArticles(true);
$categories = getCategories();

// Filter
if ($category) {
    $articles = array_values(array_filter($articles, fn($a) => $a['category'] === $category));
}
if ($search) {
    $q = strtolower($search);
    $articles = array_values(array_filter($articles, fn($a) =>
        str_contains(strtolower($a['title']), $q) ||
        str_contains(strtolower($a['excerpt']), $q)
    ));
}

$featured  = array_values(array_filter($articles, fn($a) => $a['featured']));
$regular   = array_values(array_filter($articles, fn($a) => !$a['featured']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Inkwell — Articles & Records</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --ink:     #0f0e0c;
    --paper:   #faf8f4;
    --cream:   #f2ede4;
    --gold:    #c9963a;
    --muted:   #8a8070;
    --border:  #ddd6c8;
    --white:   #ffffff;
    --radius:  14px;
  }

  html { scroll-behavior: smooth; }
  body {
    font-family: 'DM Sans', sans-serif;
    background: var(--paper);
    color: var(--ink);
    line-height: 1.6;
  }

  /* NAV */
  nav {
    position: sticky; top: 0; z-index: 100;
    background: rgba(250,248,244,.92);
    backdrop-filter: blur(12px);
    border-bottom: 1px solid var(--border);
  }
  .nav-inner {
    max-width: 1200px; margin: 0 auto;
    padding: 1rem 2rem;
    display: flex; align-items: center; gap: 2rem;
  }
  .nav-logo {
    font-family: 'Playfair Display', serif;
    font-size: 1.4rem;
    color: var(--ink);
    text-decoration: none;
    display: flex; align-items: center; gap: .5rem;
    flex-shrink: 0;
  }
  .nav-logo span { color: var(--gold); }
  .nav-search {
    flex: 1; max-width: 400px;
    position: relative;
  }
  .nav-search input {
    width: 100%;
    padding: .55rem 1rem .55rem 2.4rem;
    border: 1.5px solid var(--border);
    border-radius: 50px;
    background: var(--white);
    font-family: 'DM Sans', sans-serif;
    font-size: .875rem;
    color: var(--ink);
    outline: none;
    transition: border-color .2s;
  }
  .nav-search input:focus { border-color: var(--gold); }
  .nav-search::before {
    content: '🔍';
    position: absolute; left: .8rem; top: 50%;
    transform: translateY(-50%);
    font-size: .8rem; opacity: .5; pointer-events: none;
  }
  .nav-links { margin-left: auto; display: flex; gap: 1rem; }
  .nav-links a {
    font-size: .875rem; font-weight: 500;
    color: var(--muted); text-decoration: none;
    padding: .4rem .8rem; border-radius: 8px;
    transition: color .2s, background .2s;
  }
  .nav-links a:hover { color: var(--ink); background: var(--cream); }
  .btn-admin {
    background: var(--ink) !important;
    color: var(--white) !important;
    border-radius: 8px;
  }
  .btn-admin:hover { background: #2a2720 !important; }

  /* HERO */
  .hero {
    max-width: 1200px; margin: 0 auto;
    padding: 4rem 2rem 2rem;
    display: grid; grid-template-columns: 1.4fr 1fr; gap: 2rem; align-items: center;
  }
  .hero-text h2 {
    font-family: 'Playfair Display', serif;
    font-size: clamp(2rem, 5vw, 3.5rem);
    line-height: 1.1;
    margin-bottom: 1rem;
  }
  .hero-text h2 em { font-style: italic; color: var(--gold); }
  .hero-text p { color: var(--muted); font-size: 1rem; max-width: 380px; }
  .hero-stats {
    display: flex; gap: 2rem;
    padding: 1.5rem 2rem;
    background: var(--white);
    border-radius: var(--radius);
    border: 1px solid var(--border);
    box-shadow: 0 4px 20px rgba(0,0,0,.04);
  }
  .stat { text-align: center; }
  .stat-num {
    font-family: 'Playfair Display', serif;
    font-size: 2rem;
    color: var(--ink);
    display: block;
  }
  .stat-lbl { font-size: .8rem; color: var(--muted); text-transform: uppercase; letter-spacing: .06em; }

  /* CATEGORIES */
  .cats-bar {
    max-width: 1200px; margin: 0 auto;
    padding: 1rem 2rem;
    display: flex; gap: .5rem; flex-wrap: wrap; align-items: center;
  }
  .cat-pill {
    padding: .35rem .9rem;
    border-radius: 50px;
    border: 1.5px solid var(--border);
    font-size: .82rem; font-weight: 500;
    text-decoration: none; color: var(--muted);
    background: var(--white);
    transition: all .2s;
  }
  .cat-pill:hover, .cat-pill.active {
    border-color: var(--gold); color: var(--gold);
    background: rgba(201,150,58,.06);
  }

  /* CONTENT GRID */
  .content { max-width: 1200px; margin: 0 auto; padding: 2rem; }

  /* Featured */
  .featured-card {
    background: var(--white);
    border-radius: var(--radius);
    border: 1px solid var(--border);
    padding: 2.5rem;
    margin-bottom: 2rem;
    display: grid; grid-template-columns: 1fr auto; gap: 2rem; align-items: start;
    box-shadow: 0 4px 20px rgba(0,0,0,.04);
    text-decoration: none; color: inherit;
    transition: transform .2s, box-shadow .2s;
  }
  .featured-card:hover { transform: translateY(-2px); box-shadow: 0 12px 40px rgba(0,0,0,.08); }
  .featured-tag {
    display: inline-block;
    background: var(--gold); color: var(--white);
    font-size: .72rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase;
    padding: .2rem .6rem; border-radius: 4px; margin-bottom: .8rem;
  }
  .featured-card h3 {
    font-family: 'Playfair Display', serif;
    font-size: 1.7rem; line-height: 1.2; margin-bottom: .8rem;
  }
  .featured-card p { color: var(--muted); line-height: 1.7; max-width: 560px; }
  .featured-meta {
    display: flex; gap: 1rem; align-items: center;
    margin-top: 1.5rem;
    font-size: .82rem; color: var(--muted);
  }
  .featured-meta .cat-badge {
    background: var(--cream); color: var(--ink);
    padding: .25rem .65rem; border-radius: 4px; font-weight: 500;
  }

  /* Article grid */
  .articles-heading {
    font-family: 'Playfair Display', serif;
    font-size: 1.2rem; color: var(--muted);
    margin-bottom: 1.25rem;
  }
  .articles-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
  }
  .article-card {
    background: var(--white);
    border-radius: var(--radius);
    border: 1px solid var(--border);
    padding: 1.75rem;
    text-decoration: none; color: inherit;
    display: flex; flex-direction: column;
    transition: transform .2s, box-shadow .2s;
    box-shadow: 0 2px 12px rgba(0,0,0,.03);
  }
  .article-card:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(0,0,0,.07); }
  .article-category {
    font-size: .75rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase;
    color: var(--gold); margin-bottom: .7rem;
  }
  .article-card h3 {
    font-family: 'Playfair Display', serif;
    font-size: 1.15rem; line-height: 1.3; margin-bottom: .7rem;
  }
  .article-card p { font-size: .875rem; color: var(--muted); line-height: 1.6; flex: 1; }
  .article-footer {
    display: flex; justify-content: space-between; align-items: center;
    margin-top: 1.25rem; padding-top: 1rem;
    border-top: 1px solid var(--cream);
    font-size: .8rem; color: var(--muted);
  }
  .views-badge { display: flex; align-items: center; gap: .3rem; }

  .empty-state {
    text-align: center; padding: 4rem 2rem; color: var(--muted);
  }
  .empty-state h3 { font-family: 'Playfair Display', serif; font-size: 1.5rem; margin-bottom: .5rem; color: var(--ink); }

  footer {
    border-top: 1px solid var(--border);
    padding: 2rem; text-align: center;
    font-size: .82rem; color: var(--muted);
    margin-top: 4rem;
  }
  footer a { color: var(--gold); text-decoration: none; }

  @media (max-width: 768px) {
    .hero { grid-template-columns: 1fr; }
    .hero-stats { display: none; }
    .featured-card { grid-template-columns: 1fr; }
    .nav-inner { flex-wrap: wrap; }
  }
</style>
</head>
<body>

<nav>
  <div class="nav-inner">
    <a class="nav-logo" href="/index.php">✒ Ink<span>well</span></a>
    <form class="nav-search" method="GET" action="">
      <?php if ($category): ?>
        <input type="hidden" name="cat" value="<?= h($category) ?>">
      <?php endif; ?>
      <input type="search" name="q" placeholder="Search articles…" value="<?= h($search) ?>">
    </form>
    <div class="nav-links">
      <?php if (isLoggedIn()): ?>
        <a href="/admin/dashboard.php" class="btn-admin">Dashboard</a>
      <?php else: ?>
        <a href="/login.php" class="btn-admin">Sign In</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<?php $stats = getStats(); ?>
<div class="hero">
  <div class="hero-text">
    <h2>Ideas, stories,<br>and <em>records</em>.</h2>
    <p>A thoughtfully curated collection of articles and knowledge. Browse, search, and discover.</p>
  </div>
  <div class="hero-stats">
    <div class="stat"><span class="stat-num"><?= $stats['published'] ?></span><span class="stat-lbl">Articles</span></div>
    <div class="stat"><span class="stat-num"><?= count($categories) ?></span><span class="stat-lbl">Topics</span></div>
    <div class="stat"><span class="stat-num"><?= $stats['views'] ?></span><span class="stat-lbl">Views</span></div>
  </div>
</div>

<div class="cats-bar">
  <a href="/index.php" class="cat-pill <?= !$category ? 'active' : '' ?>">All</a>
  <?php foreach ($categories as $cat): ?>
    <a href="?cat=<?= urlencode($cat) ?>" class="cat-pill <?= $category === $cat ? 'active' : '' ?>"><?= h($cat) ?></a>
  <?php endforeach; ?>
</div>

<div class="content">
  <?php if (empty($articles)): ?>
    <div class="empty-state">
      <h3>No articles found</h3>
      <p><?= $search ? 'Try a different search term.' : 'No articles in this category yet.' ?></p>
    </div>
  <?php else: ?>
    <?php if (!$search && !$category && !empty($featured)): ?>
      <?php $f = $featured[0]; ?>
      <a class="featured-card" href="/article.php?slug=<?= urlencode($f['slug']) ?>">
        <div>
          <span class="featured-tag">⭐ Featured</span>
          <h3><?= h($f['title']) ?></h3>
          <p><?= h($f['excerpt']) ?></p>
          <div class="featured-meta">
            <span class="cat-badge"><?= h($f['category']) ?></span>
            <span>By <?= h($f['author_name']) ?></span>
            <span><?= timeAgo($f['created_at']) ?></span>
            <span>👁 <?= $f['views'] ?> views</span>
          </div>
        </div>
      </a>
      <?php $articles = array_filter($articles, fn($a) => $a['id'] !== $f['id']); ?>
    <?php endif; ?>

    <?php if (!empty($articles)): ?>
      <?php if (!$search && !$category && !empty($featured)): ?>
        <p class="articles-heading">More articles</p>
      <?php endif; ?>
      <div class="articles-grid">
        <?php foreach ($articles as $a): ?>
          <a class="article-card" href="/article.php?slug=<?= urlencode($a['slug']) ?>">
            <div class="article-category"><?= h($a['category']) ?></div>
            <h3><?= h($a['title']) ?></h3>
            <p><?= h(mb_strimwidth($a['excerpt'], 0, 110, '…')) ?></p>
            <div class="article-footer">
              <span><?= timeAgo($a['created_at']) ?></span>
              <span class="views-badge">👁 <?= $a['views'] ?></span>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  <?php endif; ?>
</div>

<footer>
  &copy; <?= date('Y') ?> Inkwell CMS &mdash; <a href="/login.php">Admin Login</a>
</footer>

</body>
</html>
