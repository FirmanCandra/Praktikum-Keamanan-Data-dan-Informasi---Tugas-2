<?php
require 'config.php';
require_login();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];

    $file_path = null;
    if (!empty($_FILES['file']['name'])) {
        $upload_dir = 'uploads/';
        $file_name = $_FILES['file']['name'];
        $tmp_file = $_FILES['file']['tmp_name'];
        $target = $upload_dir . basename($file_name);

        // ❌ Tidak ada validasi — Rawan upload file berbahaya!
        if (move_uploaded_file($tmp_file, $target)) {
            $file_path = $target;
        }
    }

    $stmt = $pdo->prepare("INSERT INTO articles (user_id, title, content, file_path) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $title, $content, $file_path]);

    $message = "Artikel berhasil disimpan!";
}
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Artikel — Versi RENTAN</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

        :root {
            --bg: #fef2f2;
            --card: #fff1f2;
            --text: #3f3f46;
            --danger: #dc2626;
            --muted: #6b7280;
            --accent: #b91c1c;
        }

        * {
            box-sizing: border-box
        }

        body {
            margin: 0;
            font-family: 'Poppins', system-ui, -apple-system, "Segoe UI", Roboto, Arial;
            background: linear-gradient(180deg, #fff5f5 0%, var(--bg) 100%);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .card {
            width: 100%;
            max-width: 700px;
            background: var(--card);
            border-radius: 16px;
            padding: 32px;
            box-shadow: 12px 12px 30px rgba(220, 38, 38, 0.06), -8px -8px 20px rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(220, 38, 38, 0.1);
        }

        h2 {
            text-align: center;
            margin-top: 0;
            margin-bottom: 18px;
            font-size: 22px;
            color: var(--accent);
        }

        .msg {
            margin-bottom: 16px;
            background: #fee2e2;
            color: var(--accent);
            border: 1px solid rgba(220, 38, 38, 0.1);
            padding: 10px 12px;
            border-radius: 10px;
            font-weight: 600;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        label {
            font-weight: 600;
            font-size: 14px;
        }

        input[type="text"],
        textarea,
        input[type="file"] {
            width: 100%;
            padding: 12px 14px;
            border-radius: 12px;
            border: none;
            background: #fff;
            box-shadow: inset 4px 4px 8px rgba(185, 185, 185, 0.2), inset -4px -4px 8px rgba(255, 255, 255, 0.8);
            font-size: 15px;
            outline: none;
            color: var(--text);
        }

        textarea {
            min-height: 160px;
            resize: vertical;
        }

        input:focus,
        textarea:focus {
            box-shadow: inset 2px 2px 6px rgba(184, 185, 190, 0.18), 0 6px 18px rgba(220, 38, 38, 0.08);
        }

        .actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 12px;
        }

        .btn {
            padding: 12px 18px;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            font-weight: 700;
            font-size: 15px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            box-shadow: 6px 10px 20px rgba(220, 38, 38, 0.15);
        }

        .btn-link {
            background: transparent;
            border: 1px solid rgba(16, 24, 40, 0.06);
            color: var(--muted);
            text-decoration: none;
            padding: 12px 18px;
            border-radius: 12px;
            font-weight: 700;
            display: inline-block;
        }

        .warning {
            margin-top: 20px;
            text-align: center;
            color: var(--danger);
            font-weight: 700;
            font-size: 14px;
            background: #fee2e2;
            border-radius: 12px;
            padding: 10px 14px;
        }

        @media(max-width:600px) {
            .card {
                padding: 22px;
            }

            .actions {
                flex-direction: column;
            }

            .btn,
            .btn-link {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>

<body>
    <div class="card">
        <h2>Tulis Artikel (Versi RENTAN)</h2>

        <?php if ($message): ?>
            <div class="msg"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <label for="title">Judul</label>
            <input id="title" type="text" name="title" required placeholder="Masukkan judul artikel">

            <label for="content">Isi Artikel</label>
            <textarea id="content" name="content" required placeholder="Tulis isi artikel di sini..."></textarea>

            <label for="file">File (opsional)</label>
            <input id="file" type="file" name="file">

            <div class="actions">
                <button type="submit" class="btn btn-primary">Simpan Artikel</button>
                <a href="dashboard.php" class="btn-link">⬅ Kembali ke Dashboard</a>
            </div>
        </form>

        <p class="warning">⚠️ PERINGATAN: Versi ini memungkinkan upload file PHP berbahaya!</p>
    </div>
</body>

</html>