import { useState, useEffect } from "react";

const USERS = [
  { id: 1, username: "admin", password: "admin123", role: "Admin", name: "Admin User" },
  { id: 2, username: "editor", password: "editor123", role: "Editor", name: "Jane Editor" },
];

const SAMPLE_ARTICLES = [
  { id: 1, title: "Getting Started with React", category: "Technology", status: "Published", author: "Admin User", date: "2026-04-10", content: "React is a JavaScript library for building user interfaces..." },
  { id: 2, title: "Top 10 Travel Destinations", category: "Travel", status: "Draft", author: "Jane Editor", date: "2026-05-01", content: "Exploring the world's most beautiful places..." },
  { id: 3, title: "Healthy Eating Habits", category: "Health", status: "Published", author: "Admin User", date: "2026-04-28", content: "Maintaining a balanced diet is essential for good health..." },
];

const SAMPLE_RECORDS = [
  { id: 1, name: "Maria Santos", email: "maria@example.com", department: "Marketing", status: "Active", joined: "2025-01-15" },
  { id: 2, name: "Juan Dela Cruz", email: "juan@example.com", department: "Engineering", status: "Active", joined: "2024-08-22" },
  { id: 3, name: "Ana Reyes", email: "ana@example.com", department: "HR", status: "Inactive", joined: "2023-03-10" },
];

const CATEGORIES = ["Technology", "Travel", "Health", "Business", "Science", "Lifestyle"];
const DEPARTMENTS = ["Marketing", "Engineering", "HR", "Finance", "Operations", "Sales"];

function initials(name) {
  return name.split(" ").map(n => n[0]).join("").slice(0, 2).toUpperCase();
}

function Badge({ status }) {
  const map = {
    Published: { bg: "#e8f5e1", color: "#3B6D11" },
    Draft:     { bg: "#fff8e0", color: "#854F0B" },
    Active:    { bg: "#e6f5f0", color: "#0F6E56" },
    Inactive:  { bg: "#f7e8e8", color: "#A32D2D" },
    Admin:     { bg: "#EEEDFE", color: "#3C3489" },
    Editor:    { bg: "#e6f1fb", color: "#185FA5" },
  };
  const s = map[status] || { bg: "#eee", color: "#555" };
  return (
    <span style={{ background: s.bg, color: s.color, fontSize: 11, fontWeight: 500, padding: "2px 8px", borderRadius: 20, letterSpacing: 0.3 }}>
      {status}
    </span>
  );
}

function Modal({ title, onClose, children }) {
  return (
    <div style={{ position: "fixed", inset: 0, background: "rgba(0,0,0,0.35)", display: "flex", alignItems: "center", justifyContent: "center", zIndex: 1000 }}>
      <div style={{ background: "#fff", borderRadius: 14, padding: "1.75rem 2rem", minWidth: 400, maxWidth: 540, width: "100%", boxShadow: "0 8px 32px rgba(0,0,0,0.12)", maxHeight: "90vh", overflowY: "auto" }}>
        <div style={{ display: "flex", justifyContent: "space-between", alignItems: "center", marginBottom: 20 }}>
          <h3 style={{ margin: 0, fontSize: 17, fontWeight: 600, color: "#1a1a2e" }}>{title}</h3>
          <button onClick={onClose} style={{ background: "none", border: "none", cursor: "pointer", fontSize: 20, color: "#888", lineHeight: 1, padding: "2px 6px" }}>×</button>
        </div>
        {children}
      </div>
    </div>
  );
}

