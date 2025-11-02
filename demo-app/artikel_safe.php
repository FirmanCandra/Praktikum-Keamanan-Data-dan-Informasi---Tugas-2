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
        $file_size = $_FILES['file']['size'];

        // ✅ Validasi ekstensi
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed_ext)) {
            die("Ekstensi file tidak diizinkan!");
        }

        // ✅ Validasi ukuran (max 2MB)
        if ($file_size > 2 * 1024 * 1024) {
            die("File terlalu besar! Maksimal 2MB.");
        }

        // ✅ Validasi MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $tmp_file);
        finfo_close($finfo);

        $allowed_mimes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
        if (!in_array($mime, $allowed_mimes)) {
            die("Tipe file tidak valid!");
        }

        // ✅ Nama file acak
        $new_name = uniqid('upload_') . '.' . $ext;
        $target = $upload_dir . $new_name;

        if (move_uploaded_file($tmp_file, $target)) {
            $file_path = $target;
        } else {
            die("Gagal menyimpan file.");
        }
    }

    $stmt = $pdo->prepare("INSERT INTO articles (user_id, title, content, file_path) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $title, $content, $file_path]);

    $message = "Artikel berhasil disimpan dengan aman!";
}
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Artikel — Versi AMAN</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

        :root {
            --bg: #eef5fb;
            --card: #ecf5fc;
            --text: #263245;
            --muted: #6b7280;
            --accent: #2563eb;
            --success: #065f46;
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
            max-width: 700px;
            background: var(--card);
            border-radius: 16px;
            padding: 32px;
            box-shadow: 12px 12px 30px rgba(16, 24, 40, 0.06), -8px -8px 20px rgba(255, 255, 255, 0.6);
        }

        h2 {
            text-align: center;
            margin-top: 0;
            margin-bottom: 18px;
            font-size: 22px;
        }

        .msg {
            margin-bottom: 16px;
            background: #f0fdf4;
            color: var(--success);
            border: 1px solid rgba(6, 95, 70, 0.06);
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
            background: #eaf4ff;
            box-shadow: inset 4px 4px 8px rgba(184, 185, 190, 0.25), inset -4px -4px 8px rgba(255, 255, 255, 0.9);
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
            box-shadow: inset 2px 2px 6px rgba(184, 185, 190, 0.18), 0 6px 18px rgba(37, 99, 235, 0.06);
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
            background: linear-gradient(135deg, #4a90e2, #357abd);
            color: white;
            box-shadow: 6px 10px 20px rgba(53, 122, 189, 0.14);
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

        .info {
            margin-top: 20px;
            text-align: center;
            color: var(--success);
            font-weight: 600;
            font-size: 14px;
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
        <h2>Tulis Artikel (Versi AMAN)</h2>

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

        <p class="info">✅ Versi ini memblokir file berbahaya dan hanya mengizinkan gambar/PDF.</p>
    </div>
</body>

</html>