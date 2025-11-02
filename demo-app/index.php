<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND password = ?");
    $stmt->execute([$username, $password]);
    $user = $stmt->fetch();

    if ($user) {
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $username;
        header('Location: dashboard.php');
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Login — Demo App</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

        :root {
            --bg: #eef5fb;
            --card: #ecf5fc;
            --text: #263245;
            --muted: #6b7280;
            --accent: #2563eb;
            --err: #b91c1c;
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
            max-width: 440px;
            background: var(--card);
            border-radius: 16px;
            padding: 32px;
            box-shadow: 10px 10px 30px rgba(16, 24, 40, 0.06), -8px -8px 20px rgba(255, 255, 255, 0.6);
        }

        h2 {
            text-align: center;
            margin-top: 0;
            margin-bottom: 16px;
            font-size: 22px;
        }

        .msg {
            margin-bottom: 12px;
            text-align: center;
            font-weight: 600;
            padding: 10px;
            border-radius: 10px;
        }

        .msg.error {
            color: var(--err);
            background: #fee2e2;
            border: 1px solid rgba(185, 28, 28, 0.08);
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        label {
            font-weight: 600;
            font-size: 14px;
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

        .btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 700;
            font-size: 15px;
            background: linear-gradient(135deg, #4a90e2, #357abd);
            color: white;
            box-shadow: 6px 10px 20px rgba(53, 122, 189, 0.14);
            margin-top: 6px;
        }

        .link {
            margin-top: 12px;
            text-align: center;
            font-size: 13px;
            color: var(--muted);
        }

        .link a {
            color: var(--accent);
            font-weight: 700;
            text-decoration: none;
        }

        @media(max-width:480px) {
            .card {
                padding: 24px;
            }
        }
    </style>
</head>

<body>
    <div class="card">
        <h2>Login ke Demo App</h2>

        <?php if (isset($error)): ?>
            <div class="msg error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post">
            <label for="username">Username</label>
            <input id="username" type="text" name="username" required placeholder="masukkan username">

            <label for="password">Password</label>
            <input id="password" type="password" name="password" required placeholder="masukkan password">

            <button type="submit" class="btn">Login</button>
        </form>

        <div class="link">
            Belum punya akun? <a href="register.php">Daftar</a>
        </div>
    </div>
</body>

</html>