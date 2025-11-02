<?php
// login_safe.php (VERSI AMAN — tampilan modern)
// Backend tetap aman: prepared statements + password_verify + session_regenerate_id

session_start();

$dsn = 'mysql:host=127.0.0.1;dbname=praktek_sqli;charset=utf8mb4';
$dbUser = 'root';
$dbPass = '';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'] ?? '';
  $password = $_POST['password'] ?? '';

  try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    // --- prepared statement (AMAN) ---
    $stmt = $pdo->prepare("SELECT id, username, password_hash, full_name FROM users_safe WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
      session_regenerate_id(true);
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['username'] = $user['username'];
      $_SESSION['full_name'] = $user['full_name'];
      $_SESSION['demo_mode'] = 'safe';
      header('Location: dashboard.php');
      exit;
    } else {
      $message = 'Username atau password salah.';
    }
  } catch (PDOException $e) {
    $message = 'Terjadi kesalahan server.';
  }
}
?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <title>Login (Aman)</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    /* Matches create_user_safe_form.php theme (neumorphism, soft colors) */
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
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      padding: 20px;
    }

    .container {
      width: 100%;
      max-width: 420px;
      background: var(--card);
      border-radius: 16px;
      padding: 32px;
      box-shadow: 10px 10px 30px rgba(16, 24, 40, 0.06), -8px -8px 20px rgba(255, 255, 255, 0.6);
    }

    .header {
      text-align: center;
      margin-bottom: 18px;
    }

    .header h2 {
      margin: 0;
      font-size: 20px;
      letter-spacing: 0.2px;
    }

    .header p {
      margin: 6px 0 0;
      color: var(--muted);
      font-size: 13px;
    }

    .status {
      text-align: center;
      margin-bottom: 14px;
      font-weight: 600;
      color: #9f1239;
      background: #fff5f5;
      padding: 10px;
      border-radius: 10px;
      border: 1px solid rgba(159, 18, 57, 0.06);
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    label {
      font-weight: 600;
      color: #2b3440;
      font-size: 13px;
    }

    input[type="text"],
    input[type="password"] {
      width: 100%;
      padding: 12px 14px;
      border-radius: 12px;
      border: none;
      background: #e9f0f8;
      box-shadow: inset 4px 4px 8px rgba(184, 185, 190, 0.35), inset -4px -4px 8px rgba(255, 255, 255, 0.9);
      font-size: 15px;
      outline: none;
      color: var(--text);
      transition: box-shadow .15s ease, transform .06s ease;
    }

    input:focus {
      box-shadow: inset 2px 2px 6px rgba(184, 185, 190, 0.25), inset -2px -2px 6px rgba(255, 255, 255, 0.95);
    }

    .actions {
      display: flex;
      gap: 10px;
      align-items: center;
      margin-top: 6px;
    }

    .btn {
      flex: 1;
      padding: 12px 14px;
      border-radius: 12px;
      border: none;
      cursor: pointer;
      font-weight: 700;
      font-size: 15px;
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

    .links {
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 13px;
      margin-top: 6px;
      color: var(--muted);
    }

    .links a {
      color: var(--primary);
      text-decoration: none;
      font-weight: 600;
    }

    .note {
      text-align: center;
      color: var(--muted);
      font-size: 12px;
      margin-top: 14px;
    }

    @media (max-width:480px) {
      .container {
        padding: 22px;
      }
    }
  </style>
</head>

<body>
  <main class="container" role="main" aria-labelledby="loginTitle">
    <header class="header">
      <h2 id="loginTitle">LOGIN — VERSI AMAN</h2>
      <p>Masuk menggunakan akun yang sudah terdaftar (safe).</p>
    </header>

    <?php if ($message): ?>
      <div class="status" role="status"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="post" action="" novalidate>
      <label for="username">Username</label>
      <input id="username" name="username" type="text" required autocomplete="username" placeholder="username">

      <label for="password">Password</label>
      <input id="password" name="password" type="password" required autocomplete="current-password" placeholder="••••••••">

      <div class="actions">
        <button type="submit" class="btn btn-primary">Login</button>

      </div>



      <div class="note">🔒 Prepared statements + password hashing digunakan di sisi pendaftaran (safe).</div>
    </form>
  </main>
</body>

</html>