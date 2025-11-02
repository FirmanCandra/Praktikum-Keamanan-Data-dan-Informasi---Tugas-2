<?php
// safe/create.php
// Layout-only update for SAFE create page. Backend logic (CSRF, uuid, token generation, DB insert) is unchanged.
require_once __DIR__ . '/../config.php';
if (empty($_SESSION['user'])) header('Location: ../login.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!check_csrf($_POST['csrf'] ?? '')) {
        http_response_code(400);
        exit('CSRF fail');
    }
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    if ($title === '') {
        $err = "Title required";
    }
    if (empty($err)) {
        $uuid = uuid4();
        $token = token_generate();
        $hash = token_hash($token);
        $stmt = $pdo->prepare("INSERT INTO items_safe (uuid, token_hash, token_expires_at, user_id, title, content)
                               VALUES (:uuid, :th, NULL, :uid, :t, :c)");
        $stmt->execute([
            ':uuid' => $uuid,
            ':th' => $hash,
            ':uid' => $_SESSION['user']['id'],
            ':t' => $title,
            ':c' => $content
        ]);
        // Show token only once (preserve behavior) — render with nicer layout
        $created_uuid = $uuid;
        $created_token = $token;
        // render below (avoid echo + exit to allow consistent layout)
    }
}

// safe helper
function e($s)
{
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Create SAFE Item</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

        :root {
            --bg: #eef5fb;
            --card: #ecf5fc;
            --text: #263245;
            --muted: #6b7280;
            --accent: #2563eb;
            --success: #16a34a;
            --field: #eaf4ff;
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

        .wrap {
            width: 100%;
            max-width: 780px;
        }

        .card {
            background: var(--card);
            border-radius: 16px;
            padding: 28px;
            box-shadow: 12px 12px 30px rgba(16, 24, 40, 0.06), -8px -8px 20px rgba(255, 255, 255, 0.6);
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
        }

        h1 {
            margin: 0;
            font-size: 20px;
        }

        .sub {
            color: var(--muted);
            font-size: 13px;
        }

        .msg-err {
            margin-bottom: 14px;
            background: #fff5f5;
            color: #9f1239;
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px solid rgba(159, 18, 57, 0.06);
            font-weight: 600;
        }

        .msg-ok {
            margin-bottom: 14px;
            background: #f0fdf4;
            color: #065f46;
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px solid rgba(6, 95, 70, 0.06);
            font-weight: 700;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        label {
            font-weight: 700;
            font-size: 13px;
            color: #2b3440;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 12px 14px;
            border-radius: 12px;
            border: none;
            background: var(--field);
            box-shadow: inset 4px 4px 8px rgba(184, 185, 190, 0.25), inset -4px -4px 8px rgba(255, 255, 255, 0.9);
            font-size: 15px;
            outline: none;
            color: var(--text);
        }

        textarea {
            min-height: 140px;
            resize: vertical;
        }

        input:focus,
        textarea:focus {
            box-shadow: inset 2px 2px 6px rgba(184, 185, 190, 0.18), 0 6px 18px rgba(37, 99, 235, 0.06);
        }

        .actions {
            display: flex;
            gap: 12px;
            margin-top: 6px;
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
            background: linear-gradient(135deg, #10b981, #059669);
            color: #fff;
            box-shadow: 6px 10px 20px rgba(6, 95, 70, 0.12);
        }

        .btn-ghost {
            background: transparent;
            border: 1px solid rgba(16, 24, 40, 0.06);
            color: var(--muted);
        }

        .token-box {
            background: #fff;
            border-radius: 12px;
            padding: 16px;
            border: 1px dashed rgba(16, 24, 40, 0.06);
            margin-top: 12px;
            font-family: monospace;
            color: #052e1f;
        }

        .small {
            margin-top: 12px;
            color: var(--muted);
            font-size: 13px;
        }

        @media(max-width:600px) {
            .card {
                padding: 20px;
            }

            .actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="wrap">
        <div class="card" role="main" aria-labelledby="pageTitle">
            <header>
                <div>
                    <h1 id="pageTitle">Create SAFE Item</h1>
                    <div class="sub">UUID + one-time access token will be generated. Save the token once — it is shown only now.</div>
                </div>
                <div>
                    <a href="list.php" class="btn btn-ghost" style="text-decoration:none;padding:10px 12px;border-radius:10px;">← Back to list</a>
                </div>
            </header>

            <?php if (!empty($err)): ?>
                <div class="msg-err"><?= e($err) ?></div>
            <?php endif; ?>

            <?php if (!empty($created_uuid) && !empty($created_token)): ?>
                <div class="msg-ok">Item berhasil dibuat. SIMPAN token akses di tempat yang aman — token hanya ditampilkan sekali.</div>

                <div>
                    <label>UUID</label>
                    <div class="token-box"><?= e($created_uuid) ?></div>
                </div>

                <div>
                    <label>ACCESS TOKEN (save this now)</label>
                    <div class="token-box"><?= e($created_token) ?></div>
                </div>

                <div class="small">
                    <strong>Perhatian:</strong> Token hanya ditampilkan sekali. Jika hilang, buat item baru atau implementasikan mekanisme reset token.
                </div>

                <div style="margin-top:16px;">
                    <a href="list.php" class="btn btn-ghost" style="text-decoration:none;padding:10px 12px;border-radius:10px;">Kembali ke List</a>
                </div>

            <?php else: ?>
                <form method="post" novalidate>
                    <div>
                        <label for="title">Title</label>
                        <input id="title" name="title" type="text" placeholder="Masukkan judul" value="<?= e($_POST['title'] ?? '') ?>">
                    </div>

                    <div>
                        <label for="content">Content</label>
                        <textarea id="content" name="content" placeholder="Masukkan konten"><?= e($_POST['content'] ?? '') ?></textarea>
                    </div>

                    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

                    <div class="actions">
                        <button type="submit" class="btn btn-primary">Create</button>
                        <a href="list.php" class="btn btn-ghost" style="text-decoration:none;padding:12px 14px;border-radius:12px;">Cancel</a>
                    </div>

                    <div class="small">Judul wajib diisi. UUID & token akan dibuat otomatis untuk mencegah IDOR — token hanya ditampilkan sekali.</div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>