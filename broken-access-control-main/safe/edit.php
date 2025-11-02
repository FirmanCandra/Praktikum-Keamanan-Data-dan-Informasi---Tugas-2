<?php
// safe/edit.php
// Layout-only update for SAFE edit page. Business logic (CSRF + ownership + UUID validation) tetap sama.
require_once __DIR__ . '/../config.php';
if (empty($_SESSION['user'])) header('Location: ../login.php');

$uuid = $_GET['u'] ?? ($_POST['uuid'] ?? '');
if (!$uuid) {
  http_response_code(400);
  exit('Missing uuid');
}

$stmt = $pdo->prepare("SELECT * FROM items_safe WHERE uuid = :u LIMIT 1");
$stmt->execute([':u' => $uuid]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$item) {
  http_response_code(404);
  exit('Not found');
}

// Ownership check
if ($item['user_id'] != $_SESSION['user']['id']) {
  http_response_code(403);
  exit('Forbidden: not owner');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!check_csrf($_POST['csrf'] ?? '')) {
    http_response_code(400);
    exit('CSRF fail');
  }
  $title = trim($_POST['title'] ?? '');
  $content = trim($_POST['content'] ?? '');
  $stmt = $pdo->prepare("UPDATE items_safe SET title = :t, content = :c WHERE uuid = :u");
  $stmt->execute([':t' => $title, ':c' => $content, ':u' => $uuid]);
  header('Location: list.php');
  exit;
}

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
  <title>Edit SAFE Item — <?= e($item['uuid']) ?></title>
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
    }

    * {
      box-sizing: border-box
    }

    body {
      margin: 0;
      font-family: 'Poppins', system-ui, -apple-system, "Segoe UI", Roboto, Arial;
      background: linear-gradient(180deg, #f8fbff 0%, var(--bg) 100%);
      color: var(--text);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px;
    }

    .wrap {
      width: 100%;
      max-width: 780px;
    }

    .card {
      background: var(--card);
      border-radius: 16px;
      padding: 28px;
      box-shadow: 12px 12px 30px rgba(16, 24, 40, 0.06), -8px -8px 20px rgba(255, 255, 255, 0.6);
    }

    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 18px;
    }

    h1 {
      margin: 0;
      font-size: 20px;
      color: var(--accent);
    }

    .sub {
      color: var(--muted);
      font-size: 13px;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 14px;
    }

    label {
      font-weight: 700;
      font-size: 13px;
      color: #2b3440;
    }

    input[type="text"],
    textarea {
      width: 100%;
      padding: 12px 14px;
      border-radius: 12px;
      border: none;
      background: #eaf4ff;
      box-shadow: inset 4px 4px 8px rgba(184, 185, 190, 0.25), inset -4px -4px 8px rgba(255, 255, 255, 0.9);
      font-size: 15px;
      outline: none;
      color: var(--text);
    }

    textarea {
      min-height: 140px;
      resize: vertical;
    }

    input:focus,
    textarea:focus {
      box-shadow: inset 2px 2px 6px rgba(184, 185, 190, 0.18), 0 6px 18px rgba(37, 99, 235, 0.06);
    }

    .actions {
      display: flex;
      gap: 12px;
      margin-top: 6px;
      flex-wrap: wrap;
    }

    .btn {
      padding: 12px 16px;
      border-radius: 12px;
      border: none;
      cursor: pointer;
      font-weight: 700;
      font-size: 14px;
    }

    .btn-primary {
      background: linear-gradient(135deg, #10b981, #059669);
      color: #fff;
      box-shadow: 6px 10px 20px rgba(6, 95, 70, 0.12);
    }

    .btn-ghost {
      background: transparent;
      border: 1px solid rgba(16, 24, 40, 0.06);
      color: var(--muted);
    }

    .note {
      margin-top: 12px;
      color: var(--muted);
      font-size: 13px;
    }

    @media(max-width:600px) {
      .card {
        padding: 20px;
      }

      .actions {
        flex-direction: column;
      }

      .btn {
        width: 100%;
      }
    }
  </style>
</head>

<body>
  <div class="wrap">
    <div class="card" role="main" aria-labelledby="title">
      <header>
        <div>
          <h1 id="title">Edit SAFE Item</h1>
          <div class="sub">UUID: <?= e($item['uuid']) ?> — Hanya pemilik item yang dapat mengedit.</div>
        </div>
        <div>
          <a href="list.php" class="btn btn-ghost" style="text-decoration:none;padding:10px 12px;border-radius:10px;">← Back to list</a>
        </div>
      </header>

      <form method="post" novalidate>
        <div>
          <label for="title">Title</label>
          <input id="title" name="title" type="text" value="<?= e($item['title']) ?>" placeholder="Masukkan judul">
        </div>

        <div>
          <label for="content">Content</label>
          <textarea id="content" name="content" placeholder="Masukkan konten"><?= e($item['content']) ?></textarea>
        </div>

        <input type="hidden" name="uuid" value="<?= e($item['uuid']) ?>">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

        <div class="actions">
          <button type="submit" class="btn btn-primary">Save</button>
          <a href="list.php" class="btn btn-ghost" style="text-decoration:none;padding:12px 14px;border-radius:12px;">Cancel</a>
        </div>

        <div class="note">
          ✅ SAFE: Update menggunakan prepared statement dan verifikasi kepemilikan. UUID digunakan untuk menghindari IDOR.
        </div>
      </form>
    </div>
  </div>
</body>

</html>