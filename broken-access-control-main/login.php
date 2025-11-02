<?php
require 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $u = trim($_POST['username']);
  $p = $_POST['password'];
  $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :u LIMIT 1");
  $stmt->execute([':u' => $u]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);
  // if ($user && password_verify($p, $user['password'])) {
  if ($user && $user['password']) {
    session_regenerate_id(true);
    $_SESSION['user'] = ['id' => $user['id'], 'username' => $user['username'], 'role' => $user['role']];
    header('Location: index.php');
    exit;
  } else $err = "Login gagal.";
}
?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <title>Login — Demo Security</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

    :root {
      --bg: #eef5fb;
      --card: #ecf5fc;
      --text: #263245;
      --muted: #6b7280;
      --accent: #2563eb;
      --danger: #dc2626;
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

    .card {
      width: 100%;
      max-width: 420px;
      background: var(--card);
      border-radius: 16px;
      padding: 32px;
      box-shadow: 12px 12px 30px rgba(16, 24, 40, 0.06), -8px -8px 20px rgba(255, 255, 255, 0.6);
    }

    h3 {
      margin-top: 0;
      text-align: center;
      font-size: 22px;
      font-weight: 700;
      color: var(--text);
    }

    .msg {
      margin-bottom: 12px;
      background: #fee2e2;
      color: var(--danger);
      border: 1px solid rgba(220, 38, 38, 0.15);
      padding: 10px 12px;
      border-radius: 10px;
      font-weight: 600;
      text-align: center;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 14px;
    }

    input {
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

    input:focus {
      box-shadow: inset 2px 2px 6px rgba(184, 185, 190, 0.18), 0 6px 18px rgba(37, 99, 235, 0.08);
    }

    button {
      background: linear-gradient(135deg, #4a90e2, #357abd);
      color: white;
      font-weight: 700;
      padding: 12px;
      border: none;
      border-radius: 12px;
      cursor: pointer;
      font-size: 15px;
      box-shadow: 6px 10px 20px rgba(53, 122, 189, 0.12);
      transition: all .2s ease;
    }

    button:hover {
      transform: translateY(-2px);
      box-shadow: 6px 12px 22px rgba(53, 122, 189, 0.2);
    }

    .footer {
      text-align: center;
      margin-top: 16px;
      color: var(--muted);
      font-size: 13px;
    }

    .footer a {
      color: var(--accent);
      text-decoration: none;
      font-weight: 600;
    }
  </style>
</head>

<body>
  <div class="card">
    <form method="post">
      <h3>Login ke Aplikasi</h3>

      <?php if (isset($err)): ?>
        <div class="msg"><?= htmlspecialchars($err) ?></div>
      <?php endif; ?>

      <input name="username" placeholder="Username" required>
      <input name="password" type="password" placeholder="Password" required>
      <button type="submit">Login</button>

      <div class="footer">
        Belum punya akun? <a href="register.php">Daftar</a>
      </div>
    </form>
  </div>
</body>

</html>