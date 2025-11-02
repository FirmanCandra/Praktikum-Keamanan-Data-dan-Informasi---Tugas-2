<?php
require 'config.php';

$feedback = ''; // hanya untuk menampilkan pesan (tetap menggunakan logika backend asli)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->execute([$username, $password]);
        $feedback = "<p class='ok'>Registrasi berhasil! <a href='index.php'>Login</a></p>";
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $feedback = "<p class='err'>Username sudah digunakan!</p>";
        } else {
            $feedback = "<p class='err'>Terjadi kesalahan.</p>";
        }
    }
}
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Daftar — Demo App</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        /* Modern neumorphism style — consistent with previous pages */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

        :root {
            --bg: #eef5fb;
            --card: #ecf5fc;
            --text: #263245;
            --muted: #6b7280;
            --accent: #2563eb;
            --ok: #065f46;
            --err: #9f1239;
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
            max-width: 520px;
            background: var(--card);
            border-radius: 16px;
            padding: 34px;
            box-shadow: 12px 12px 30px rgba(16, 24, 40, 0.06), -8px -8px 20px rgba(255, 255, 255, 0.6);
        }

        .brand {
            text-align: center;
            margin-bottom: 18px;
        }

        .brand h1 {
            margin: 0;
            font-size: 22px;
        }

        .brand p {
            margin: 6px 0 0;
            color: var(--muted);
            font-size: 13px;
        }

        .feedback {
            margin-bottom: 14px;
        }

        .feedback .ok {
            background: #f0fdf4;
            color: var(--ok);
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px solid rgba(6, 95, 70, 0.06);
            font-weight: 600;
        }

        .feedback .err {
            background: #fff5f5;
            color: var(--err);
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px solid rgba(159, 18, 57, 0.06);
            font-weight: 600;
        }

        .feedback a {
            color: var(--accent);
            font-weight: 700;
            text-decoration: none;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        label {
            font-size: 13px;
            font-weight: 600;
            color: #2b3440;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px 14px;
            border-radius: 12px;
            border: none;
            background: #eaf4ff;
            box-shadow: inset 4px 4px 8px rgba(184, 185, 190, 0.25), inset -4px -4px 8px rgba(255, 255, 255, 0.9);
            font-size: 15px;
            outline: none;
            color: var(--text);
            transition: box-shadow .15s ease;
        }

        input:focus {
            box-shadow: inset 2px 2px 6px rgba(184, 185, 190, 0.18), 0 6px 18px rgba(37, 99, 235, 0.06);
        }

        .actions {
            display: flex;
            gap: 12px;
            margin-top: 8px;
            align-items: center;
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
            box-shadow: 6px 10px 20px rgba(53, 122, 189, 0.14);
        }

        .btn-ghost {
            background: transparent;
            border: 1px solid rgba(16, 24, 40, 0.06);
            color: var(--muted);
        }

        .hint {
            margin-top: 10px;
            text-align: center;
            color: var(--muted);
            font-size: 13px;
        }

        .small-links {
            margin-top: 14px;
            display: flex;
            justify-content: center;
            gap: 8px;
            font-size: 13px;
            color: var(--muted);
        }

        .small-links a {
            color: var(--accent);
            font-weight: 700;
            text-decoration: none;
        }

        @media (max-width:540px) {
            .card {
                padding: 22px;
            }
        }
    </style>
</head>

<body>
    <div class="card" role="main" aria-labelledby="registerTitle">
        <div class="brand">
            <h1 id="registerTitle">Daftar Akun Baru</h1>
            <p>Buat akun untuk masuk ke demo app</p>
        </div>

        <?php if ($feedback): ?>
            <div class="feedback"><?= $feedback ?></div>
        <?php endif; ?>

        <form method="post" action="" novalidate>
            <label for="username">Username</label>
            <input id="username" name="username" type="text" required autocomplete="username" placeholder="masukkan username">

            <label for="password">Password</label>
            <input id="password" name="password" type="password" required autocomplete="new-password" placeholder="masukkan password">

            <div class="actions">
                <button type="submit" class="btn btn-primary">Daftar</button>
                <button type="button" class="btn btn-ghost" onclick="location.href='index.php'">Kembali</button>
            </div>

            <div class="small-links">
                <span>Sudah punya akun?</span>
                <a href="index.php">Login</a>
            </div>

            <div class="hint">Catatan: tampilan saja yang diubah — logika backend tetap sama.</div>
        </form>
    </div>
</body>

</html>