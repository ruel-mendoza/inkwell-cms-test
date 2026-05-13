# ✒ Inkwell CMS

A clean, lightweight Article & Record Management System built in PHP with JSON file storage. No database required.

---

## Features

| Area | Details |
|------|---------|
| **Auth** | Session-based login, password hashing (bcrypt), session expiry |
| **Frontend** | Public article listing, search, category filter, single article view |
| **Admin Dashboard** | Stats overview, recent articles table |
| **Article Manager** | Create, edit, delete, publish/draft, featured flag, tags, categories |
| **User Manager** | Add users, change passwords, delete users, role management (admin/editor) |
| **Storage** | Plain JSON files in `data/` — no database needed |

---

## Quick Start

### Requirements
- PHP 8.0+ with `session` support
- A web server (Apache with `mod_rewrite`, Nginx, or PHP built-in server)

### 1. Clone / Copy files
```bash
cp -r inkwell-cms/ /var/www/html/cms
```

### 2. Set permissions
```bash
chmod 755 /var/www/html/cms
chmod -R 664 /var/www/html/cms/data/*.json
```

### 3. Serve with PHP built-in server (quickest)
```bash
cd /var/www/html/cms
php -S localhost:8080
```
Then open: http://localhost:8080

### 4. Apache / Nginx
Point your document root to the `cms/` folder. For Apache, `mod_rewrite` must be enabled. The included `.htaccess` blocks direct access to `data/`.

---

## Default Login

| Field    | Value      |
|----------|------------|
| URL      | `/login.php` |
| Username | `admin`    |
| Password | `password` |

> **Change this immediately!** Go to **Admin → Users** and update the password.

---

## File Structure

```
cms/
├── index.php              # Public homepage (article listing)
├── article.php            # Single article view
├── login.php              # Login page
├── logout.php             # Session destroy
│
├── admin/
│   ├── dashboard.php      # Admin home with stats
│   ├── articles.php       # Article list/delete
│   ├── article-edit.php   # Create & edit articles
│   └── users.php          # User management
│
├── includes/
│   ├── helpers.php        # All core functions (auth, CRUD, utils)
│   ├── admin-header.php   # Admin layout: sidebar + topbar open
│   ├── admin-body-open.php
│   └── admin-footer.php   # Closes layout HTML
│
└── data/
    ├── users.json         # User accounts
    ├── articles.json      # Articles / records
    └── .htaccess          # Blocks direct HTTP access to data/
```

---

## Data Schemas

### `data/users.json`
```json
{
  "id": 1,
  "username": "admin",
  "password": "<bcrypt hash>",
  "name": "Administrator",
  "role": "admin",            // "admin" | "editor"
  "email": "admin@site.com",
  "created_at": "2024-01-01T00:00:00Z"
}
```

### `data/articles.json`
```json
{
  "id": 1,
  "title": "My Article",
  "slug": "my-article",
  "excerpt": "Short summary...",
  "content": "Full content...",
  "category": "Technology",
  "tags": ["php", "cms"],
  "status": "published",      // "published" | "draft"
  "featured": false,
  "author_id": 1,
  "author_name": "Administrator",
  "views": 42,
  "created_at": "2024-01-01T10:00:00Z",
  "updated_at": "2024-01-01T10:00:00Z"
}
```

---

## Security Notes

- Passwords are hashed with `password_hash()` (bcrypt)
- Session cookies use `HttpOnly` and `SameSite=Lax`
- `startSession()` now checks `headers_sent()` before setting cookie parameters or starting the session
- All user output is escaped with `htmlspecialchars()`
- Session IDs are regenerated on login
- `data/` directory is blocked from HTTP access via `.htaccess`
- For production, add CSRF tokens to all state-changing forms

---

## Extending

**Add a new field to articles:** Edit `data/articles.json` schema, add the form field in `admin/article-edit.php`, and save it in `includes/helpers.php → saveArticle()`.

**Add rich text editing:** Include a JS editor like [Quill](https://quilljs.com) or [TinyMCE](https://www.tiny.cloud) in `admin/article-edit.php` targeting the `content` textarea.

**Add image upload:** Add a file input, move uploads to `public/uploads/`, and save the path as `cover_image` in the article JSON.
