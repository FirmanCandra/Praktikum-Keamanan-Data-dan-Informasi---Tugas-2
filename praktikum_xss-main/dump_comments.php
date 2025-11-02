<?php
// dump_comments.php — temporary lab helper (letakkan pada folder praktikum_xss-main)
// DO NOT leave this file in a public server. For lab use only.

require 'auth_simple.php';   // pakai koneksi dari file ini
$pdo = pdo_connect();

try {
    // jika tabel bernama lain, sesuaikan 'comments'
    $sql = "SELECT c.id, c.user_id, COALESCE(u.username,'Guest') AS username, c.comment, c.created_at
          FROM comments c
          LEFT JOIN users u ON c.user_id = u.id
          ORDER BY c.id DESC
          LIMIT 1000";
    $stmt = $pdo->query($sql);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("DB error: " . htmlspecialchars($e->getMessage()));
}

header('Content-Type: text/html; charset=utf-8');
echo "<!doctype html><html><head><meta charset='utf-8'><title>Dump comments</title>";
echo "<style>body{font-family:monospace;background:#f8fafc;padding:18px}pre{white-space:pre-wrap;background:#fff;padding:12px;border-radius:8px;box-shadow:0 6px 18px rgba(15,23,42,.03)}</style>";
echo "</head><body>";
echo "<h2>Dump comments (last " . count($rows) . ")</h2>";
foreach ($rows as $r) {
    echo "<pre>";
    echo "ID: " . htmlspecialchars($r['id']) . " | user: " . htmlspecialchars($r['username']) . " | at: " . htmlspecialchars($r['created_at']) . "\n";
    echo "---- COMMENT START ----\n";
    // tampilkan raw comment apa adanya agar payload terlihat
    echo $r['comment'] . "\n";
    echo "---- COMMENT END ------\n";
    echo "</pre><br/>\n";
}
echo "</body></html>";
