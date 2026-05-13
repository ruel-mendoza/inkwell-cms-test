<?php
require_once __DIR__ . '/includes/helpers.php';
startSession();

if (isLoggedIn()) {
    header('Location: /admin/dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if (login($username, $password)) {
        header('Location: /admin/dashboard.php');
        exit;
    }
    $error = 'Invalid username or password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sign In — Inkwell CMS</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --ink:     #0f0e0c;
    --paper:   #faf8f4;
    --cream:   #f2ede4;
    --gold:    #c9963a;
    --gold-lt: #e8c97a;
    --muted:   #8a8070;
    --border:  #ddd6c8;
    --error:   #c0392b;
  }

  body {
    font-family: 'DM Sans', sans-serif;
    background: var(--paper);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
  }

  .page-wrap {
    display: grid;
    grid-template-columns: 1fr 1fr;
    max-width: 900px;
    width: 100%;
    min-height: 520px;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 30px 80px rgba(15,14,12,.18);
  }

  /* Left panel */
  .panel-left {
    background: var(--ink);
    padding: 3.5rem;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    position: relative;
    overflow: hidden;
  }
  .panel-left::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse at 30% 70%, rgba(201,150,58,.2) 0%, transparent 60%);
  }
  .brand { position: relative; z-index: 1; }
  .brand-mark {
    width: 44px; height: 44px;
    background: var(--gold);
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem;
    margin-bottom: 1.5rem;
  }
  .brand h1 {
    font-family: 'Playfair Display', serif;
    font-size: 2.2rem;
    color: #fff;
    line-height: 1.1;
    margin-bottom: .75rem;
  }
  .brand p {
    color: var(--muted);
    font-size: .9rem;
    font-weight: 300;
    line-height: 1.6;
  }
  .panel-tagline {
    position: relative; z-index: 1;
    font-family: 'Playfair Display', serif;
    font-size: .85rem;
    color: var(--gold);
    letter-spacing: .08em;
  }

  /* Right panel */
  .panel-right {
    background: #fff;
    padding: 3.5rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
  }
  .login-heading {
    font-family: 'Playfair Display', serif;
    font-size: 1.8rem;
    color: var(--ink);
    margin-bottom: .4rem;
  }
  .login-sub {
    color: var(--muted);
    font-size: .88rem;
    margin-bottom: 2rem;
  }

  .error-box {
    background: #fdf0ef;
    border-left: 3px solid var(--error);
    color: var(--error);
    padding: .75rem 1rem;
    border-radius: 6px;
    font-size: .875rem;
    margin-bottom: 1.5rem;
  }

  .field { margin-bottom: 1.25rem; }
  .field label {
    display: block;
    font-size: .8rem;
    font-weight: 500;
    letter-spacing: .06em;
    text-transform: uppercase;
    color: var(--muted);
    margin-bottom: .5rem;
  }
  .field input {
    width: 100%;
    padding: .8rem 1rem;
    border: 1.5px solid var(--border);
    border-radius: 10px;
    font-family: 'DM Sans', sans-serif;
    font-size: .95rem;
    color: var(--ink);
    background: var(--paper);
    transition: border-color .2s, box-shadow .2s;
    outline: none;
  }
  .field input:focus {
    border-color: var(--gold);
    box-shadow: 0 0 0 3px rgba(201,150,58,.12);
    background: #fff;
  }

  .btn-login {
    width: 100%;
    padding: .9rem;
    background: var(--ink);
    color: #fff;
    border: none;
    border-radius: 10px;
    font-family: 'DM Sans', sans-serif;
    font-size: .95rem;
    font-weight: 500;
    cursor: pointer;
    transition: background .2s, transform .1s;
    margin-top: .5rem;
  }
  .btn-login:hover { background: #1e1c18; }
  .btn-login:active { transform: scale(.98); }

  .hint {
    margin-top: 1.5rem;
    text-align: center;
    font-size: .82rem;
    color: var(--muted);
  }
  .hint a { color: var(--gold); text-decoration: none; font-weight: 500; }

  @media (max-width: 620px) {
    .page-wrap { grid-template-columns: 1fr; }
    .panel-left { display: none; }
  }
</style>
</head>
<body>
<div class="page-wrap">
  <div class="panel-left">
    <div class="brand">
      <div class="brand-mark">✒</div>
      <h1>Inkwell<br>CMS</h1>
      <p>A refined space for your articles, stories, and records.</p>
    </div>
    <div class="panel-tagline">Words that endure.</div>
  </div>

  <div class="panel-right">
    <h2 class="login-heading">Welcome back</h2>
    <p class="login-sub">Sign in to manage your content</p>

    <?php if ($error): ?>
      <div class="error-box">⚠ <?= h($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="field">
        <label for="username">Username</label>
        <input type="text" id="username" name="username"
               value="<?= h($_POST['username'] ?? '') ?>"
               placeholder="admin" autocomplete="username" required autofocus>
      </div>
      <div class="field">
        <label for="password">Password</label>
        <input type="password" id="password" name="password"
               placeholder="••••••••" autocomplete="current-password" required>
      </div>
      <button type="submit" class="btn-login">Sign In →</button>
    </form>

    <p class="hint">Default credentials: <strong>admin</strong> / <strong>password</strong></p>
    <p class="hint" style="margin-top:.5rem"><a href="/index.php">← View public site</a></p>
  </div>
</div>
</body>
</html>
