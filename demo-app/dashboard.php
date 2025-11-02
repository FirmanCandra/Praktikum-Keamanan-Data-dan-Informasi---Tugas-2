<?php
require 'config.php';
require_login(); // as original

// helper safe output
function e($s)
{
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$username = $_SESSION['username'] ?? '';
$displayName = $_SESSION['full_name'] ?? $username;
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Dashboard — Demo App</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

        :root {
            --bg: #eef5fb;
            --card: #ecf5fc;
            --text: #263245;
            --muted: #6b7280;
            --accent: #2563eb;
            --danger: #ef4444;
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

        .container {
            width: 100%;
            max-width: 960px;
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 20px;
            align-items: start;
        }

        .card {
            background: var(--card);
            border-radius: 16px;
            padding: 24px;
            box-shadow: 12px 12px 30px rgba(16, 24, 40, 0.06), -8px -8px 20px rgba(255, 255, 255, 0.6);
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }

        header h1 {
            margin: 0;
            font-size: 20px;
        }

        .header-right {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .avatar {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            background: linear-gradient(135deg, #dbe9ff, #f0f6ff);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 18px;
            color: #1e3a8a;
            box-shadow: inset 4px 4px 8px rgba(255, 255, 255, 0.9), 6px 10px 20px rgba(37, 99, 235, 0.06);
        }

        .nav-links {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .nav-links a {
            display: inline-block;
            padding: 10px 12px;
            border-radius: 12px;
            text-decoration: none;
            color: var(--text);
            font-weight: 700;
            background: transparent;
            border: 1px solid rgba(16, 24, 40, 0.04);
        }

        .nav-links a.primary {
            background: linear-gradient(135deg, #4a90e2, #357abd);
            color: #fff;
            box-shadow: 6px 10px 20px rgba(53, 122, 189, 0.12);
        }

        .content h2 {
            margin-top: 0
        }

        .muted {
            color: var(--muted)
        }

        .side .card {
            padding: 20px;
        }

        .quick {
            display: flex;
            gap: 10px;
            margin-top: 12px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 10px 12px;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            font-weight: 700;
            font-size: 14px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #4a90e2, #357abd);
            color: #fff;
            box-shadow: 6px 10px 20px rgba(53, 122, 189, 0.12);
        }

        .btn-ghost {
            background: transparent;
            border: 1px solid rgba(16, 24, 40, 0.06);
            color: var(--muted);
        }

        .links-list {
            margin: 12px 0 0 0;
            padding: 0;
            list-style: none;
        }

        .links-list li {
            margin-bottom: 8px;
        }

        .links-list a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
        }

        footer {
            margin-top: 18px;
            color: var(--muted);
            font-size: 13px;
            text-align: center;
        }

        @media(max-width:900px) {
            .container {
                grid-template-columns: 1fr;
            }

            .header-right {
                flex-direction: column;
                align-items: flex-end;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <main class="card content" role="main" aria-labelledby="title">
            <header>
                <div>
                    <h1 id="title">Selamat datang, <?= e($displayName ?: 'Pengguna') ?>!</h1>
                    <div class="muted">Anda masuk sebagai <strong><?= e($username ?: '-') ?></strong></div>
                </div>

                <div class="header-right" aria-hidden="false">
                    <div class="avatar" title="<?= e($displayName) ?>"><?= e(strtoupper(substr($username ?: $displayName ?: 'U', 0, 2))) ?></div>
                    <nav class="nav-links" aria-label="Menu utama">
                        <a href="artikel_vul.php" class="">📝 Artikel (Versi RENTAN)</a>
                        <a href="artikel_safe.php" class="">✅ Artikel (Versi AMAN)</a>
                        <form action="logout.php" method="post" style="display:inline">
                            <button type="submit" class="btn" style="background:linear-gradient(135deg,var(--danger),#dc2626);color:#fff;border-radius:12px;padding:10px 12px;font-weight:700;border:none;cursor:pointer">Logout</button>
                        </form>
                    </nav>
                </div>
            </header>

            <section>
                <h2>Menu Utama</h2>
                <p class="muted">Gunakan navigasi di kanan atas untuk mengakses modul yang tersedia.</p>

                <div class="quick" role="region" aria-label="Tindakan cepat">
                    <button class="btn btn-primary" onclick="location.href='create_user_safe_form.php'">Buat User (Safe)</button>
                    <button class="btn btn-ghost" onclick="location.href='create_user_vul_form.php'">Buat User (Vul)</button>
                    <button class="btn btn-ghost" onclick="location.href='login_safe.php'">Login (Safe)</button>
                </div>

                <article style="margin-top:18px">
                    <h3>Ringkasan</h3>
                    <p class="muted">Ini adalah dashboard sederhana setelah login. Halaman ini hanya contoh untuk praktik keamanan web (demo).</p>
                </article>
            </section>

            <footer>
                Demo App — praktik keamanan web. Jangan gunakan data demo di lingkungan produksi.
            </footer>
        </main>

        <aside class="card side" aria-labelledby="sideTitle">
            <h3 id="sideTitle">Link Cepat</h3>
            <ul class="links-list">
                <li><a href="artikel_vul.php">📝 Artikel (Versi RENTAN)</a></li>
                <li><a href="artikel_safe.php">✅ Artikel (Versi AMAN)</a></li>
                <li><a href="create_user_safe_form.php">Daftar (Safe)</a></li>
                <li><a href="create_user_vul_form.php">Daftar (Vul)</a></li>
            </ul>

            <div style="margin-top:14px">
                <strong>Status sesi</strong>
                <p class="muted">Sesi Anda aktif. Untuk mengakhiri sesi, gunakan tombol Logout di atas.</p>
            </div>
        </aside>
    </div>
</body>

</html>