function LoginPage({ onLogin }) {
  const [form, setForm] = useState({ username: "", password: "" });
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);

  function handleSubmit() {
    setError("");
    setLoading(true);
    setTimeout(() => {
      const user = USERS.find(u => u.username === form.username && u.password === form.password);
      if (user) { onLogin(user); }
      else { setError("Invalid username or password."); }
      setLoading(false);
    }, 500);
  }

  return (
    <div style={{ minHeight: "100vh", display: "flex", alignItems: "center", justifyContent: "center", background: "linear-gradient(135deg, #1a1a2e 0%, #16213e 60%, #0f3460 100%)", fontFamily: "'Segoe UI', system-ui, sans-serif" }}>
      <div style={{ background: "#fff", borderRadius: 18, padding: "2.5rem 2.5rem", width: 380, boxShadow: "0 16px 48px rgba(0,0,0,0.25)" }}>
        <div style={{ textAlign: "center", marginBottom: 28 }}>
          <div style={{ width: 52, height: 52, borderRadius: 14, background: "linear-gradient(135deg, #1a1a2e, #0f3460)", display: "inline-flex", alignItems: "center", justifyContent: "center", marginBottom: 12 }}>
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#fff" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
              <path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/>
            </svg>
          </div>
          <h1 style={{ margin: 0, fontSize: 22, fontWeight: 700, color: "#1a1a2e" }}>CMS Portal</h1>
          <p style={{ margin: "4px 0 0", fontSize: 13, color: "#888" }}>Content & Records Management</p>
        </div>
        <div style={{ marginBottom: 14 }}>
          <label style={{ display: "block", fontSize: 12, fontWeight: 600, color: "#555", marginBottom: 5, textTransform: "uppercase", letterSpacing: 0.5 }}>Username</label>
          <input value={form.username} onChange={e => setForm({ ...form, username: e.target.value })}
            onKeyDown={e => e.key === "Enter" && handleSubmit()}
            placeholder="Enter username" style={{ width: "100%", padding: "10px 12px", borderRadius: 8, border: "1.5px solid #e0e0e0", fontSize: 14, outline: "none", boxSizing: "border-box", fontFamily: "inherit" }} />
        </div>
        <div style={{ marginBottom: 6 }}>
          <label style={{ display: "block", fontSize: 12, fontWeight: 600, color: "#555", marginBottom: 5, textTransform: "uppercase", letterSpacing: 0.5 }}>Password</label>
          <input type="password" value={form.password} onChange={e => setForm({ ...form, password: e.target.value })}
            onKeyDown={e => e.key === "Enter" && handleSubmit()}
            placeholder="Enter password" style={{ width: "100%", padding: "10px 12px", borderRadius: 8, border: "1.5px solid #e0e0e0", fontSize: 14, outline: "none", boxSizing: "border-box", fontFamily: "inherit" }} />
        </div>
        {error && <p style={{ color: "#c0392b", fontSize: 13, margin: "8px 0" }}>{error}</p>}
        <button onClick={handleSubmit} disabled={loading}
          style={{ width: "100%", marginTop: 18, padding: "11px", background: "linear-gradient(135deg, #1a1a2e, #0f3460)", color: "#fff", border: "none", borderRadius: 9, fontSize: 15, fontWeight: 600, cursor: "pointer", letterSpacing: 0.3 }}>
          {loading ? "Signing in..." : "Sign In"}
        </button>
        <div style={{ marginTop: 18, background: "#f8f9fa", borderRadius: 8, padding: "10px 14px" }}>
          <p style={{ margin: 0, fontSize: 11.5, color: "#888", fontWeight: 600, marginBottom: 4 }}>DEMO CREDENTIALS</p>
          <p style={{ margin: "2px 0", fontSize: 12, color: "#555" }}><b>Admin:</b> admin / admin123</p>
          <p style={{ margin: "2px 0", fontSize: 12, color: "#555" }}><b>Editor:</b> editor / editor123</p>
        </div>
      </div>
    </div>
  );
}

