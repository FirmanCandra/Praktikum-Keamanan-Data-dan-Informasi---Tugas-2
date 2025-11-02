<?php
// safe/list.php
// Layout-only update for SAFE items list. Backend logic unchanged (prepared statements, ownership checks).
require_once __DIR__ . '/../config.php';
if (empty($_SESSION['user'])) header('Location: ../login.php');

$stmt = $pdo->prepare("SELECT id, uuid, title, created_at FROM items_safe WHERE user_id = :u ORDER BY created_at DESC");
$stmt->execute([':u' => $_SESSION['user']['id']]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// helper
function e($s)
{
  return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <title>SAFE — Items (Your Items)</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

    :root {
      --bg: #eef5fb;
      --card: #ecf5fc;
      --text: #263245;
      --muted: #6b7280;
      --accent: #2563eb;
      --success: #16a34a;
      --table-head: #e8f6ef;
    }

    * {
      box-sizing: border-box
    }

    body {
      margin: 0;
      font-family: 'Poppins', system-ui, -apple-system, "Segoe UI", Roboto, Arial;
      background: linear-gradient(180deg, #f8fbff 0%, var(--bg) 100%);
      color: var(--text);
      padding: 28px;
      min-height: 100vh;
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
      background: linear-gradient(135deg, #10b981, #059669);
      color: #fff;
      box-shadow: 6px 10px 20px rgba(6, 95, 70, 0.12);
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
      min-width: 720px;
      background: transparent;
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
      background: rgba(16, 185, 129, 0.02);
    }

    .uuid {
      width: 260px;
      font-family: monospace;
      color: #0f5132;
    }

    .title {
      width: 320px;
      font-weight: 700;
      color: #0b3b2e;
    }

    .created {
      width: 180px;
      color: var(--muted);
    }

    .actions {
      width: 220px;
      text-align: right;
    }

    .link-action {
      color: var(--accent);
      text-decoration: none;
      font-weight: 700;
      margin-left: 8px;
      display: inline-block;
    }

    form.inline {
      display: inline-block;
      margin: 0;
    }

    form.inline button {
      background: transparent;
      border: 1px solid rgba(16, 24, 40, 0.06);
      padding: 6px 10px;
      border-radius: 8px;
      cursor: pointer;
      font-weight: 700;
      color: var(--muted);
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
        <h1>SAFE — Items (Your Items)</h1>
        <div class="sub">Hanya menampilkan item milik Anda. UUID & CSRF digunakan untuk operasi sensitif.</div>
      </div>

      <div class="controls" role="group" aria-label="Controls">
        <a class="btn btn-create" href="create.php">+ Create</a>
        <a class="btn btn-back" href="../index.php">Back to Dashboard</a>
      </div>
    </header>

    <div class="card">
      <div class="table-wrap" role="region" aria-labelledby="tableTitle">
        <table aria-describedby="tableDesc">
          <thead>
            <tr>
              <th class="uuid">UUID</th>
              <th class="title">Title</th>
              <th class="created">Created</th>
              <th class="actions">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($rows as $r): ?>
              <tr>
                <td class="uuid"><?= e($r['uuid']) ?></td>
                <td class="title"><?= e($r['title']) ?></td>
                <td class="created"><?= e($r['created_at']) ?></td>
                <td class="actions" aria-hidden="false">
                  <a class="link-action" href="view.php?u=<?= urlencode($r['uuid']) ?>">View</a> |
                  <a class="link-action" href="edit.php?u=<?= urlencode($r['uuid']) ?>">Edit</a> |
                  <form action="delete.php" method="post" class="inline" onsubmit="return confirm('Delete this item?')">
                    <input type="hidden" name="uuid" value="<?= e($r['uuid']) ?>">
                    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                    <button type="submit">Delete</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($rows)): ?>
              <tr>
                <td colspan="4" style="text-align:center;color:var(--muted);padding:30px 14px">You don't have any items yet.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <div class="note" id="tableDesc">
        ✅ SAFE area: operations require UUID (unguessable) and CSRF token for destructive actions. Good for preventing IDOR & CSRF.
      </div>
    </div>
  </div>
</body>

</html>