<?php

require_once 'includes/config.php';
require_once 'includes/api.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

$seriesId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$series = getSeriesDetails($seriesId);
$seasons = getSeriesSeasons($seriesId);

if ($seriesId === 0 || !$series) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($series['title'] ?? 'Series Details'); ?> - Whisper Club</title>
    
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
            --transition: 0.4s cubic-bezier(0.25, 1, 0.5, 1);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            font-family: var(--font-body);
            background-color: var(--color-dark);
            color: var(--color-light);
            overflow-x: hidden;
        }
        a { text-decoration: none; color: inherit; }

        .main-header {
            padding: 25px 5%; display: flex; justify-content: space-between; align-items: center;
            background-color: var(--color-dark); box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        .header-logo { font-family: var(--font-heading); font-size: 1.8rem; color: var(--color-gold); }
        .main-nav { margin: 0 auto; }
        .main-nav a { margin: 0 15px; color: var(--color-secondary-text); }
        .header-actions { display: flex; align-items: center; }
        .search-icon { font-size: 1.2rem; cursor: pointer; color: var(--color-secondary-text); transition: color 0.3s; }
        .search-icon:hover { color: var(--color-light); }

        .search-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background-color: rgba(10, 10, 10, 0.95); backdrop-filter: blur(10px);
            z-index: 2000; display: flex; justify-content: center; align-items: center;
            opacity: 0; visibility: hidden; transition: opacity 0.4s ease, visibility 0.4s ease;
        }
        .search-overlay.visible { opacity: 1; visibility: visible; }
        .search-overlay-content { position: relative; width: 90%; max-width: 700px; }
        .close-search { position: absolute; top: -60px; right: 0; background: none; border: none; color: var(--color-light); font-size: 3rem; cursor: pointer; }
        .search-form { display: flex; width: 100%; border-bottom: 3px solid var(--color-secondary-text); }
        .search-form input { flex-grow: 1; background: transparent; border: none; color: var(--color-light); font-size: 2.5rem; padding: 15px 0; font-family: var(--font-heading); }
        .search-form input:focus { outline: none; }
        .search-form button { background: none; border: none; color: var(--color-light); font-size: 2rem; cursor: pointer; padding: 0 15px; }

        .detail-hero { position: relative; height: 60vh; background-size: cover; background-position: center 20%; }
        .hero-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(to top, var(--color-dark) 10%, transparent 50%); }

        .detail-content { padding: 0 5%; max-width: 1400px; margin: -200px auto 50px auto; position: relative; z-index: 10; }
        .detail-grid { display: grid; grid-template-columns: 300px 1fr; gap: 40px; align-items: flex-start; }
        .detail-poster img { width: 100%; border-radius: 12px; box-shadow: 0 15px 30px rgba(0,0,0,0.5); }
        .detail-info h1 { font-family: var(--font-heading); font-size: 3.5rem; line-height: 1.2; margin-bottom: 20px; }
        .detail-meta { display: flex; align-items: center; flex-wrap: wrap; gap: 25px; margin-bottom: 25px; color: var(--color-secondary-text); font-weight: 700; }
        .meta-item { display: flex; align-items: center; gap: 8px; }
        .meta-item .fa-star { color: var(--color-gold); }
        .detail-description { font-size: 1.1rem; line-height: 1.8; margin-bottom: 30px; max-width: 800px; }
        
        .seasons-section, .similar-section { padding: 50px 5%; max-width: 1400px; margin: auto; }
        .seasons-section > h2, .similar-section > h2 { font-family: var(--font-heading); font-size: 2.5rem; margin-bottom: 30px; }
        
        .seasons-tabs { border: 1px solid #2a2a2a; border-radius: 12px; overflow: hidden; }
        .tabs-header { display: flex; flex-wrap: wrap; background-color: #1a1a1a; padding: 10px; }
        .tab-button {
            padding: 10px 20px; border: none; background: none; color: var(--color-secondary-text);
            font-size: 1rem; font-weight: 600; cursor: pointer; transition: color 0.3s, background-color 0.3s; border-radius: 8px;
        }
        .tab-button.active, .tab-button:hover { background-color: var(--color-gold); color: var(--color-dark); }
        
        .tab-content { display: none; }
        .tab-content.active { display: block; }

        .episodes-list { padding: 30px; }
        .episode-item { display: flex; gap: 20px; padding: 20px; border-radius: 10px; margin-bottom: 15px; transition: background-color 0.3s; align-items: center; }
        .episode-item:hover { background-color: #1a1a1a; }
        .episode-thumbnail { width: 180px; flex-shrink: 0; aspect-ratio: 16/9; border-radius: 8px; overflow: hidden; background-color: #000; }
        .episode-thumbnail img { width: 100%; height: 100%; object-fit: cover; }
        .episode-info h3 { font-size: 1.2rem; margin-bottom: 8px; color: var(--color-light); }
        .episode-info p { font-size: 0.9rem; color: var(--color-secondary-text); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        
        .episode-actions { margin-left: auto; }
        .btn-episode {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 20px; border-radius: 50px;
            font-size: 1rem; font-weight: 700; text-decoration: none;
            color: var(--color-dark); background-color: var(--color-gold);
            border: 2px solid var(--color-gold); transition: var(--transition);
        }
        .btn-episode:hover { background-color: transparent; color: var(--color-gold); }

        .content-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 25px; }
        .content-card {
            border-radius: 12px; overflow: hidden; transition: var(--transition);
            position: relative; cursor: pointer; text-decoration: none;
        }
        .content-card:hover { transform: scale(1.05); box-shadow: 0 20px 40px rgba(0,0,0,0.5); }
        .card-image img { width: 100%; height: 100%; object-fit: cover; aspect-ratio: 2 / 3; transition: var(--transition); }
        .content-card:hover .card-image img { transform: scale(1.1); }
        .card-content {
            position: absolute; bottom: 0; left: 0; width: 100%;
            padding: 20px; background: linear-gradient(to top, rgba(0,0,0,0.9), transparent);
            opacity: 0; transition: var(--transition);
        }
        .content-card:hover .card-content { opacity: 1; }
        .card-content h3 { font-family: var(--font-heading); font-size: 1.5rem; color: var(--color-light); }
        
        .no-content { padding: 50px; text-align: center; color: var(--color-secondary-text); }
        
        @media (max-width: 992px) {
            .detail-grid { grid-template-columns: 1fr; text-align: center; }
            .detail-poster { width: 250px; margin: 0 auto 30px auto; }
            .detail-meta, .genres-list { justify-content: center; }
        }
        @media (max-width: 768px) {
            .episode-item { flex-direction: column; align-items: flex-start; }
            .episode-actions { margin-left: 0; margin-top: 15px; }
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

<div class="search-overlay" id="search-overlay">
    <div class="search-overlay-content">
        <button class="close-search" id="close-search">&times;</button>
        <form action="search.php" method="GET" class="search-form">
            <input type="text" name="q" placeholder="Search..." autofocus>
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>
    </div>
</div>

<main>
    <div class="detail-hero" style="background-image: url('<?php echo htmlspecialchars($series['cover'] ?? $series['image'] ?? ''); ?>')">
        <div class="hero-overlay"></div>
    </div>

    <section class="detail-content">
        <div class="detail-grid">
            <div class="detail-poster">
                <img src="<?php echo htmlspecialchars($series['image'] ?? ''); ?>" alt="<?php echo htmlspecialchars($series['title'] ?? ''); ?>">
            </div>
            <div class="detail-info">
                <h1><?php echo htmlspecialchars($series['title'] ?? 'Title not available'); ?></h1>
                <div class="detail-meta">
                    <?php if (isset($series['year'])): ?><div class="meta-item"><i class="fas fa-calendar"></i><span><?php echo htmlspecialchars($series['year']); ?></span></div><?php endif; ?>
                    <?php if (isset($series['duration'])): ?><div class="meta-item"><i class="fas fa-clock"></i><span><?php echo htmlspecialchars($series['duration']); ?></span></div><?php endif; ?>
                    <?php if (isset($series['imdb']) && $series['imdb'] > 0): ?><div class="meta-item"><i class="fas fa-star"></i><span>IMDb <?php echo htmlspecialchars($series['imdb']); ?></span></div><?php endif; ?>
                </div>
                <div class="detail-description"><p><?php echo htmlspecialchars($series['description'] ?? ''); ?></p></div>
                <?php if (isset($series['genres']) && !empty($series['genres'])): ?>
                    <div class="detail-genres">
                        <div class="genres-list">
                            <?php foreach ($series['genres'] as $genre): ?>
                            <span class="genre-tag"><?php echo htmlspecialchars($genre['title']); ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php if (!empty($seasons)): ?>
    <section class="seasons-section">
        <h2>Seasons & Episodes</h2>
        <div class="seasons-tabs">
            <div class="tabs-header">
                <?php foreach ($seasons as $index => $season): ?>
                <button class="tab-button <?php echo $index === 0 ? 'active' : ''; ?>" data-tab="season-<?php echo $season['id']; ?>">
                    <?php echo htmlspecialchars($season['title']); ?>
                </button>
                <?php endforeach; ?>
            </div>
            
            <div class="tabs-content">
                <?php foreach ($seasons as $index => $season): ?>
                <div id="season-<?php echo $season['id']; ?>" class="tab-content <?php echo $index === 0 ? 'active' : ''; ?>">
                    <div class="episodes-list">
                        <?php if (isset($season['episodes']) && !empty($season['episodes'])): ?>
                            <?php foreach ($season['episodes'] as $episode): ?>
                            <div class="episode-item">
                                <div class="episode-thumbnail">
                                    <img src="<?php echo htmlspecialchars($episode['image'] ?? ($series['image'] ?? '')); ?>" alt="<?php echo htmlspecialchars($episode['title']); ?>">
                                </div>
                                <div class="episode-info">
                                    <h3><?php echo htmlspecialchars($episode['title']); ?></h3>
                                    <?php if (isset($episode['description']) && !empty($episode['description'])): ?>
                                        <p><?php echo htmlspecialchars($episode['description']); ?></p>
                                    <?php endif; ?>
                                </div>
                                <div class="episode-actions">
                                    <a href="episode.php?id=<?php echo $episode['id']; ?>&episode=true&series_id=<?php echo $seriesId; ?>" class="btn-episode">
                                        <i class="fas fa-play"></i> Watch
                                    </a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="no-content"><p>No episodes available for this season.</p></div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>
    
    <?php 
    $similarSeries = [];
    if (isset($series['genres']) && !empty($series['genres'])) {
        $genreId = $series['genres'][0]['id'];
        $allSeries = getCategoryContent('created', 'serie', 20); 
        foreach ($allSeries as $item) {
            if ($item['id'] != $seriesId && isset($item['genres'])) {
                foreach ($item['genres'] as $genre) {
                    if ($genre['id'] == $genreId) {
                        $similarSeries[] = $item;
                        break;
                    }
                }
            }
            if (count($similarSeries) >= 6) {
                break;
            }
        }
    }
    
    if (!empty($similarSeries)):
    ?>
    <section class="similar-section">
        <h2>Similar Series</h2>
        <div class="content-grid">
            <?php foreach ($similarSeries as $item): ?>
                <a href="serie.php?id=<?php echo htmlspecialchars($item['id']); ?>" class="content-card">
                    <div class="card-image"><img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>"></div>
                    <div class="card-content"><h3><?php echo htmlspecialchars($item['title']); ?></h3></div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const tabId = this.getAttribute('data-tab');
                tabContents.forEach(content => content.classList.remove('active'));
                tabButtons.forEach(btn => btn.classList.remove('active'));
                document.getElementById(tabId).classList.add('active');
                this.classList.add('active');
            });
        });
    });

    const searchIcon = document.getElementById('search-icon');
    const searchOverlay = document.getElementById('search-overlay');
    const closeSearch = document.getElementById('close-search');

    if (searchIcon && searchOverlay && closeSearch) {
        searchIcon.addEventListener('click', () => {
            searchOverlay.classList.add('visible');
            searchOverlay.querySelector('input').focus();
        });
        closeSearch.addEventListener('click', () => {
            searchOverlay.classList.remove('visible');
        });
    }
</script>

</body>
</html>