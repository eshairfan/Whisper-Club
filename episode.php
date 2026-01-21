<?php
/**
 * Whisper Club - Styled Movie Viewer with Embed Servers
 */

$episodeId = isset($_GET['id']) ? intval($_GET['id']) : 178545;

$apiUrl = "https://dwapp.arabypros.com/api/episode/source/by/{$episodeId}/4F5A9C3D9A86FA54EACEDDD635185/d506abfd-9fe2-4b71-b979-feff21bcad13/";

// Fetch API response
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $apiUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_HTTPHEADER => [
        "Host: dwapp.arabypros.com",
        "User-Agent: okhttp/4.12.0",
        "Connection: keep-alive"
    ]
]);
$response = curl_exec($ch);
curl_close($ch);

// Decode Base64 JSON inside response
$sources = [];
if ($response && preg_match('/(W3.*)/', $response, $matches)) {
    $clean = $matches[1];
    $pad = strlen($clean) % 4;
    if ($pad !== 0) $clean .= str_repeat('=', 4 - $pad);
    $decoded = base64_decode($clean, true);
    $json = json_decode($decoded, true);
    if (is_array($json)) $sources = $json;
}

// Filter embed links
$embedSources = [];
foreach ($sources as $src) {
    if (isset($src['url']) && strpos($src['url'], 'embed') !== false) {
        $embedSources[] = $src;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Whisper Club – Watch Movie</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Lato:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>
:root {
    --color-gold: #FFD700;
    --color-dark: #0a0a0a;
    --color-light: #ffffff;
    --color-secondary: #a9a9a9;
}
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:'Lato',sans-serif; background: var(--color-dark); color: var(--color-light); }

.header-bar {
    background: var(--color-dark);
    border-bottom: 1px solid #333;
    padding: 15px 5%;
    position: sticky;
    top: 0;
    z-index: 100;
}
.header-bar a {
    font-family:'Playfair Display', serif;
    font-size:1.8rem;
    color: var(--color-gold);
    text-decoration: none;
}

.container { max-width:1200px; margin:auto; padding:20px; }
.page-header { text-align:center; padding:50px 20px; }
.page-header h1 { font-family:'Playfair Display', serif; font-size:3rem; color: var(--color-gold); margin-bottom:10px; }
.page-header p { color: var(--color-secondary); font-size:1.1rem; }

.server-buttons { display:flex; flex-wrap:wrap; gap:15px; justify-content:center; margin:25px 0; }
.server-button {
    background:#2a2a2a; color:#fff; padding:12px 30px; border:none; border-radius:50px; cursor:pointer;
    transition:0.3s; font-weight:bold; font-size:1rem; box-shadow:0 5px 15px rgba(0,0,0,0.3);
}
.server-button.active { background: var(--color-gold); color:#000; }
.server-button:hover { background: var(--color-gold); color:#000; }

.video-player { width:100%; aspect-ratio:16/9; background:#000; border-radius:12px; overflow:hidden;
    box-shadow:0 20px 50px rgba(0,0,0,0.7); margin-bottom:40px; border: 2px solid var(--color-gold);
}
iframe { width:100%; height:100%; border:none; }

.empty { text-align:center; padding:100px 20px; color: var(--color-secondary); font-size:1.2rem; }

@media (max-width:768px) {
    .server-button { padding:10px 20px; font-size:0.9rem; }
    .page-header h1 { font-size:2.2rem; }
}
</style>
</head>
<body>

<div class="header-bar">
    <a href="index.php">Whisper Club</a>
</div>

<div class="container">
    <div class="page-header">
        <h1>Watch Movie</h1>
        <p>Select a server to start streaming the film instantly</p>
    </div>

    <?php if (!empty($embedSources)): ?>
        <div class="server-buttons">
            <?php foreach ($embedSources as $i => $s): ?>
                <button class="server-button <?php echo $i===0?'active':'';?>" onclick="changeServer('<?php echo htmlspecialchars($s['url']); ?>', this)">
                    <?php echo htmlspecialchars($s['title'] ?? 'Server ' . ($i+1)); ?>
                </button>
            <?php endforeach; ?>
        </div>

        <div class="video-player">
            <iframe id="player" src="<?php echo htmlspecialchars($embedSources[0]['url']); ?>" allowfullscreen></iframe>
        </div>
    <?php else: ?>
        <div class="empty">No streaming sources available.</div>
    <?php endif; ?>
</div>

<script>
function changeServer(url, btn) {
    document.getElementById('player').src = url;
    document.querySelectorAll('.server-button').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
}
</script>

</body>
</html>
