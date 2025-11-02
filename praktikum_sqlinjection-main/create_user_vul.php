<?php
// create_user_vul_form.php
// DEMO ONLY: VULNERABLE user creation form — gunakan hanya di lab lokal/VM
// Catatan penting: file ini SENGAJA rentan (plain SQL concatenation, password plaintext).
// Jangan gunakan di lingkungan publik/produksi.

$dsn = 'mysql:host=127.0.0.1;dbname=praktek_sqli;charset=utf8mb4';
$dbUser = 'root';
$dbPass = ''; // sesuaikan jika perlu

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'] ?? '';
  $password = $_POST['password'] ?? '';
  $fullname = $_POST['full_name'] ?? '';

  try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    // VULNERABLE: menyimpan password plaintext dan concatenation query (DEMO saja)
    $sql = "INSERT INTO users_vul (username, password, full_name) VALUES ('"
      . $username . "', '" . $password . "', '" . $fullname . "')";
    $pdo->exec($sql);

    $message = "User rentan berhasil dibuat: " . htmlspecialchars($username);
  } catch (PDOException $e) {
    $message = "Terjadi kesalahan server (demo).";
  }
}
?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <title>Create User (VULNERABLE)</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    /* theme matches the safe modern layout (neumorphism, soft colors) */
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
      max-width: 480px;
      background: var(--card);
      border-radius: 16px;
      padding: 32px;
      box-shadow: 10px 10px 30px rgba(16, 24, 40, 0.06), -8px -8px 20px rgba(255, 255, 255, 0.6);
    }

    h2 {
      text-align: center;
      margin: 0 0 14px;
      font-size: 20px;
      letter-spacing: 0.2px;
    }

    .sub {
      text-align: center;
      color: var(--muted);
      font-size: 13px;
      margin-bottom: 18px;
    }

    .status {
      text-align: center;
      margin-bottom: 14px;
      font-weight: 600;
      color: #065f46;
      background: #f0fdf4;
      padding: 10px;
      border-radius: 10px;
      border: 1px solid rgba(6, 95, 70, 0.06);
    }

    .err {
      background: #fff5f5;
      color: #9f1239;
      border-radius: 10px;
      padding: 10px;
      margin-bottom: 12px;
      font-size: 14px;
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

    .btn-ghost {
      background: transparent;
      border: 1px solid rgba(16, 24, 40, 0.06);
      color: var(--muted);
    }

    .note {
      text-align: center;
      color: var(--muted);
      font-size: 13px;
      margin-top: 14px;
    }

    @media (max-width:520px) {
      .container {
        padding: 22px;
      }
    }
  </style>
</head>

<body>
  <div class="container" role="main" aria-labelledby="title">
    <h2 id="title">CREATE USER — VERSI RENTAN (DEMO)</h2>
    <div class="sub">Form ini sengaja rentan untuk praktik di lab lokal/VM — jangan gunakan di publik.</div>

    <?php if (!empty($message)): ?>
      <div class="status"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="post" action="">
      <label for="username">Username</label>
      <input id="username" name="username" type="text" required placeholder="ex: admin' OR '1'='1">

      <label for="password">Password (plaintext — demo)</label>
      <input id="password" name="password" type="text" required placeholder="password-demo">

      <label for="full_name">Full name</label>
      <input id="full_name" name="full_name" type="text" placeholder="Nama lengkap">

      <div class="actions">
        <button type="submit" class="btn btn-primary">Buat User (vul)</button>
        <button type="button" class="btn btn-ghost" onclick="showHint()">Petunjuk</button>
      </div>

      <div class="note">Contoh DB: <code>users_vul</code> — plain text password & query concatenation.</div>
    </form>
  </div>

  <script>
    function showHint() {
      alert("Contoh payload SQLi (DEMO):\nusername: test' OR '1'='1\npassword: anything\n\nGunakan hanya di lingkungan lokal/terisolasi.");
    }
  </script>
</body>

</html>