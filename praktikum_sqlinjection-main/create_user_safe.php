<?php
session_start();

$dsn = 'mysql:host=127.0.0.1;dbname=praktek_sqli;charset=utf8mb4';
$dbUser = 'root';
$dbPass = '';

if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
}

$message = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $token = $_POST['csrf_token'] ?? '';
  if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
    $errors[] = 'Token CSRF tidak valid.';
  }

  $username = trim($_POST['username'] ?? '');
  $password = $_POST['password'] ?? '';
  $fullname = trim($_POST['full_name'] ?? '');

  if ($username === '' || $password === '') {
    $errors[] = 'Username dan password wajib diisi.';
  } else {
    if (!preg_match('/^[A-Za-z0-9_]{3,30}$/', $username)) {
      $errors[] = 'Username hanya boleh huruf, angka, underscore; 3-30 karakter.';
    }
    if (strlen($password) < 8) {
      $errors[] = 'Password minimal 8 karakter.';
    }
  }

  if (empty($errors)) {
    try {
      $pdo = new PDO($dsn, $dbUser, $dbPass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
      $stmt = $pdo->prepare("SELECT id FROM users_safe WHERE username = ?");
      $stmt->execute([$username]);
      if ($stmt->fetch()) {
        $errors[] = 'Username sudah terdaftar. Pilih username lain.';
      } else {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users_safe (username, password_hash, full_name) VALUES (?, ?, ?)");
        $stmt->execute([$username, $passwordHash, $fullname]);
        $message = "User berhasil dibuat: " . htmlspecialchars($username);
        $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
      }
    } catch (PDOException $e) {
      $errors[] = 'Terjadi kesalahan server. Coba lagi nanti.';
    }
  }
}
?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <title>Create User (Safe Modern UI)</title>
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: #e0e5ec;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      margin: 0;
    }

    .container {
      background: #e0e5ec;
      box-shadow: 8px 8px 16px #b8b9be, -8px -8px 16px #ffffff;
      border-radius: 16px;
      padding: 40px;
      width: 100%;
      max-width: 420px;
    }

    h2 {
      text-align: center;
      color: #333;
      margin-bottom: 24px;
    }

    label {
      font-weight: 600;
      color: #444;
      display: block;
      margin-bottom: 6px;
      margin-top: 10px;
    }

    input[type="text"],
    input[type="password"] {
      width: 100%;
      padding: 12px;
      border: none;
      border-radius: 12px;
      background: #e0e5ec;
      box-shadow: inset 4px 4px 8px #b8b9be, inset -4px -4px 8px #ffffff;
      outline: none;
      font-size: 15px;
      color: #333;
      transition: all 0.2s ease-in-out;
    }

    input:focus {
      box-shadow: inset 2px 2px 4px #b8b9be, inset -2px -2px 4px #ffffff;
    }

    button {
      width: 100%;
      margin-top: 24px;
      padding: 12px;
      border: none;
      border-radius: 12px;
      background: linear-gradient(135deg, #4a90e2, #357ABD);
      color: white;
      font-weight: bold;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.3s ease;
      box-shadow: 4px 4px 8px #b8b9be, -4px -4px 8px #ffffff;
    }

    button:hover {
      background: linear-gradient(135deg, #5aa0f3, #2f6ca0);
    }

    .msg {
      text-align: center;
      color: green;
      margin-bottom: 12px;
      font-weight: 600;
    }

    .err {
      background: #ffe8e8;
      color: #c00;
      border-radius: 10px;
      padding: 10px;
      margin-bottom: 15px;
      font-size: 14px;
    }

    .note {
      color: #777;
      font-size: 12px;
      margin-top: 20px;
      text-align: center;
    }
  </style>
</head>

<body>
  <div class="container">
    <h2>✨ Create User (Secure)</h2>

    <?php if ($message): ?>
      <p class="msg"><?= $message ?></p>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
      <div class="err">
        <ul>
          <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post" action="">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
      <label>Username</label>
      <input type="text" name="username" required value="<?= isset($username) ? htmlspecialchars($username) : '' ?>">

      <label>Password</label>
      <input type="password" name="password" required>

      <label>Nama Lengkap</label>
      <input type="text" name="full_name" value="<?= isset($fullname) ? htmlspecialchars($fullname) : '' ?>">

      <button type="submit">Buat User Aman</button>
    </form>

    <p class="note">🔒 Form ini sudah dilengkapi validasi, CSRF token, dan password hashing.</p>
  </div>
</body>

</html>