function Sidebar({ active, setActive, user, onLogout }) {
  const navItems = [
    { key: "dashboard", icon: "🏠", label: "Dashboard" },
    { key: "articles", icon: "📄", label: "Articles" },
    { key: "records", icon: "👥", label: "Records" },
  ];
  return (
    <div style={{ width: 220, background: "#1a1a2e", minHeight: "100vh", display: "flex", flexDirection: "column", flexShrink: 0 }}>
      <div style={{ padding: "24px 20px 16px", borderBottom: "1px solid rgba(255,255,255,0.08)" }}>
        <div style={{ display: "flex", alignItems: "center", gap: 10 }}>
          <div style={{ width: 36, height: 36, borderRadius: 9, background: "linear-gradient(135deg, #4776e6, #8e54e9)", display: "flex", alignItems: "center", justifyContent: "center", fontSize: 15 }}>📋</div>
          <div>
            <div style={{ color: "#fff", fontWeight: 700, fontSize: 14, letterSpacing: 0.2 }}>CMS Portal</div>
            <div style={{ color: "rgba(255,255,255,0.4)", fontSize: 11 }}>v1.0</div>
          </div>
        </div>
      </div>
      <nav style={{ padding: "16px 12px", flex: 1 }}>
        {navItems.map(item => (
          <button key={item.key} onClick={() => setActive(item.key)}
            style={{ width: "100%", display: "flex", alignItems: "center", gap: 10, padding: "9px 12px", borderRadius: 8, background: active === item.key ? "rgba(255,255,255,0.12)" : "transparent", border: "none", cursor: "pointer", color: active === item.key ? "#fff" : "rgba(255,255,255,0.55)", fontSize: 13.5, fontWeight: active === item.key ? 600 : 400, marginBottom: 2, transition: "all 0.15s", textAlign: "left" }}>
            <span style={{ fontSize: 16 }}>{item.icon}</span>{item.label}
          </button>
        ))}
      </nav>
      <div style={{ padding: "16px 16px 20px", borderTop: "1px solid rgba(255,255,255,0.08)" }}>
        <div style={{ display: "flex", alignItems: "center", gap: 10, marginBottom: 10 }}>
          <div style={{ width: 34, height: 34, borderRadius: "50%", background: "linear-gradient(135deg, #4776e6, #8e54e9)", display: "flex", alignItems: "center", justifyContent: "center", color: "#fff", fontSize: 12, fontWeight: 700 }}>{initials(user.name)}</div>
          <div style={{ flex: 1, minWidth: 0 }}>
            <div style={{ color: "#fff", fontSize: 12.5, fontWeight: 600, overflow: "hidden", textOverflow: "ellipsis", whiteSpace: "nowrap" }}>{user.name}</div>
            <Badge status={user.role} />
          </div>
        </div>
        <button onClick={onLogout} style={{ width: "100%", padding: "7px", background: "rgba(255,255,255,0.07)", border: "1px solid rgba(255,255,255,0.12)", borderRadius: 7, color: "rgba(255,255,255,0.65)", fontSize: 12.5, cursor: "pointer" }}>
          Sign Out
        </button>
      </div>
    </div>
  );
}

