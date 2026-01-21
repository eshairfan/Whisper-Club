<?php

// Your PHP logic remains exactly the same
require_once 'includes/config.php';
require_once 'includes/api.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

$type = isset($_GET['type']) ? $_GET['type'] : null;
$sort = isset($_GET['sort']) ? $_GET['sort'] : null;
$category = isset($_GET['category']) ? $_GET['category'] : null;

if ($category === 'مسلسلات رمضان 2025') {
    $content = getRamadanSeries();
    $pageTitle = 'Ramadan Series 2025'; // Translated for consistency
} elseif ($type && $sort) {
    $content = getCategoryContent($sort, $type);
    $pageTitle = ucfirst($sort) . ' ' . ucfirst($type) . 's';
} else {
    $content = [];
    $pageTitle = 'Browse';
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - Whisper Club</title>
    
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
            padding: 25px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: var(--color-dark);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        .header-logo { font-family: var(--font-heading); font-size: 1.8rem; color: var(--color-gold); text-decoration: none; }
        .main-nav a { margin: 0 15px; font-weight: 400; text-decoration: none; color: var(--color-secondary-text); transition: color 0.3s; }
        .main-nav a:hover { color: var(--color-light); }
        .header-actions { display: flex; align-items: center; }
        .search-icon { font-size: 1.2rem; cursor: pointer; }

        /* --- BROWSE PAGE STYLES --- */
        .page-header {
            padding: 120px 5% 40px;
            background-color: #111;
            text-align: center;
            border-bottom: 1px solid #222;
        }
        .page-header h1 {
            font-family: var(--font-heading);
            font-size: 3.5rem;
            color: var(--color-gold);
        }
        
        .page-content {
            padding: 50px 5%;
        }

        .content-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
        }
        
        /* Using the same card design as homepage for consistency */
        .content-card {
            border-radius: 12px;
            overflow: hidden;
            transition: var(--transition);
            position: relative;
            cursor: pointer;
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
        .card-meta { display: flex; align-items: center; gap: 15px; font-size: 0.9rem; color: var(--color-secondary-text); margin-top: 8px; }
        .card-meta .rating { color: var(--color-gold); font-weight: 700; }
        
        .no-content {
            padding: 80px 20px;
            text-align: center;
            color: var(--color-secondary-text);
            grid-column: 1 / -1; /* Make it span the full grid width */
        }
        .no-content i { font-size: 4rem; margin-bottom: 20px; opacity: 0.3; }
        .no-content h3 { font-family: var(--font-heading); font-size: 2rem; color: var(--color-light); }
        
        .main-footer { padding: 50px 5%; background-color: #000; border-top: 1px solid #222; margin-top: 50px; }

    </style>
</head>
<body>

<?php // include 'includes/header.php'; ?>
<header class="main-header">
    <a href="index.php" class="header-logo">Whisper Club</a>
    <nav class="main-nav">
        <a href="index.php">Home</a>
        <a href="#">Movies</a>
        <a href="#">Series</a>
    </nav>
</header>

<main>
    <section class="page-header">
        <h1><?php echo htmlspecialchars($pageTitle); ?></h1>
    </section>

    <section class="page-content">
        <div class="content-grid">
            <?php if (!empty($content)): ?>
                <?php foreach ($content as $item): ?>
                    <a href="<?php echo htmlspecialchars($item['type']); ?>.php?id=<?php echo htmlspecialchars($item['id']); ?>" class="content-card">
                        <div class="card-image"><img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>"></div>
                        <div class="card-content">
                            <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                            <div class="card-meta">
                                <span><?php echo htmlspecialchars($item['year']); ?></span>
                                <span class="rating"><i class="fas fa-star"></i> <?php echo htmlspecialchars($item['rating'] ?? 'N/A'); ?></span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-content">
                    <i class="fas fa-box-open"></i>
                    <h3>No Content Available</h3>
                    <p>There is currently no content in this category.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php // include 'includes/footer.php'; ?>

</body>
</html>