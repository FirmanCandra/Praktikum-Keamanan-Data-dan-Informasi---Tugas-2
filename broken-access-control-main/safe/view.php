<?php
// safe/view.php
// Layout-only upgrade for SAFE view page — logic tetap sama (CSRF, ownership, token check)
require_once __DIR__ . '/../config.php';
if (empty($_SESSION['user'])) header('Location: ../login.php');

$uuid = $_GET['u'] ?? '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uuid = $_POST['u'] ?? '';
    $token = $_POST['token'] ?? '';
} else {
    $token = $_GET['t'] ?? '';
}

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

// Helper
function e($s)
{
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

if (!$token):
?>
    <!doctype html>
    <html lang="id">

    <head>
        <meta charset="utf-8">
        <title>Verifikasi Token — SAFE View</title>
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

            :root {
                --bg: #eef5fb;
                --card: #ecf5fc;
                --text: #263245;
                --muted: #6b7280;
                --accent: #2563eb;
                --danger: #b91c1c;
            }

            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                font-family: 'Poppins', system-ui, -apple-system, "Segoe UI", Roboto, Arial;
                background: linear-gradient(180deg, #f8fbff 0%, var(--bg) 100%);
                color: var(--text);
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
                padding: 20px;
            }

            .card {
                background: var(--card);
                border-radius: 16px;
                padding: 30px;
                width: 100%;
                max-width: 600px;
                box-shadow: 8px 12px 26px rgba(16, 24, 40, 0.06);
            }

            h1 {
                margin-top: 0;
                font-size: 22px;
                color: var(--accent);
            }

            p {
                color: var(--muted);
            }

            input {
                width: 100%;
                padding: 12px 14px;
                border-radius: 12px;
                border: none;
                background: #eaf4ff;
                font-size: 15px;
                outline: none;
                margin-top: 8px;
            }

            button {
                margin-top: 18px;
                padding: 12px 18px;
                border: none;
                border-radius: 12px;
                background: linear-gradient(135deg, #10b981, #059669);
                color: #fff;
                font-weight: 700;
                cursor: pointer;
            }

            a {
                color: var(--muted);
                text-decoration: none;
                font-size: 14px;
            }
        </style>
    </head>

    <body>
        <div class="card">
            <h1>Masukkan Access Token</h1>
            <p>Untuk melihat item dengan UUID:</p>
            <p><strong><?= e($uuid) ?></strong></p>
            <form method="post">
                <input type="hidden" name="u" value="<?= e($uuid) ?>">
                <label for="token">Access Token:</label>
                <input id="token" name="token" placeholder="Tempelkan token di sini">
                <button type="submit">Lihat Item</button>
            </form>
            <p style="margin-top:20px;"><a href="list.php">← Kembali ke List</a></p>
        </div>
    </body>

    </html>
<?php
    exit;
endif;

// Token provided → verify
$provided_hash = token_hash($token);
if (!hash_equals($item['token_hash'], $provided_hash)) {
    http_response_code(403);
?>
    <!doctype html>
    <html lang="id">

    <head>
        <meta charset="utf-8">
        <title>Token Salah</title>
        <style>
            body {
                font-family: Poppins, Arial;
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
                background: #fef2f2;
                color: #b91c1c;
            }

            .msg {
                background: #fee2e2;
                padding: 24px 30px;
                border-radius: 12px;
                box-shadow: 6px 8px 16px rgba(0, 0, 0, 0.05);
            }

            a {
                color: #334155;
                text-decoration: none;
            }
        </style>
    </head>

    <body>
        <div class="msg">
            <h2>❌ Invalid Token</h2>
            <p>Token yang Anda masukkan tidak valid atau sudah tidak berlaku.</p>
            <p><a href="view.php?u=<?= e($uuid) ?>">Coba lagi</a></p>
        </div>
    </body>

    </html>
<?php
    exit;
}

// Token valid — tampilkan konten
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title><?= e($item['title']) ?> — SAFE View</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

        :root {
            --bg: #eef5fb;
            --card: #ecf5fc;
            --text: #263245;
            --muted: #6b7280;
            --accent: #2563eb;
        }

        body {
            margin: 0;
            font-family: 'Poppins', system-ui, -apple-system, "Segoe UI", Roboto, Arial;
            background: linear-gradient(180deg, #f8fbff 0%, var(--bg) 100%);
            color: var(--text);
            padding: 24px;
            min-height: 100vh;
            display: flex;
            justify-content: center;
        }

        .card {
            background: var(--card);
            border-radius: 16px;
            padding: 30px;
            max-width: 780px;
            box-shadow: 12px 12px 30px rgba(16, 24, 40, 0.06);
        }

        h1 {
            margin-top: 0;
            color: var(--accent);
        }

        p {
            white-space: pre-line;
            font-size: 15px;
            line-height: 1.6;
        }

        .uuid {
            margin-top: 18px;
            color: var(--muted);
            font-size: 13px;
        }

        a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 700;
        }
    </style>
</head>

<body>
    <div class="card">
        <h1><?= e($item['title']) ?></h1>
        <p><?= nl2br(e($item['content'])) ?></p>
        <p class="uuid"><strong>UUID:</strong> <?= e($item['uuid']) ?></p>
        <p><a href="list.php">← Kembali ke List</a></p>
    </div>
</body>

</html>