function Dashboard({ articles, records, user }) {
  const stats = [
    { label: "Total Articles", value: articles.length, icon: "📄", color: "#4776e6" },
    { label: "Published", value: articles.filter(a => a.status === "Published").length, icon: "✅", color: "#27ae60" },
    { label: "Drafts", value: articles.filter(a => a.status === "Draft").length, icon: "✏️", color: "#f39c12" },
    { label: "Total Records", value: records.length, icon: "👥", color: "#8e54e9" },
  ];
  return (
    <div>
      <h2 style={{ margin: "0 0 6px", fontSize: 22, fontWeight: 700, color: "#1a1a2e" }}>Welcome back, {user.name.split(" ")[0]} 👋</h2>
      <p style={{ margin: "0 0 24px", color: "#888", fontSize: 14 }}>Here's a quick overview of your content.</p>
      <div style={{ display: "grid", gridTemplateColumns: "repeat(4, 1fr)", gap: 16, marginBottom: 28 }}>
        {stats.map(s => (
          <div key={s.label} style={{ background: "#fff", border: "1px solid #eee", borderRadius: 12, padding: "18px 16px", display: "flex", alignItems: "center", gap: 14 }}>
            <div style={{ width: 44, height: 44, borderRadius: 11, background: s.color + "18", display: "flex", alignItems: "center", justifyContent: "center", fontSize: 20 }}>{s.icon}</div>
            <div>
              <div style={{ fontSize: 24, fontWeight: 700, color: "#1a1a2e" }}>{s.value}</div>
              <div style={{ fontSize: 12, color: "#888" }}>{s.label}</div>
            </div>
          </div>
        ))}
      </div>
      <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: 20 }}>
        <div style={{ background: "#fff", border: "1px solid #eee", borderRadius: 12, padding: "18px 20px" }}>
          <h4 style={{ margin: "0 0 14px", fontSize: 14, fontWeight: 700, color: "#1a1a2e" }}>Recent Articles</h4>
          {articles.slice(0, 3).map(a => (
            <div key={a.id} style={{ display: "flex", alignItems: "center", justifyContent: "space-between", padding: "8px 0", borderBottom: "1px solid #f3f3f3" }}>
              <div>
                <div style={{ fontSize: 13.5, fontWeight: 500, color: "#1a1a2e" }}>{a.title}</div>
                <div style={{ fontSize: 11.5, color: "#aaa" }}>{a.category} · {a.date}</div>
              </div>
              <Badge status={a.status} />
            </div>
          ))}
        </div>
        <div style={{ background: "#fff", border: "1px solid #eee", borderRadius: 12, padding: "18px 20px" }}>
          <h4 style={{ margin: "0 0 14px", fontSize: 14, fontWeight: 700, color: "#1a1a2e" }}>Recent Records</h4>
          {records.slice(0, 3).map(r => (
            <div key={r.id} style={{ display: "flex", alignItems: "center", justifyContent: "space-between", padding: "8px 0", borderBottom: "1px solid #f3f3f3" }}>
              <div style={{ display: "flex", alignItems: "center", gap: 9 }}>
                <div style={{ width: 30, height: 30, borderRadius: "50%", background: "#e6f1fb", display: "flex", alignItems: "center", justifyContent: "center", color: "#185FA5", fontSize: 11, fontWeight: 700 }}>{initials(r.name)}</div>
                <div>
                  <div style={{ fontSize: 13.5, fontWeight: 500, color: "#1a1a2e" }}>{r.name}</div>
                  <div style={{ fontSize: 11.5, color: "#aaa" }}>{r.department}</div>
                </div>
              </div>
              <Badge status={r.status} />
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}

function ArticleForm({ initial, onSave, onClose, user }) {
  const [form, setForm] = useState(initial || { title: "", category: CATEGORIES[0], status: "Draft", content: "" });
  function submit() {
    if (!form.title.trim()) return alert("Title is required.");
    onSave({ ...form, author: user.name, date: new Date().toISOString().split("T")[0] });
  }
  const inp = { width: "100%", padding: "9px 11px", borderRadius: 7, border: "1.5px solid #e5e5e5", fontSize: 13.5, boxSizing: "border-box", fontFamily: "inherit", outline: "none" };
  return (
    <div>
      <div style={{ marginBottom: 12 }}>
        <label style={{ display: "block", fontSize: 12, fontWeight: 600, color: "#666", marginBottom: 5 }}>Title *</label>
        <input style={inp} value={form.title} onChange={e => setForm({ ...form, title: e.target.value })} placeholder="Article title..." />
      </div>
      <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: 12, marginBottom: 12 }}>
        <div>
          <label style={{ display: "block", fontSize: 12, fontWeight: 600, color: "#666", marginBottom: 5 }}>Category</label>
          <select style={inp} value={form.category} onChange={e => setForm({ ...form, category: e.target.value })}>
            {CATEGORIES.map(c => <option key={c}>{c}</option>)}
          </select>
        </div>
        <div>
          <label style={{ display: "block", fontSize: 12, fontWeight: 600, color: "#666", marginBottom: 5 }}>Status</label>
          <select style={inp} value={form.status} onChange={e => setForm({ ...form, status: e.target.value })}>
            <option>Draft</option><option>Published</option>
          </select>
        </div>
      </div>
      <div style={{ marginBottom: 18 }}>
        <label style={{ display: "block", fontSize: 12, fontWeight: 600, color: "#666", marginBottom: 5 }}>Content</label>
        <textarea style={{ ...inp, minHeight: 100, resize: "vertical" }} value={form.content} onChange={e => setForm({ ...form, content: e.target.value })} placeholder="Write your article content..." />
      </div>
      <div style={{ display: "flex", gap: 10, justifyContent: "flex-end" }}>
        <button onClick={onClose} style={{ padding: "8px 18px", borderRadius: 7, border: "1px solid #ddd", background: "#fff", cursor: "pointer", fontSize: 13.5, color: "#555" }}>Cancel</button>
        <button onClick={submit} style={{ padding: "8px 20px", borderRadius: 7, border: "none", background: "#1a1a2e", color: "#fff", cursor: "pointer", fontSize: 13.5, fontWeight: 600 }}>Save Article</button>
      </div>
    </div>
  );
}

