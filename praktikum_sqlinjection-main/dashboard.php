<?php
// dashboard.php — tampilan modern (neumorphism) dengan tombol Logout
session_start();
if (!isset($_SESSION['user_id'])) {
  header('Location: login_safe.php');
  exit;
}

function e($s)
{
  return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$userId   = $_SESSION['user_id'] ?? '';
$username = $_SESSION['username'] ?? '';
$fullName = $_SESSION['full_name'] ?? '';
$demoMode = $_SESSION['demo_mode'] ?? 'unknown';
?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <title>Dashboard — <?= e($username ?: $fullName ?: 'User') ?></title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    * {
      box-sizing: border-box;
    }

    :root {
      --bg: #e9eff6;
      --card: #e8eef6;
      --text: #263245;
      --muted: #6b7280;
      --primary: #2563eb;
    }

    body {
      margin: 0;
      font-family: 'Poppins', system-ui, -apple-system, "Segoe UI", Roboto, Arial;
      background: linear-gradient(180deg, #f7fbfe 0%, var(--bg) 100%);
      color: var(--text);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 30px;
    }

    .container {
      width: 100%;
      max-width: 900px;
      background: var(--card);
      border-radius: 20px;
      padding: 32px;
      box-shadow: 10px 10px 30px rgba(16, 24, 40, 0.06), -8px -8px 20px rgba(255, 255, 255, 0.6);
    }

    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 24px;
    }

    header h1 {
      margin: 0;
      font-size: 22px;
      letter-spacing: 0.3px;
    }

    .badge {
      background: #eef2ff;
      color: #1e3a8a;
      padding: 6px 10px;
      border-radius: 12px;
      font-weight: 700;
      font-size: 13px;
    }

    .logout-btn {
      padding: 10px 16px;
      background: linear-gradient(135deg, #ef4444, #dc2626);
      color: #fff;
      font-weight: 700;
      border: none;
      border-radius: 12px;
      cursor: pointer;
      box-shadow: 6px 10px 20px rgba(239, 68, 68, 0.18);
      transition: background 0.3s ease;
    }

    .logout-btn:hover {
      background: linear-gradient(135deg, #f87171, #dc2626);
    }

    .profile {
      display: flex;
      gap: 16px;
      align-items: center;
      margin-bottom: 20px;
    }

    .avatar {
      width: 64px;
      height: 64px;
      border-radius: 14px;
      background: linear-gradient(135deg, #dbe9ff, #f0f6ff);
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 22px;
      color: #1e3a8a;
      box-shadow: inset 4px 4px 8px rgba(255, 255, 255, 0.9), 6px 10px 20px rgba(37, 99, 235, 0.08);
    }

    .meta .name {
      font-weight: 700;
      font-size: 17px;
    }

    .meta .small {
      font-size: 13px;
      color: var(--muted);
    }

    .info-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 14px;
      margin: 20px 0;
    }

    .info {
      background: #fdfdfd;
      border-radius: 12px;
      padding: 14px;
      font-size: 14px;
      box-shadow: inset 2px 2px 6px rgba(255, 255, 255, 0.7);
    }

    .info b {
      display: block;
      margin-bottom: 6px;
      color: #334155;
    }

    .actions {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }

    .btn {
      padding: 12px 14px;
      border-radius: 12px;
      border: none;
      cursor: pointer;
      font-weight: 700;
      font-size: 15px;
      flex: 1;
      min-width: 130px;
    }

    .btn-primary {
      background: linear-gradient(135deg, #4a90e2, #357abd);
      color: white;
      box-shadow: 6px 10px 20px rgba(53, 122, 189, 0.18);
    }

    .btn-secondary {
      background: transparent;
      border: 1px solid rgba(16, 24, 40, 0.06);
      color: var(--muted);
    }

    footer {
      text-align: center;
      color: var(--muted);
      font-size: 13px;
      margin-top: 24px;
    }

    @media(max-width:700px) {
      .info-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>

<body>
  <div class="container">
    <header>
      <div>
        <h1>Dashboard</h1>
        <span class="badge"><?= e(strtoupper($demoMode)) ?></span>
      </div>
      <form action="logout.php" method="post">
        <button type="submit" class="logout-btn">Logout</button>
      </form>
    </header>

    <section class="profile">
      <div class="avatar"><?= e(strtoupper(substr($username ?: $fullName, 0, 2))) ?></div>
      <div class="meta">
        <div class="name"><?= e($fullName ?: $username) ?></div>
        <div class="small">ID: <?= e($userId) ?> · Username: <?= e($username) ?></div>
      </div>
    </section>

    <div class="info-grid">
      <div class="info">
        <b>Mode Demo</b>
        <?= e($demoMode) ?>
      </div>
      <div class="info">
        <b>Status Sesi</b>
        Aktif — sesi berjalan normal.
      </div>
    </div>

    <div class="actions">
      <button class="btn btn-primary" onclick="location.href='create_user_safe_form.php'">Create User (Safe)</button>
      <button class="btn btn-secondary" onclick="location.href='create_user_vul_form.php'">Create User (Vul)</button>
      <button class="btn btn-secondary" onclick="location.href='login_safe.php'">Login Safe</button>
      <button class="btn btn-secondary" onclick="location.href='login_vul.php'">Login Vul</button>
    </div>

    <footer>
      Halaman ini hanya contoh dashboard untuk keperluan demo SQL Injection & keamanan web.
    </footer>
  </div>
</body>

</html>