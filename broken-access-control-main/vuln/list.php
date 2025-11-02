<?php
// vuln/list.php
// Layout-only update for the vulnerable items list (stored XSS demo).
// NOTE: This page intentionally preserves the original vulnerable behavior (no escaping of stored content).
require_once __DIR__ . '/../config.php';
if (empty($_SESSION['user'])) header('Location: ../login.php');

$res = $pdo->query("SELECT items_vuln.*, users.username FROM items_vuln JOIN users ON items_vuln.user_id = users.id ORDER BY items_vuln.id DESC");
?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <title>VULN — Items</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

    :root {
      --bg: #fff9f9;
      --card: #fff;
      --text: #2f2f33;
      --muted: #6b7280;
      --danger: #dc2626;
      --accent: #ef4444;
      --table-head: #f7eaea;
    }

    * {
      box-sizing: border-box
    }

    body {
      margin: 0;
      font-family: 'Poppins', system-ui, -apple-system, "Segoe UI", Roboto, Arial;
      background: linear-gradient(180deg, #fff8f8 0%, var(--bg) 100%);
      color: var(--text);
      padding: 28px;
    }

    .wrap {
      max-width: 1100px;
      margin: 0 auto;
    }

    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 12px;
      margin-bottom: 18px;
    }

    h1 {
      margin: 0;
      font-size: 20px;
    }

    .sub {
      color: var(--muted);
      font-size: 13px;
    }

    .controls {
      display: flex;
      gap: 10px;
      align-items: center;
    }

    .btn {
      padding: 10px 14px;
      border-radius: 10px;
      font-weight: 700;
      text-decoration: none;
      font-size: 14px;
      cursor: pointer;
      border: none;
    }

    .btn-create {
      background: linear-gradient(135deg, #ef4444, #dc2626);
      color: #fff;
      box-shadow: 6px 10px 20px rgba(220, 38, 38, 0.12);
    }

    .btn-back {
      background: transparent;
      color: var(--muted);
      border: 1px solid rgba(16, 24, 40, 0.05);
    }

    .card {
      background: var(--card);
      border-radius: 12px;
      padding: 18px;
      box-shadow: 8px 12px 26px rgba(16, 24, 40, 0.06);
    }

    .table-wrap {
      overflow: auto;
      margin-top: 12px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      min-width: 820px;
    }

    thead th {
      text-align: left;
      padding: 12px 14px;
      background: var(--table-head);
      font-weight: 700;
      font-size: 13px;
      color: var(--text);
      border-bottom: 1px solid rgba(16, 24, 40, 0.04);
    }

    tbody td {
      padding: 12px 14px;
      vertical-align: top;
      border-bottom: 1px solid rgba(16, 24, 40, 0.04);
      font-size: 14px;
      color: var(--text);
    }

    tbody tr:hover {
      background: rgba(239, 68, 68, 0.02);
    }

    .id {
      width: 64px;
      font-weight: 700;
      color: var(--muted);
    }

    .title {
      width: 220px;
      font-weight: 700;
      color: #111827;
    }

    .author {
      width: 160px;
      color: var(--muted);
    }

    .actions {
      width: 180px;
      text-align: right;
    }

    .link-action {
      color: var(--accent);
      text-decoration: none;
      font-weight: 700;
      margin-left: 8px;
    }

    .note {
      margin-top: 12px;
      color: var(--muted);
      font-size: 13px;
    }

    @media(max-width:880px) {
      table {
        min-width: 640px;
      }

      .controls {
        flex-direction: column;
        align-items: flex-end;
        gap: 8px;
      }
    }
  </style>
</head>

<body>
  <div class="wrap">
    <header>
      <div>
        <h1>VULN — Items</h1>
        <div class="sub">Daftar item (demo rentan). Konten disimpan tanpa sanitasi — stored XSS demo.</div>
      </div>

      <div class="controls">
        <a class="btn btn-create" href="create.php">+ Create</a>
        <a class="btn btn-back" href="../index.php">Back to Dashboard</a>
      </div>
    </header>

    <div class="card">
      <div class="table-wrap" role="region" aria-labelledby="tableTitle">
        <table aria-describedby="tableDesc">
          <thead>
            <tr>
              <th class="id">ID</th>
              <th class="title">Title</th>
              <th>Content</th>
              <th class="author">Author</th>
              <th class="actions">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($res as $r): ?>
              <tr>
                <td class="id"><?= $r['id'] ?></td>
                <td class="title"><?= $r['title'] ?></td>
                <!-- intentionally not escaped (stored XSS demonstration) -->
                <td><?= $r['content'] ?></td>
                <td class="author"><?= $r['username'] ?></td>
                <td class="actions">
                  <a href="edit.php?id=<?= $r['id'] ?>" class="link-action">Edit</a> |
                  <a href="delete.php?id=<?= $r['id'] ?>" class="link-action"
                    onclick="return confirm('Delete this item?')">Delete</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div class="note" id="tableDesc">
        ⚠️ Halaman ini sengaja rentan untuk keperluan pelatihan (stored XSS & lain-lain). Jangan jalankan di lingkungan produksi.
      </div>
    </div>
  </div>
</body>

</html>