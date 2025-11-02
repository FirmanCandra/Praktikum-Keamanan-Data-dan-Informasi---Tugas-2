<?php
// vuln/edit.php
// Layout-only update for the vulnerable edit page (keperluan demo).
// NOTE: Vulnerable SQL concatenation and missing ownership check intentionally preserved for training purposes.
require_once __DIR__ . '/../config.php';
if (empty($_SESSION['user'])) header('Location: ../login.php');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
  http_response_code(400);
  exit('Bad Request');
}

// Load (no ownership check)
$row = $pdo->query("SELECT * FROM items_vuln WHERE id = $id")->fetch(PDO::FETCH_ASSOC);
if (!$row) {
  http_response_code(404);
  exit('Not found');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = $_POST['title'] ?? '';
  $content = $_POST['content'] ?? '';
  // VULNERABLE: direct concatenation (demo only)
  $sql = "UPDATE items_vuln SET title = '{$title}', content = '{$content}' WHERE id = $id";
  $pdo->exec($sql);
  header('Location: list.php');
  exit;
}

// safe helper for output in form fields
function e($s)
{
  return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <title>Edit VULN Item — ID <?= e($row['id']) ?></title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

    :root {
      --bg: #fff8f8;
      --card: #fff;
      --text: #2f2f33;
      --muted: #6b7280;
      --accent: #ef4444;
      --danger: #dc2626;
    }

    * {
      box-sizing: border-box
    }

    body {
      margin: 0;
      font-family: 'Poppins', system-ui, -apple-system, "Segoe UI", Roboto, Arial;
      background: linear-gradient(180deg, #fff8f8 0%, var(--bg) 100%);
      color: var(--text);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 28px;
    }

    .wrap {
      width: 100%;
      max-width: 860px;
    }

    .card {
      background: var(--card);
      border-radius: 14px;
      padding: 24px;
      box-shadow: 10px 14px 30px rgba(220, 38, 38, 0.06), -8px -8px 20px rgba(255, 255, 255, 0.9);
      border: 1px solid rgba(220, 38, 38, 0.06);
    }

    header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
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
      gap: 12px;
    }

    label {
      font-weight: 700;
      font-size: 13px;
      color: #333;
    }

    input[type="text"],
    textarea {
      width: 100%;
      padding: 12px 14px;
      border-radius: 12px;
      border: none;
      background: #fff;
      box-shadow: inset 4px 4px 8px rgba(185, 185, 185, 0.18), inset -4px -4px 8px rgba(255, 255, 255, 0.9);
      font-size: 15px;
      outline: none;
      color: var(--text);
    }

    textarea {
      min-height: 160px;
      resize: vertical;
    }

    input:focus,
    textarea:focus {
      box-shadow: inset 2px 2px 6px rgba(184, 185, 190, 0.12), 0 8px 20px rgba(220, 38, 38, 0.06);
    }

    .actions {
      display: flex;
      gap: 12px;
      margin-top: 8px;
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
      background: linear-gradient(135deg, #ef4444, #dc2626);
      color: #fff;
      box-shadow: 6px 10px 20px rgba(220, 38, 38, 0.12);
    }

    .btn-ghost {
      background: transparent;
      border: 1px solid rgba(16, 24, 40, 0.06);
      color: var(--muted);
    }

    .note {
      margin-top: 14px;
      background: #fff5f5;
      color: var(--accent);
      padding: 10px 12px;
      border-radius: 10px;
      border: 1px solid rgba(220, 38, 38, 0.06);
      font-weight: 700;
    }

    @media(max-width:720px) {
      .wrap {
        padding: 12px;
      }
    }
  </style>
</head>

<body>
  <div class="wrap">
    <div class="card" role="main" aria-labelledby="title">
      <header>
        <div>
          <h1 id="title">Edit VULN Item (ID <?= e($row['id']) ?>)</h1>
          <div class="sub">Halaman ini sengaja rentan — ownership check & parametrized update tidak diterapkan.</div>
        </div>
        <div>
          <a href="list.php" class="btn btn-ghost" style="text-decoration:none;display:inline-block;padding:10px 12px;border-radius:10px;">← Back to list</a>
        </div>
      </header>

      <form method="post" novalidate>
        <div>
          <label for="title_input">Title</label>
          <input id="title_input" name="title" type="text" value="<?= e($row['title']) ?>" placeholder="Masukkan judul">
        </div>

        <div>
          <label for="content_input">Content</label>
          <textarea id="content_input" name="content" placeholder="Masukkan konten"><?= e($row['content']) ?></textarea>
        </div>

        <div class="actions">
          <button type="submit" class="btn btn-primary">Save</button>
          <a href="list.php" class="btn btn-ghost" style="text-decoration:none;display:inline-block;padding:12px 16px;border-radius:12px;">Cancel</a>
        </div>

        <div class="note" role="note">
          ⚠️ PERINGATAN: Update ini menggunakan concatenation langsung ke query — hanya untuk tujuan pembelajaran di lingkungan terisolasi.
        </div>
      </form>
    </div>
  </div>
</body>

</html>