<?php

// Your PHP logic remains exactly the same
require_once 'includes/config.php';
require_once 'includes/api.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$results = [];
if (!empty($query)) {
    $results = searchContent($query);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results for "<?php echo htmlspecialchars($query); ?>"</title>
    
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

        /* --- HEADER (Consistent with homepage) --- */
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

        /* --- MAIN CONTENT & SEARCH --- */
        .page-content { padding: 40px 5%; }
        .search-form-container { margin-bottom: 50px; }
        .search-form {
            display: flex;
            max-width: 700px;
            margin: auto;
            background-color: #1a1a1a;
            border-radius: 50px;
            overflow: hidden;
            border: 1px solid #333;
        }
        .search-form input {
            flex-grow: 1; border: none; background: none; color: var(--color-light);
            padding: 15px 25px; font-size: 1.1rem; font-family: var(--font-body);
        }
        .search-form input:focus { outline: none; }
        .search-form button {
            border: none; background-color: var(--color-gold); color: var(--color-dark);
            padding: 0 30px; cursor: pointer; font-size: 1.2rem; transition: background-color 0.3s;
        }
        .search-form button:hover { background-color: #fff; }

        .search-results-header h2 {
            font-family: var(--font-heading);
            font-size: 2.5rem;
            margin-bottom: 40px;
            text-align: center;
            font-weight: 400;
        }
        .search-results-header .highlight {
            color: var(--color-gold);
            font-weight: 700;
        }

        /* --- RESULTS GRID (Using homepage card style) --- */
        .content-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
        }
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
        .card-content h3 { font-family: var(--font-heading); font-size: 1.5rem; color: var(--color-light); margin-bottom: 8px;}
        .card-meta { display: flex; align-items: center; gap: 15px; font-size: 0.9rem; color: var(--color-secondary-text); }
        .card-meta .rating { color: var(--color-gold); font-weight: 700; }
        
        /* --- NO RESULTS / PROMPT STYLES --- */
        .message-box {
            text-align: center;
            padding: 80px 20px;
            color: var(--color-secondary-text);
        }
        .message-box i { font-size: 4rem; margin-bottom: 20px; opacity: 0.3; }
        .message-box h3 { font-family: var(--font-heading); font-size: 2rem; color: var(--color-light); }
        
        /* --- FOOTER (Same as homepage) --- */
        .main-footer { padding: 50px 5%; background-color: #000; border-top: 1px solid #222; margin-top: 50px; }
        .footer-content { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 30px; max-width: 1200px; margin: 0 auto 40px auto; }
        .footer-column h4 { font-family: var(--font-heading); font-size: 1.2rem; margin-bottom: 20px; color: var(--color-light); }
        .footer-column a { display: block; color: var(--color-secondary-text); text-decoration: none; margin-bottom: 10px; transition: color 0.3s; }
        .footer-column a:hover { color: var(--color-light); }
        .footer-social { display: flex; gap: 20px; font-size: 1.5rem; }
        .footer-bottom { text-align: center; padding-top: 30px; border-top: 1px solid #222; color: var(--color-secondary-text); font-size: 0.9rem; }
    </style>
</head>
<body>

<?php // include 'includes/header.php'; // Your header include goes here ?>
<header class="main-header">
    <a href="index.php" class="header-logo">Whisper Club</a>
    <nav class="main-nav">
        <a href="index.php">Home</a>
        <a href="#">Movies</a>
        <a href="#">Series</a>
    </nav>
</header>

<main class="page-content">
    <div class="search-form-container">
        <form action="search.php" method="GET" class="search-form">
            <input type="text" name="q" placeholder="Search for movies and series..." value="<?php echo htmlspecialchars($query); ?>" required>
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>
    </div>

    <div class="search-results-header">
        <?php if (!empty($query)): ?>
            <h2>Results for: <span class="highlight"><?php echo htmlspecialchars($query); ?></span></h2>
        <?php endif; ?>
    </div>
    
    <div class="results-container">
        <?php if (!empty($query)): ?>
            <?php if (isset($results['posters']) && !empty($results['posters'])): ?>
                <div class="content-grid">
                    <?php foreach ($results['posters'] as $item): ?>
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
                </div>
            <?php else: ?>
                <div class="message-box no-results">
                    <i class="fas fa-video-slash"></i>
                    <h3>No results found</h3>
                    <p>We couldn't find any matches for "<?php echo htmlspecialchars($query); ?>"</p>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="message-box search-prompt">
                <i class="fas fa-search"></i>
                <h3>Search the library</h3>
                <p>Find your next favorite movie or series.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php // include 'includes/footer.php'; // Your footer include goes here ?>
<footer class="main-footer">
    <div class="footer-content">
        <div class="footer-column"><h4>Whisper Club</h4><a href="#">About Us</a></div>
        <div class="footer-column"><h4>Help</h4><a href="#">FAQ</a><a href="#">Contact Us</a></div>
        <div class="footer-column"><h4>Follow Us</h4><div class="footer-social"><a href="#"><i class="fab fa-facebook-f"></i></a><a href="#"><i class="fab fa-instagram"></i></a></div></div>
    </div>
    <div class="footer-bottom"><p>&copy; <?php echo date("Y"); ?> Whisper Club. All Rights Reserved.</p></div>
</footer>

</body>
</html>