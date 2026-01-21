<?php
require_once 'includes/config.php';
require_once 'includes/api.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

$movieId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$movie = getMovieDetails($movieId);

if (!$movieId || !$movie) {
    header('Location: index.php');
    exit;
}

// جلب روابط المشاهدة
$sourceApi = "https://dwapp.arabypros.com/api/movie/source/by/{$movieId}/4F5A9C3D9A86FA54EACEDDD635185/d506abfd-9fe2-4b71-b979-feff21bcad13/";
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $sourceApi,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_HTTPHEADER => ["User-Agent: okhttp/4.12.0"]
]);
$sourceResponse = curl_exec($ch);
curl_close($ch);

$sources = [];
if ($sourceResponse && preg_match('/(W3.*)/', $sourceResponse, $matches)) {
    $clean = $matches[1];
    $pad = strlen($clean) % 4;
    if ($pad !== 0) $clean .= str_repeat('=', 4 - $pad);
    $json = json_decode(base64_decode($clean), true);
    if (is_array($json)) {
        foreach ($json as $src) {
            if (isset($src['url']) && strpos($src['url'], 'embed') !== false) {
                $sources[] = $src;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($movie['title'] ?? 'Movie Details'); ?> - Whisper Club</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<style>
:root {
    --color-gold: #FFD700;
    --color-dark: #0a0a0a;
    --color-light: #ffffff;
    --color-secondary-text: #a9a9a9;
    --card-bg: #1a1a1a;
    --font-heading: 'Playfair Display', serif;
    --font-body: 'Lato', sans-serif;
    --transition: 0.4s cubic-bezier(0.25,1,0.5,1);
}
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:var(--font-body);background-color:var(--color-dark);color:var(--color-light);}
a{text-decoration:none;color:inherit;}
.main-header{padding:25px 5%;display:flex;justify-content:space-between;align-items:center;background-color:var(--color-dark);box-shadow:0 5px 15px rgba(0,0,0,0.3);}
.header-logo{font-family:var(--font-heading);font-size:1.8rem;color:var(--color-gold);}
.main-nav a{margin:0 15px;color:var(--color-secondary-text);}
.search-icon{font-size:1.2rem;cursor:pointer;color:var(--color-secondary-text);}

.detail-hero{position:relative;height:70vh;background-size:cover;background-position:center;margin-bottom:50px;}
.hero-overlay{position:absolute;top:0;left:0;width:100%;height:100%;background:linear-gradient(to top,var(--color-dark) 20%,transparent 80%);}

.detail-card{position:relative;max-width:1200px;margin:-200px auto 0;display:flex;gap:40px;padding:30px;background-color:var(--card-bg);border-radius:12px;box-shadow:0 15px 30px rgba(0,0,0,0.6);}
.detail-poster img{width:250px;border-radius:12px;box-shadow:0 10px 20px rgba(0,0,0,0.5);}
.detail-info h1{font-family:var(--font-heading);font-size:2.5rem;margin-bottom:20px;}
.detail-meta{display:flex;gap:25px;margin-bottom:25px;color:var(--color-secondary-text);font-weight:700;}
.meta-item{display:flex;align-items:center;gap:8px;}
.meta-item .fa-star{color:var(--color-gold);}
.detail-description{font-size:1.1rem;line-height:1.8;margin-bottom:30px;}
.genres-list{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:30px;}
.genre-tag{background-color:#222;padding:5px 15px;border-radius:50px;font-size:0.9rem;color:var(--color-secondary-text);}

.player-section{margin:60px auto;max-width:1000px;text-align:center;}
.player-buttons{display:flex;flex-wrap:wrap;gap:10px;margin-bottom:20px;justify-content:center;}
.player-buttons button{padding:10px 25px;border-radius:50px;border:2px solid var(--color-gold);background-color:var(--color-gold);color:var(--color-dark);cursor:pointer;transition:var(--transition);font-weight:700;}
.player-buttons button:hover{background-color:transparent;color:var(--color-gold);}
.player-section iframe{width:100%;height:600px;border:none;border-radius:12px;box-shadow:0 15px 30px rgba(0,0,0,0.5);}

@media(max-width:992px){
  .detail-card{flex-direction:column;align-items:center;margin:-150px auto 0;}
  .detail-poster img{width:200px;}
}
@media(max-width:768px){
  .player-section iframe{height:400px;}
}
</style>
</head>
<body>

<header class="main-header">
<a href="index.php" class="header-logo">Whisper Club</a>
<nav class="main-nav">
<a href="index.php">Home</a>
<a href="#">Movies</a>
<a href="#">Series</a>
</nav>
<div class="header-actions">
<i class="fas fa-search search-icon" id="search-icon"></i>
</div>
</header>

<main>
<div class="detail-hero" style="background-image:url('<?php echo htmlspecialchars($movie['cover'] ?? $movie['image'] ?? ''); ?>')">
  <div class="hero-overlay"></div>
</div>

<div class="detail-card">
  <div class="detail-poster">
    <img src="<?php echo htmlspecialchars($movie['image'] ?? ''); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>">
  </div>
  <div class="detail-info">
    <h1><?php echo htmlspecialchars($movie['title']); ?></h1>
    <div class="detail-meta">
      <?php if(isset($movie['year'])): ?><div class="meta-item"><i class="fas fa-calendar"></i> <?php echo htmlspecialchars($movie['year']); ?></div><?php endif; ?>
      <?php if(isset($movie['duration'])): ?><div class="meta-item"><i class="fas fa-clock"></i> <?php echo htmlspecialchars($movie['duration']); ?></div><?php endif; ?>
      <?php if(isset($movie['imdb']) && $movie['imdb']>0): ?><div class="meta-item"><i class="fas fa-star"></i> IMDb <?php echo htmlspecialchars($movie['imdb']); ?></div><?php endif; ?>
    </div>
    <div class="detail-description"><p><?php echo htmlspecialchars($movie['description'] ?? ''); ?></p></div>

    <?php if(!empty($movie['genres'])): ?>
    <div class="genres-list">
      <?php foreach($movie['genres'] as $genre): ?>
      <span class="genre-tag"><?php echo htmlspecialchars($genre['title']); ?></span>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</div>

<?php if(!empty($sources)): ?>
<section class="player-section">
  <div class="player-buttons">
    <?php foreach($sources as $i=>$src): ?>
    <button onclick="document.getElementById('player').src='<?php echo $src['url']; ?>'">
      <?php echo htmlspecialchars($src['title'] ?? 'Server '.($i+1)); ?>
    </button>
    <?php endforeach; ?>
  </div>
  <iframe id="player" src="<?php echo $sources[0]['url']; ?>" allowfullscreen></iframe>
</section>
<?php endif; ?>

</main>

</body>
</html>