<?php
// includes/admin-header.php
// Usage: include after requireLogin()/requireAdmin()
// Sets: $pageTitle (string), $activeNav (string)
$user = currentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= h($pageTitle ?? 'Dashboard') ?> — Inkwell Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  :root {
    --ink:      #0f0e0c;
    --sidebar:  #141210;
    --paper:    #faf8f4;
    --cream:    #f2ede4;
    --gold:     #c9963a;
    --gold-lt:  rgba(201,150,58,.15);
    --muted:    #8a8070;
    --border:   #ddd6c8;
    --white:    #ffffff;
    --success:  #2d8a4e;
    --danger:   #c0392b;
    --warning:  #d4820a;
    --sidebar-w: 240px;
  }

  html, body { height: 100%; }
  body {
    font-family: 'DM Sans', sans-serif;
    background: var(--paper);
    color: var(--ink);
    display: flex;
  }

  /* SIDEBAR */
  .sidebar {
    width: var(--sidebar-w);
    min-height: 100vh;
    background: var(--sidebar);
    display: flex; flex-direction: column;
    position: fixed; left: 0; top: 0;
    overflow-y: auto; z-index: 200;
    flex-shrink: 0;
  }
  .sidebar-brand {
    padding: 1.5rem 1.5rem 1rem;
    border-bottom: 1px solid rgba(255,255,255,.06);
  }
  .sidebar-brand a {
    font-family: 'Playfair Display', serif;
    font-size: 1.3rem; color: #fff; text-decoration: none;
    display: flex; align-items: center; gap: .5rem;
  }
  .sidebar-brand span { color: var(--gold); }
  .sidebar-badge {
    font-size: .65rem; background: var(--gold);
    color: #fff; padding: .15rem .45rem; border-radius: 4px;
    letter-spacing: .06em; text-transform: uppercase; margin-left: auto;
  }

  .sidebar-section {
    padding: 1.25rem .75rem .5rem;
    font-size: .68rem; letter-spacing: .1em; text-transform: uppercase;
    color: rgba(255,255,255,.25); font-weight: 500;
  }
  .sidebar-nav { list-style: none; padding: 0 .75rem; flex: 1; }
  .sidebar-nav li a {
    display: flex; align-items: center; gap: .75rem;
    padding: .65rem .85rem; border-radius: 9px;
    color: rgba(255,255,255,.55); text-decoration: none;
    font-size: .9rem; transition: all .15s; margin-bottom: .15rem;
  }
  .sidebar-nav li a:hover { color: #fff; background: rgba(255,255,255,.07); }
  .sidebar-nav li a.active { color: #fff; background: var(--gold-lt); }
  .sidebar-nav li a.active .nav-icon { color: var(--gold); }
  .nav-icon { font-size: 1rem; flex-shrink: 0; }

  .sidebar-user {
    padding: 1rem .75rem;
    margin: .75rem;
    border-radius: 10px;
    background: rgba(255,255,255,.05);
    display: flex; align-items: center; gap: .75rem;
  }
  .user-avatar {
    width: 32px; height: 32px; border-radius: 50%;
    background: var(--gold);
    display: flex; align-items: center; justify-content: center;
    font-size: .8rem; font-weight: 700; color: #fff; flex-shrink: 0;
  }
  .user-info small { display: block; font-size: .72rem; color: rgba(255,255,255,.35); }
  .user-info strong { display: block; font-size: .85rem; color: rgba(255,255,255,.85); }
  .sidebar-logout {
    display: block; text-align: center; padding: .65rem;
    color: rgba(255,255,255,.3); font-size: .8rem;
    text-decoration: none; transition: color .2s;
    border-top: 1px solid rgba(255,255,255,.06); margin-top: auto;
  }
  .sidebar-logout:hover { color: rgba(255,255,255,.7); }

  /* MAIN */
  .main {
    margin-left: var(--sidebar-w);
    flex: 1; min-height: 100vh;
    display: flex; flex-direction: column;
  }
  .topbar {
    background: var(--white);
    border-bottom: 1px solid var(--border);
    padding: 1rem 2rem;
    display: flex; align-items: center; justify-content: space-between;
    position: sticky; top: 0; z-index: 100;
  }
  .topbar-title {
    font-family: 'Playfair Display', serif;
    font-size: 1.4rem;
  }
  .topbar-actions { display: flex; gap: .75rem; align-items: center; }

  /* Buttons */
  .btn {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .55rem 1.1rem; border-radius: 9px; font-family: 'DM Sans', sans-serif;
    font-size: .875rem; font-weight: 500; cursor: pointer; text-decoration: none;
    border: 1.5px solid transparent; transition: all .15s; white-space: nowrap;
  }
  .btn-primary  { background: var(--ink); color: #fff; }
  .btn-primary:hover  { background: #2a2720; }
  .btn-gold     { background: var(--gold); color: #fff; border-color: var(--gold); }
  .btn-gold:hover { background: #b8862f; }
  .btn-outline  { background: transparent; color: var(--ink); border-color: var(--border); }
  .btn-outline:hover { background: var(--cream); }
  .btn-danger   { background: transparent; color: var(--danger); border-color: #f5c6c2; }
  .btn-danger:hover { background: #fdf0ef; }
  .btn-sm { padding: .35rem .75rem; font-size: .8rem; }

  /* Alerts */
  .alert {
    padding: .85rem 1.25rem; border-radius: 10px; margin-bottom: 1.5rem;
    font-size: .875rem; display: flex; align-items: center; gap: .6rem;
  }
  .alert-success { background: #edfaf3; color: var(--success); border: 1px solid #b6e8cc; }
  .alert-error   { background: #fdf0ef; color: var(--danger); border: 1px solid #f5c6c2; }
  .alert-warning { background: #fef9ec; color: var(--warning); border: 1px solid #f5d98b; }

  /* Page body */
  .page-body { padding: 2rem; flex: 1; }

  /* Cards */
  .card {
    background: var(--white); border: 1px solid var(--border);
    border-radius: 14px; overflow: hidden;
  }
  .card-header {
    padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border);
    display: flex; align-items: center; justify-content: space-between;
  }
  .card-header h2 { font-size: 1rem; font-weight: 600; }
  .card-body { padding: 1.5rem; }

  /* Table */
  table { width: 100%; border-collapse: collapse; }
  th {
    text-align: left; padding: .8rem 1rem;
    font-size: .78rem; font-weight: 600; letter-spacing: .06em; text-transform: uppercase;
    color: var(--muted); background: var(--paper);
    border-bottom: 1px solid var(--border);
  }
  td {
    padding: .9rem 1rem; border-bottom: 1px solid var(--cream);
    font-size: .9rem; vertical-align: middle;
  }
  tr:last-child td { border-bottom: none; }
  tr:hover td { background: rgba(250,248,244,.6); }

  /* Badges */
  .badge {
    display: inline-block; padding: .25rem .65rem; border-radius: 50px;
    font-size: .75rem; font-weight: 600;
  }
  .badge-published { background: #edfaf3; color: var(--success); }
  .badge-draft     { background: var(--cream); color: var(--muted); }
  .badge-admin     { background: var(--gold-lt); color: var(--gold); }

  /* Form elements */
  .form-group { margin-bottom: 1.25rem; }
  .form-label {
    display: block; font-size: .8rem; font-weight: 500;
    letter-spacing: .06em; text-transform: uppercase;
    color: var(--muted); margin-bottom: .5rem;
  }
  .form-control {
    width: 100%; padding: .75rem 1rem;
    border: 1.5px solid var(--border); border-radius: 10px;
    font-family: 'DM Sans', sans-serif; font-size: .95rem; color: var(--ink);
    background: var(--paper); outline: none; transition: border-color .2s, box-shadow .2s;
  }
  .form-control:focus {
    border-color: var(--gold); background: var(--white);
    box-shadow: 0 0 0 3px rgba(201,150,58,.1);
  }
  textarea.form-control { resize: vertical; min-height: 120px; line-height: 1.6; }
  select.form-control { cursor: pointer; }

  /* Grid helpers */
  .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
  .grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.25rem; }

  /* Stat card */
  .stat-card {
    background: var(--white); border: 1px solid var(--border); border-radius: 14px;
    padding: 1.5rem;
  }
  .stat-card .stat-icon { font-size: 1.5rem; margin-bottom: .75rem; }
  .stat-card .stat-num {
    font-family: 'Playfair Display', serif; font-size: 2.2rem; line-height: 1;
    margin-bottom: .25rem;
  }
  .stat-card .stat-label { font-size: .82rem; color: var(--muted); text-transform: uppercase; letter-spacing: .06em; }

  @media (max-width: 768px) {
    .sidebar { width: 60px; }
    .sidebar-brand a span, .sidebar-brand span,
    .sidebar-nav li a span:not(.nav-icon),
    .sidebar-user .user-info, .sidebar-section,
    .sidebar-badge { display: none; }
    .sidebar-user { justify-content: center; }
    .main { margin-left: 60px; }
    .grid-4 { grid-template-columns: 1fr 1fr; }
    .grid-2 { grid-template-columns: 1fr; }
  }
</style>

<div class="sidebar">
  <div class="sidebar-brand">
    <a href="/admin/dashboard.php">
      ✒ Ink<span>well</span>
      <span class="sidebar-badge">Admin</span>
    </a>
  </div>

  <p class="sidebar-section">Content</p>
  <ul class="sidebar-nav">
    <li><a href="/admin/dashboard.php" class="<?= ($activeNav??'')==='dashboard'?'active':'' ?>">
      <span class="nav-icon">📊</span> <span>Dashboard</span>
    </a></li>
    <li><a href="/admin/articles.php" class="<?= ($activeNav??'')==='articles'?'active':'' ?>">
      <span class="nav-icon">📄</span> <span>Articles</span>
    </a></li>
    <li><a href="/admin/article-edit.php" class="<?= ($activeNav??'')==='new-article'?'active':'' ?>">
      <span class="nav-icon">✏️</span> <span>New Article</span>
    </a></li>
  </ul>

  <p class="sidebar-section">System</p>
  <ul class="sidebar-nav">
    <li><a href="/admin/users.php" class="<?= ($activeNav??'')==='users'?'active':'' ?>">
      <span class="nav-icon">👥</span> <span>Users</span>
    </a></li>
    <li><a href="/index.php" target="_blank">
      <span class="nav-icon">🌐</span> <span>View Site</span>
    </a></li>
  </ul>

  <div style="flex:1"></div>

  <div class="sidebar-user">
    <div class="user-avatar"><?= strtoupper(substr($user['name'], 0, 1)) ?></div>
    <div class="user-info">
      <strong><?= h($user['name']) ?></strong>
      <small><?= h($user['role']) ?></small>
    </div>
  </div>
  <a href="/logout.php" class="sidebar-logout">Sign out</a>
</div>

<div class="main">
  <div class="topbar">
    <h1 class="topbar-title"><?= h($pageTitle ?? 'Dashboard') ?></h1>
    <div class="topbar-actions">
