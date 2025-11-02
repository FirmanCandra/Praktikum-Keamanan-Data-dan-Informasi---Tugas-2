<?php
require 'config.php';
if (empty($_SESSION['user'])) header('Location: login.php');
$user = $_SESSION['user'];
?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <title>Dashboard — Demo Security</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

    :root {
      --bg: #eef5fb;
      --card: #ecf5fc;
      --text: #263245;
      --muted: #6b7280;
      --accent: #2563eb;
      --danger: #dc2626;
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
      padding: 32px;
    }

    .dashboard {
      width: 100%;
      max-width: 900px;
      background: var(--card);
      border-radius: 16px;
      padding: 32px;
      box-shadow: 12px 12px 30px rgba(16, 24, 40, 0.06), -8px -8px 20px rgba(255, 255, 255, 0.6);
    }

    h1 {
      text-align: center;
      margin-top: 0;
      font-size: 24px;
    }

    .areas {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
      margin-top: 28px;
    }

    .card {
      border-radius: 16px;
      padding: 24px;
      background: white;
      box-shadow: inset 4px 4px 8px rgba(184, 185, 190, 0.25), inset -4px -4px 8px rgba(255, 255, 255, 0.9);
    }

    .card h3 {
      margin-top: 0;
      font-size: 18px;
    }

    .card p {
      font-size: 14px;
      color: var(--muted);
      line-height: 1.4;
    }

    .card.vuln {
      border-top: 4px solid var(--danger);
    }

    .card.safe {
      border-top: 4px solid var(--success);
    }

    .btn {
      display: inline-block;
      margin-top: 12px;
      padding: 10px 14px;
      border-radius: 12px;
      font-weight: 700;
      text-decoration: none;
      transition: all .2s ease-in-out;
    }

    .btn-vuln {
      background: linear-gradient(135deg, #ef4444, #dc2626);
      color: #fff;
      box-shadow: 6px 10px 20px rgba(220, 38, 38, 0.15);
    }

    .btn-safe {
      background: linear-gradient(135deg, #4ade80, #16a34a);
      color: #fff;
      box-shadow: 6px 10px 20px rgba(22, 163, 74, 0.15);
    }

    .logout {
      text-align: center;
      margin-top: 28px;
    }

    .logout a {
      display: inline-block;
      color: var(--muted);
      text-decoration: none;
      font-weight: 600;
      border: 1px solid rgba(16, 24, 40, 0.1);
      padding: 10px 16px;
      border-radius: 12px;
      transition: all .2s;
    }

    .logout a:hover {
      background: rgba(16, 24, 40, 0.04);
    }

    @media(max-width:700px) {
      .areas {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>

<body>
  <div class="dashboard">
    <h1>Selamat datang, <?= htmlspecialchars($user['username']) ?> 👋</h1>

    <div class="areas">
      <div class="card vuln">
        <h3>⚠️ VULNERABLE AREA</h3>
        <p>Contoh <b>Broken Access Control (IDOR)</b> — tanpa validasi kepemilikan data.</p>
        <a href="vuln/list.php" class="btn btn-vuln">Masuk ke Area Rentan</a>
      </div>

      <div class="card safe">
        <h3>✅ SAFE AREA</h3>
        <p>Versi aman dengan <b>UUID</b>, <b>Token</b>, dan <b>Ownership Check</b> untuk mencegah IDOR.</p>
        <a href="safe/list.php" class="btn btn-safe">Masuk ke Area Aman</a>
      </div>
    </div>

    <div class="logout">
      <a href="logout.php">Logout</a>
    </div>
  </div>
</body>

</html>