function ArticlesPage({ articles, setArticles, user }) {
  const [search, setSearch] = useState("");
  const [filterStatus, setFilterStatus] = useState("All");
  const [modal, setModal] = useState(null);
  const [editItem, setEditItem] = useState(null);
  const [viewItem, setViewItem] = useState(null);

  const filtered = articles.filter(a =>
    (filterStatus === "All" || a.status === filterStatus) &&
    (a.title.toLowerCase().includes(search.toLowerCase()) || a.category.toLowerCase().includes(search.toLowerCase()))
  );

  function handleSave(data) {
    if (editItem) {
      setArticles(articles.map(a => a.id === editItem.id ? { ...a, ...data } : a));
    } else {
      setArticles([...articles, { ...data, id: Date.now() }]);
    }
    setModal(null); setEditItem(null);
  }
  function handleDelete(id) {
    if (window.confirm("Delete this article?")) setArticles(articles.filter(a => a.id !== id));
  }

  const th = { padding: "10px 14px", textAlign: "left", fontSize: 12, fontWeight: 600, color: "#888", borderBottom: "1px solid #eee", letterSpacing: 0.3 };
  const td = { padding: "12px 14px", fontSize: 13.5, color: "#333", borderBottom: "1px solid #f5f5f5" };

  return (
    <div>
      <div style={{ display: "flex", justifyContent: "space-between", alignItems: "center", marginBottom: 20 }}>
        <div>
          <h2 style={{ margin: 0, fontSize: 21, fontWeight: 700, color: "#1a1a2e" }}>Articles</h2>
          <p style={{ margin: "3px 0 0", color: "#888", fontSize: 13 }}>{filtered.length} articles found</p>
        </div>
        <button onClick={() => { setEditItem(null); setModal("form"); }}
          style={{ padding: "9px 18px", background: "#1a1a2e", color: "#fff", border: "none", borderRadius: 8, cursor: "pointer", fontSize: 13.5, fontWeight: 600, display: "flex", alignItems: "center", gap: 6 }}>
          + New Article
        </button>
      </div>
      <div style={{ display: "flex", gap: 10, marginBottom: 16 }}>
        <input value={search} onChange={e => setSearch(e.target.value)} placeholder="🔍 Search articles..."
          style={{ flex: 1, padding: "8px 12px", borderRadius: 7, border: "1.5px solid #e5e5e5", fontSize: 13.5, fontFamily: "inherit", outline: "none" }} />
        {["All", "Published", "Draft"].map(s => (
          <button key={s} onClick={() => setFilterStatus(s)}
            style={{ padding: "8px 14px", borderRadius: 7, border: "1.5px solid " + (filterStatus === s ? "#1a1a2e" : "#e5e5e5"), background: filterStatus === s ? "#1a1a2e" : "#fff", color: filterStatus === s ? "#fff" : "#555", cursor: "pointer", fontSize: 13, fontWeight: filterStatus === s ? 600 : 400 }}>
            {s}
          </button>
        ))}
      </div>
      <div style={{ background: "#fff", borderRadius: 12, border: "1px solid #eee", overflow: "hidden" }}>
        <table style={{ width: "100%", borderCollapse: "collapse" }}>
          <thead><tr style={{ background: "#fafafa" }}>
            <th style={th}>Title</th><th style={th}>Category</th><th style={th}>Author</th><th style={th}>Date</th><th style={th}>Status</th><th style={th}>Actions</th>
          </tr></thead>
          <tbody>
            {filtered.length === 0 ? (
              <tr><td colSpan={6} style={{ ...td, textAlign: "center", color: "#bbb", padding: 28 }}>No articles found.</td></tr>
            ) : filtered.map(a => (
              <tr key={a.id} style={{ transition: "background 0.1s" }} onMouseEnter={e => e.currentTarget.style.background = "#fafcff"} onMouseLeave={e => e.currentTarget.style.background = ""}>
                <td style={{ ...td, fontWeight: 500, color: "#1a1a2e" }}>{a.title}</td>
                <td style={td}><span style={{ background: "#f0f0ff", color: "#534AB7", borderRadius: 20, padding: "2px 9px", fontSize: 12 }}>{a.category}</span></td>
                <td style={td}>{a.author}</td>
                <td style={{ ...td, color: "#aaa" }}>{a.date}</td>
                <td style={td}><Badge status={a.status} /></td>
                <td style={td}>
                  <div style={{ display: "flex", gap: 6 }}>
                    <button onClick={() => setViewItem(a)} style={{ padding: "4px 10px", borderRadius: 6, border: "1px solid #ddd", background: "#fff", cursor: "pointer", fontSize: 12 }}>View</button>
                    <button onClick={() => { setEditItem(a); setModal("form"); }} style={{ padding: "4px 10px", borderRadius: 6, border: "1px solid #4776e6", background: "#f0f4ff", color: "#4776e6", cursor: "pointer", fontSize: 12, fontWeight: 500 }}>Edit</button>
                    <button onClick={() => handleDelete(a.id)} style={{ padding: "4px 10px", borderRadius: 6, border: "1px solid #ffcdd2", background: "#fff5f5", color: "#c0392b", cursor: "pointer", fontSize: 12, fontWeight: 500 }}>Del</button>
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
      {modal === "form" && (
        <Modal title={editItem ? "Edit Article" : "New Article"} onClose={() => { setModal(null); setEditItem(null); }}>
          <ArticleForm initial={editItem} onSave={handleSave} onClose={() => { setModal(null); setEditItem(null); }} user={user} />
        </Modal>
      )}
      {viewItem && (
        <Modal title={viewItem.title} onClose={() => setViewItem(null)}>
          <div>
            <div style={{ display: "flex", gap: 8, marginBottom: 14, flexWrap: "wrap" }}>
              <Badge status={viewItem.status} />
              <span style={{ background: "#f0f0ff", color: "#534AB7", borderRadius: 20, padding: "2px 9px", fontSize: 12 }}>{viewItem.category}</span>
            </div>
            <p style={{ margin: "0 0 8px", fontSize: 12.5, color: "#aaa" }}>By <b style={{ color: "#555" }}>{viewItem.author}</b> · {viewItem.date}</p>
            <hr style={{ border: "none", borderTop: "1px solid #eee", margin: "14px 0" }} />
            <p style={{ margin: 0, fontSize: 14, color: "#444", lineHeight: 1.7 }}>{viewItem.content || <i style={{ color: "#ccc" }}>No content</i>}</p>
            <button onClick={() => setViewItem(null)} style={{ marginTop: 20, padding: "8px 20px", borderRadius: 7, border: "none", background: "#1a1a2e", color: "#fff", cursor: "pointer", fontSize: 13.5 }}>Close</button>
          </div>
        </Modal>
      )}
    </div>
  );
}

function RecordForm({ initial, onSave, onClose }) {
  const [form, setForm] = useState(initial || { name: "", email: "", department: DEPARTMENTS[0], status: "Active", joined: new Date().toISOString().split("T")[0] });
  function submit() {
    if (!form.name.trim() || !form.email.trim()) return alert("Name and email are required.");
    onSave(form);
  }
  const inp = { width: "100%", padding: "9px 11px", borderRadius: 7, border: "1.5px solid #e5e5e5", fontSize: 13.5, boxSizing: "border-box", fontFamily: "inherit", outline: "none" };
  return (
    <div>
      {[["Name *", "name", "text", "Full name..."], ["Email *", "email", "email", "email@example.com"]].map(([label, key, type, ph]) => (
        <div key={key} style={{ marginBottom: 12 }}>
          <label style={{ display: "block", fontSize: 12, fontWeight: 600, color: "#666", marginBottom: 5 }}>{label}</label>
          <input type={type} style={inp} value={form[key]} onChange={e => setForm({ ...form, [key]: e.target.value })} placeholder={ph} />
        </div>
      ))}
      <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: 12, marginBottom: 12 }}>
        <div>
          <label style={{ display: "block", fontSize: 12, fontWeight: 600, color: "#666", marginBottom: 5 }}>Department</label>
          <select style={inp} value={form.department} onChange={e => setForm({ ...form, department: e.target.value })}>
            {DEPARTMENTS.map(d => <option key={d}>{d}</option>)}
          </select>
        </div>
        <div>
          <label style={{ display: "block", fontSize: 12, fontWeight: 600, color: "#666", marginBottom: 5 }}>Status</label>
          <select style={inp} value={form.status} onChange={e => setForm({ ...form, status: e.target.value })}>
            <option>Active</option><option>Inactive</option>
          </select>
        </div>
      </div>
      <div style={{ marginBottom: 18 }}>
        <label style={{ display: "block", fontSize: 12, fontWeight: 600, color: "#666", marginBottom: 5 }}>Joined Date</label>
        <input type="date" style={inp} value={form.joined} onChange={e => setForm({ ...form, joined: e.target.value })} />
      </div>
      <div style={{ display: "flex", gap: 10, justifyContent: "flex-end" }}>
        <button onClick={onClose} style={{ padding: "8px 18px", borderRadius: 7, border: "1px solid #ddd", background: "#fff", cursor: "pointer", fontSize: 13.5, color: "#555" }}>Cancel</button>
        <button onClick={submit} style={{ padding: "8px 20px", borderRadius: 7, border: "none", background: "#1a1a2e", color: "#fff", cursor: "pointer", fontSize: 13.5, fontWeight: 600 }}>Save Record</button>
      </div>
    </div>
  );
}

function RecordsPage({ records, setRecords, user }) {
  const [search, setSearch] = useState("");
  const [filterDept, setFilterDept] = useState("All");
  const [modal, setModal] = useState(null);
  const [editItem, setEditItem] = useState(null);

  const depts = ["All", ...DEPARTMENTS.filter(d => records.some(r => r.department === d))];
  const filtered = records.filter(r =>
    (filterDept === "All" || r.department === filterDept) &&
    (r.name.toLowerCase().includes(search.toLowerCase()) || r.email.toLowerCase().includes(search.toLowerCase()))
  );

  function handleSave(data) {
    if (editItem) {
      setRecords(records.map(r => r.id === editItem.id ? { ...r, ...data } : r));
    } else {
      setRecords([...records, { ...data, id: Date.now() }]);
    }
    setModal(null); setEditItem(null);
  }
  function handleDelete(id) {
    if (window.confirm("Delete this record?")) setRecords(records.filter(r => r.id !== id));
  }

  const th = { padding: "10px 14px", textAlign: "left", fontSize: 12, fontWeight: 600, color: "#888", borderBottom: "1px solid #eee", letterSpacing: 0.3 };
  const td = { padding: "12px 14px", fontSize: 13.5, color: "#333", borderBottom: "1px solid #f5f5f5" };

  return (
    <div>
      <div style={{ display: "flex", justifyContent: "space-between", alignItems: "center", marginBottom: 20 }}>
        <div>
          <h2 style={{ margin: 0, fontSize: 21, fontWeight: 700, color: "#1a1a2e" }}>Records</h2>
          <p style={{ margin: "3px 0 0", color: "#888", fontSize: 13 }}>{filtered.length} records found</p>
        </div>
        <button onClick={() => { setEditItem(null); setModal("form"); }}
          style={{ padding: "9px 18px", background: "#1a1a2e", color: "#fff", border: "none", borderRadius: 8, cursor: "pointer", fontSize: 13.5, fontWeight: 600 }}>
          + New Record
        </button>
      </div>
      <div style={{ display: "flex", gap: 10, marginBottom: 16, flexWrap: "wrap" }}>
        <input value={search} onChange={e => setSearch(e.target.value)} placeholder="🔍 Search records..."
          style={{ flex: 1, minWidth: 160, padding: "8px 12px", borderRadius: 7, border: "1.5px solid #e5e5e5", fontSize: 13.5, fontFamily: "inherit", outline: "none" }} />
        {depts.map(d => (
          <button key={d} onClick={() => setFilterDept(d)}
            style={{ padding: "8px 13px", borderRadius: 7, border: "1.5px solid " + (filterDept === d ? "#1a1a2e" : "#e5e5e5"), background: filterDept === d ? "#1a1a2e" : "#fff", color: filterDept === d ? "#fff" : "#555", cursor: "pointer", fontSize: 12.5, fontWeight: filterDept === d ? 600 : 400 }}>
            {d}
          </button>
        ))}
      </div>
      <div style={{ background: "#fff", borderRadius: 12, border: "1px solid #eee", overflow: "hidden" }}>
        <table style={{ width: "100%", borderCollapse: "collapse" }}>
          <thead><tr style={{ background: "#fafafa" }}>
            <th style={th}>Name</th><th style={th}>Email</th><th style={th}>Department</th><th style={th}>Joined</th><th style={th}>Status</th><th style={th}>Actions</th>
          </tr></thead>
          <tbody>
            {filtered.length === 0 ? (
              <tr><td colSpan={6} style={{ ...td, textAlign: "center", color: "#bbb", padding: 28 }}>No records found.</td></tr>
            ) : filtered.map(r => (
              <tr key={r.id} onMouseEnter={e => e.currentTarget.style.background = "#fafcff"} onMouseLeave={e => e.currentTarget.style.background = ""}>
                <td style={td}>
                  <div style={{ display: "flex", alignItems: "center", gap: 10 }}>
                    <div style={{ width: 32, height: 32, borderRadius: "50%", background: "#e6f1fb", display: "flex", alignItems: "center", justifyContent: "center", color: "#185FA5", fontSize: 11, fontWeight: 700, flexShrink: 0 }}>{initials(r.name)}</div>
                    <span style={{ fontWeight: 500, color: "#1a1a2e" }}>{r.name}</span>
                  </div>
                </td>
                <td style={{ ...td, color: "#4776e6" }}>{r.email}</td>
                <td style={td}><span style={{ background: "#f5f0ff", color: "#534AB7", borderRadius: 20, padding: "2px 9px", fontSize: 12 }}>{r.department}</span></td>
                <td style={{ ...td, color: "#aaa" }}>{r.joined}</td>
                <td style={td}><Badge status={r.status} /></td>
                <td style={td}>
                  <div style={{ display: "flex", gap: 6 }}>
                    <button onClick={() => { setEditItem(r); setModal("form"); }} style={{ padding: "4px 10px", borderRadius: 6, border: "1px solid #4776e6", background: "#f0f4ff", color: "#4776e6", cursor: "pointer", fontSize: 12, fontWeight: 500 }}>Edit</button>
                    <button onClick={() => handleDelete(r.id)} style={{ padding: "4px 10px", borderRadius: 6, border: "1px solid #ffcdd2", background: "#fff5f5", color: "#c0392b", cursor: "pointer", fontSize: 12, fontWeight: 500 }}>Del</button>
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
      {modal === "form" && (
        <Modal title={editItem ? "Edit Record" : "New Record"} onClose={() => { setModal(null); setEditItem(null); }}>
          <RecordForm initial={editItem} onSave={handleSave} onClose={() => { setModal(null); setEditItem(null); }} />
        </Modal>
      )}
    </div>
  );
}

export default function App() {
  const [user, setUser] = useState(null);
  const [page, setPage] = useState("dashboard");
  const [articles, setArticles] = useState(SAMPLE_ARTICLES);
  const [records, setRecords] = useState(SAMPLE_RECORDS);

  if (!user) return <LoginPage onLogin={setUser} />;

  return (
    <div style={{ display: "flex", minHeight: "100vh", background: "#f5f6fa", fontFamily: "'Segoe UI', system-ui, sans-serif" }}>
      <Sidebar active={page} setActive={setPage} user={user} onLogout={() => setUser(null)} />
      <main style={{ flex: 1, padding: "28px 32px", overflowY: "auto" }}>
        {page === "dashboard" && <Dashboard articles={articles} records={records} user={user} />}
        {page === "articles" && <ArticlesPage articles={articles} setArticles={setArticles} user={user} />}
        {page === "records" && <RecordsPage records={records} setRecords={setRecords} user={user} />}
      </main>
    </div>
  );
}
