<?php
// includes/helpers.php — Core utility functions

define('DATA_PATH', __DIR__ . '/../data/');
define('SESSION_LIFETIME', 3600);

// ── JSON Storage ──────────────────────────────────────────────
function readJson(string $file): array {
    $path = DATA_PATH . $file;
    if (!file_exists($path)) return [];
    $json = file_get_contents($path);
    return json_decode($json, true) ?? [];
}

function writeJson(string $file, array $data): bool {
    $path = DATA_PATH . $file;
    return file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
}

function nextId(array $records): int {
    if (empty($records)) return 1;
    return max(array_column($records, 'id')) + 1;
}

// ── Auth ──────────────────────────────────────────────────────
function startSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        if (headers_sent()) {
            return;
        }
        session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
        session_start();
    }
}

function isLoggedIn(): bool {
    startSession();
    return isset($_SESSION['user_id']) && isset($_SESSION['logged_in_at'])
        && (time() - $_SESSION['logged_in_at']) < SESSION_LIFETIME;
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: /login.php');
        exit;
    }
}

function requireAdmin(): void {
    requireLogin();
    if (($_SESSION['user_role'] ?? '') !== 'admin') {
        header('Location: /index.php');
        exit;
    }
}

function currentUser(): ?array {
    if (!isLoggedIn()) return null;
    return [
        'id'   => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'],
        'role' => $_SESSION['user_role'],
    ];
}

function login(string $username, string $password): bool {
    $users = readJson('users.json');
    foreach ($users as $user) {
        if ($user['username'] === $username && password_verify($password, $user['password'])) {
            startSession();
            session_regenerate_id(true);
            $_SESSION['user_id']       = $user['id'];
            $_SESSION['user_name']     = $user['name'];
            $_SESSION['user_role']     = $user['role'];
            $_SESSION['logged_in_at']  = time();
            return true;
        }
    }
    return false;
}

function logout(): void {
    startSession();
    session_destroy();
}

// ── Articles ──────────────────────────────────────────────────
function getArticles(bool $publishedOnly = false): array {
    $articles = readJson('articles.json');
    if ($publishedOnly) {
        $articles = array_values(array_filter($articles, fn($a) => $a['status'] === 'published'));
    }
    usort($articles, fn($a, $b) => strtotime($b['created_at']) - strtotime($a['created_at']));
    return $articles;
}

function getArticleById(int $id): ?array {
    foreach (readJson('articles.json') as $a) {
        if ($a['id'] === $id) return $a;
    }
    return null;
}

function getArticleBySlug(string $slug): ?array {
    foreach (readJson('articles.json') as $a) {
        if ($a['slug'] === $slug) return $a;
    }
    return null;
}

function saveArticle(array $data): bool {
    $articles = readJson('articles.json');
    if (isset($data['id']) && $data['id'] > 0) {
        foreach ($articles as &$a) {
            if ($a['id'] === (int)$data['id']) {
                $data['updated_at'] = date('c');
                $a = array_merge($a, $data);
                break;
            }
        }
    } else {
        $data['id']         = nextId($articles);
        $data['views']      = 0;
        $data['created_at'] = date('c');
        $data['updated_at'] = date('c');
        $articles[]         = $data;
    }
    return writeJson('articles.json', $articles);
}

function deleteArticle(int $id): bool {
    $articles = array_values(array_filter(readJson('articles.json'), fn($a) => $a['id'] !== $id));
    return writeJson('articles.json', $articles);
}

function incrementViews(int $id): void {
    $articles = readJson('articles.json');
    foreach ($articles as &$a) {
        if ($a['id'] === $id) { $a['views']++; break; }
    }
    writeJson('articles.json', $articles);
}

function slugify(string $text): string {
    $text = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $text), '-'));
    return $text ?: 'article';
}

// ── Helpers ───────────────────────────────────────────────────
function h(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

if (!function_exists('mb_strimwidth')) {
    function mb_strimwidth(string $str, int $start, int $width, string $trimmarker = '', string $encoding = 'UTF-8'): string {
        if ($start !== 0) {
            $str = extension_loaded('mbstring') ? mb_substr($str, $start, null, $encoding) : substr($str, $start);
        }

        $markerLength = extension_loaded('mbstring') ? mb_strlen($trimmarker, $encoding) : strlen($trimmarker);
        $length = $width - $markerLength;
        if ($length < 0) {
            return $trimmarker;
        }

        $text = extension_loaded('mbstring') ? mb_substr($str, 0, $length, $encoding) : substr($str, 0, $length);
        if ((extension_loaded('mbstring') ? mb_strlen($str, $encoding) : strlen($str)) > (extension_loaded('mbstring') ? mb_strlen($text, $encoding) : strlen($text))) {
            return $text . $trimmarker;
        }
        return $text;
    }
}

function timeAgo(string $datetime): string {
    $diff = time() - strtotime($datetime);
    if ($diff < 60)     return 'just now';
    if ($diff < 3600)   return floor($diff/60) . 'm ago';
    if ($diff < 86400)  return floor($diff/3600) . 'h ago';
    if ($diff < 604800) return floor($diff/86400) . 'd ago';
    return date('M j, Y', strtotime($datetime));
}

function getCategories(): array {
    $articles = readJson('articles.json');
    $cats = array_unique(array_column($articles, 'category'));
    sort($cats);
    return array_filter($cats);
}

function getStats(): array {
    $articles = readJson('articles.json');
    $users    = readJson('users.json');
    return [
        'total'     => count($articles),
        'published' => count(array_filter($articles, fn($a) => $a['status'] === 'published')),
        'drafts'    => count(array_filter($articles, fn($a) => $a['status'] === 'draft')),
        'views'     => array_sum(array_column($articles, 'views')),
        'users'     => count($users),
    ];
}
