<?php
require_once __DIR__ . '/../includes/helpers.php';
requireAdmin();

$error   = '';
$success = '';

// Handle add user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $username = trim($_POST['username'] ?? '');
        $name     = trim($_POST['name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role     = in_array($_POST['role'] ?? '', ['admin','editor']) ? $_POST['role'] : 'editor';

        if (!$username || !$name || !$password) {
            $error = 'Username, name, and password are required.';
        } else {
            $users = readJson('users.json');
            foreach ($users as $u) {
                if ($u['username'] === $username) { $error = 'Username already exists.'; break; }
            }
            if (!$error) {
                $newUser = [
                    'id'         => nextId($users),
                    'username'   => $username,
                    'password'   => password_hash($password, PASSWORD_DEFAULT),
                    'name'       => $name,
                    'role'       => $role,
                    'email'      => $email,
                    'created_at' => date('c'),
                ];
                $users[] = $newUser;
                writeJson('users.json', $users);
                header('Location: /admin/users.php?msg=' . urlencode('User created!'));
                exit;
            }
        }
    } elseif ($_POST['action'] === 'delete') {
        $delId = (int)$_POST['user_id'];
        $current = currentUser();
        if ($delId === $current['id']) {
            $error = 'You cannot delete your own account.';
        } else {
            $users = array_values(array_filter(readJson('users.json'), fn($u) => $u['id'] !== $delId));
            writeJson('users.json', $users);
            header('Location: /admin/users.php?msg=' . urlencode('User deleted.'));
            exit;
        }
    } elseif ($_POST['action'] === 'change_password') {
        $uid  = (int)$_POST['user_id'];
        $pwd  = $_POST['new_password'] ?? '';
        if (strlen($pwd) < 6) {
            $error = 'Password must be at least 6 characters.';
        } else {
            $users = readJson('users.json');
            foreach ($users as &$u) {
                if ($u['id'] === $uid) { $u['password'] = password_hash($pwd, PASSWORD_DEFAULT); break; }
            }
            writeJson('users.json', $users);
            header('Location: /admin/users.php?msg=' . urlencode('Password updated.'));
            exit;
        }
    }
}

$pageTitle = 'Users';
$activeNav = 'users';
$users = readJson('users.json');
$current = currentUser();

include __DIR__ . '/../includes/admin-header.php';
?>
<?php include __DIR__ . '/../includes/admin-body-open.php'; ?>

<?php if (isset($_GET['msg'])): ?>
  <div class="alert alert-success">✓ <?= h(urldecode($_GET['msg'])) ?></div>
<?php endif; ?>
<?php if ($error): ?>
  <div class="alert alert-error">⚠ <?= h($error) ?></div>
<?php endif; ?>

<div class="grid-2" style="gap:2rem;align-items:start">

  <!-- Users table -->
  <div class="card">
    <div class="card-header">
      <h2>All Users (<?= count($users) ?>)</h2>
    </div>
    <table>
      <thead>
        <tr><th>Name</th><th>Username</th><th>Role</th><th>Joined</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php foreach ($users as $u): ?>
          <tr>
            <td>
              <div style="display:flex;align-items:center;gap:.7rem">
                <div style="width:30px;height:30px;border-radius:50%;background:var(--gold);display:flex;align-items:center;justify-content:center;font-size:.8rem;font-weight:700;color:#fff;flex-shrink:0">
                  <?= strtoupper(substr($u['name'],0,1)) ?>
                </div>
                <div>
                  <strong><?= h($u['name']) ?></strong>
                  <?php if ($u['id'] === $current['id']): ?>
                    <span style="font-size:.7rem;color:var(--gold)">(you)</span>
                  <?php endif; ?>
                  <div style="font-size:.78rem;color:var(--muted)"><?= h($u['email'] ?? '') ?></div>
                </div>
              </div>
            </td>
            <td><code style="font-size:.85rem"><?= h($u['username']) ?></code></td>
            <td><span class="badge badge-<?= $u['role'] === 'admin' ? 'admin' : 'draft' ?>"><?= h($u['role']) ?></span></td>
            <td><?= timeAgo($u['created_at']) ?></td>
            <td>
              <div style="display:flex;gap:.35rem;flex-wrap:wrap">
                <!-- Change password modal trigger -->
                <button class="btn btn-outline btn-sm" onclick="showPwdForm(<?= $u['id'] ?>, '<?= h($u['name']) ?>')">
                  🔑
                </button>
                <?php if ($u['id'] !== $current['id']): ?>
                  <form method="POST" onsubmit="return confirm('Delete user <?= h(addslashes($u['name'])) ?>?')">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                    <button type="submit" class="btn btn-danger btn-sm">Del</button>
                  </form>
                <?php endif; ?>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Right column -->
  <div style="display:flex;flex-direction:column;gap:1.5rem">
    <!-- Add user -->
    <div class="card">
      <div class="card-header"><h2>Add New User</h2></div>
      <div class="card-body">
        <form method="POST">
          <input type="hidden" name="action" value="add">
          <div class="form-group">
            <label class="form-label">Full Name *</label>
            <input type="text" name="name" class="form-control" placeholder="Jane Doe" required>
          </div>
          <div class="form-group">
            <label class="form-label">Username *</label>
            <input type="text" name="username" class="form-control" placeholder="jane" required>
          </div>
          <div class="form-group">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" placeholder="jane@example.com">
          </div>
          <div class="form-group">
            <label class="form-label">Password *</label>
            <input type="password" name="password" class="form-control" placeholder="Min 6 characters" required>
          </div>
          <div class="form-group">
            <label class="form-label">Role</label>
            <select name="role" class="form-control">
              <option value="editor">Editor</option>
              <option value="admin">Admin</option>
            </select>
          </div>
          <button type="submit" class="btn btn-gold" style="width:100%">Create User</button>
        </form>
      </div>
    </div>

    <!-- Change password inline form (hidden by default) -->
    <div class="card" id="pwd-form-card" style="display:none">
      <div class="card-header">
        <h2>Change Password for <span id="pwd-user-name"></span></h2>
        <button onclick="document.getElementById('pwd-form-card').style.display='none'"
                class="btn btn-outline btn-sm">✕</button>
      </div>
      <div class="card-body">
        <form method="POST">
          <input type="hidden" name="action" value="change_password">
          <input type="hidden" name="user_id" id="pwd-user-id">
          <div class="form-group">
            <label class="form-label">New Password</label>
            <input type="password" name="new_password" class="form-control" placeholder="Min 6 characters" required>
          </div>
          <button type="submit" class="btn btn-primary" style="width:100%">Update Password</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
function showPwdForm(id, name) {
  document.getElementById('pwd-user-id').value = id;
  document.getElementById('pwd-user-name').textContent = name;
  document.getElementById('pwd-form-card').style.display = '';
  document.getElementById('pwd-form-card').scrollIntoView({behavior:'smooth'});
}
</script>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
