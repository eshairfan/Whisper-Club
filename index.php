<?php



require_once 'includes/config.php';

require_once 'includes/api.php';



ini_set('display_errors', 1);

error_reporting(E_ALL);



// Fetching all the data needed for the page

$latestSeries = getCategoryContent('created', 'serie', 20);

$popularMovies = getCategoryContent('views', 'movie', 20);

$topRated = getCategoryContent('rating', 'serie', 20);

$latestMovies = getCategoryContent('created', 'movie', 20); 



?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Whisper Club - A Cinematic Experience</title>

    

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

        .main-header {

            position: fixed;

            top: 0; left: 0; width: 100%;

            padding: 25px 5%;

            display: flex;

            justify-content: space-between;

            align-items: center;

            z-index: 1000;

            background-color: var(--color-dark);

            box-shadow: 0 5px 15px rgba(0,0,0,0.3);

        }

        .header-logo { font-family: var(--font-heading); font-size: 1.8rem; color: var(--color-gold); text-decoration: none; }

        .main-nav a { margin: 0 15px; font-weight: 400; text-decoration: none; color: var(--color-secondary-text); transition: color 0.3s; }

        .main-nav a:hover { color: var(--color-light); }

        

        /* --- VISIBLE SEARCH SECTION --- */

        .search-section {

            padding: 120px 5% 60px; /* Padding to push it below header */

            text-align: center;

        }

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

            flex-grow: 1;

            border: none;

            background: none;

            color: var(--color-light);

            padding: 15px 25px;

            font-size: 1.1rem;

            font-family: var(--font-body);

        }

        .search-form input:focus { outline: none; }

        .search-form button {

            border: none;

            background-color: var(--color-gold);

            color: var(--color-dark);

            padding: 0 30px;

            cursor: pointer;

            font-size: 1.2rem;

            transition: background-color 0.3s;

        }

        .search-form button:hover { background-color: #fff; }



        /* --- CONTENT SLIDERS --- */

        .content-section { padding: 0 0 50px; }

        .slider-header { margin: 0 5% 30px; display: flex; justify-content: space-between; align-items: center; }

        .slider-header h2 { font-family: var(--font-heading); font-size: 2.5rem; }

        .view-all-link { font-size: 1rem; font-weight: 700; color: var(--color-secondary-text); text-decoration: none; display: flex; align-items: center; gap: 8px; transition: color 0.3s ease; }

        .view-all-link i { transition: transform 0.3s ease; }

        .view-all-link:hover { color: var(--color-light); }

        .view-all-link:hover i { transform: translateX(5px); }

        

        .slider-wrapper { position: relative; }

        .slider-container { overflow-x: scroll; scroll-behavior: smooth; scrollbar-width: none; }

        .slider-container::-webkit-scrollbar { display: none; }

        .slider { display: flex; width: fit-content; padding: 20px 5%; }

        

        .content-card {

            flex: 0 0 auto;

            width: 280px;

            margin: 0 10px;

            border-radius: 12px;

            overflow: hidden;

            transition: var(--transition);

            position: relative;

            cursor: pointer;

            text-decoration: none;

        }

        .content-card:hover { transform: scale(1.05); box-shadow: 0 20px 40px rgba(0,0,0,0.5); }

        .card-image img { width: 100%; height: 100%; object-fit: cover; aspect-ratio: 2 / 3; transition: var(--transition); }

        .content-card:hover .card-image img { transform: scale(1.1); }

        .card-content { position: absolute; bottom: 0; left: 0; width: 100%; padding: 20px; background: linear-gradient(to top, rgba(0,0,0,0.9), transparent); opacity: 0; transition: var(--transition); }

        .content-card:hover .card-content { opacity: 1; }

        .card-content h3 { font-family: var(--font-heading); font-size: 1.5rem; color: var(--color-light); }

        .card-meta { display: flex; align-items: center; gap: 15px; font-size: 0.9rem; color: var(--color-secondary-text); margin-top: 8px; }

        .card-meta .rating { color: var(--color-gold); font-weight: 700; }

        

        .slider-arrow {

            position: absolute; top: 50%; transform: translateY(-50%);

            width: 60px; height: 100%;

            background: rgba(10, 10, 10, 0.5);

            backdrop-filter: blur(5px);

            color: var(--color-light); border: none;

            font-size: 2rem; cursor: pointer; z-index: 2;

            opacity: 0; transition: opacity 0.3s ease;

        }

        .slider-wrapper:hover .slider-arrow { opacity: 1; }

        .slider-arrow:hover { background-color: rgba(10, 10, 10, 0.8); }

        .slider-arrow.left { left: 0; }

        .slider-arrow.right { right: 0; }

        

        .no-content { padding: 50px; text-align: center; color: var(--color-secondary-text); }



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



<header class="main-header">

    <a href="#" class="header-logo">Whisper Club</a>

    <nav class="main-nav">

        <a href="#" class="active">Home</a>

        <a href="#series">Series</a>

        <a href="#movies">Movies</a>

    </nav>

</header>



<main>

    <section class="search-section">

        <form action="search.php" method="GET" class="search-form">

            <input type="text" name="q" placeholder="Search for movies and series...">

            <button type="submit"><i class="fas fa-search"></i></button>

        </form>

    </section>



    <section class="content-section">

        <div class="slider-wrapper" id="series">

            <div class="slider-header"><h2>Latest Series</h2><a href="browse.php?type=serie&sort=created" class="view-all-link">View All <i class="fas fa-chevron-right"></i></a></div>

            <div class="slider-container">

                <div class="slider">

                    <?php if (!empty($latestSeries)): foreach ($latestSeries as $item): ?>

                        <a href="<?php echo htmlspecialchars($item['type']); ?>.php?id=<?php echo htmlspecialchars($item['id']); ?>" class="content-card">

                            <div class="card-image"><img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>"></div>

                            <div class="card-content"><h3><?php echo htmlspecialchars($item['title']); ?></h3><div class="card-meta"><span><?php echo htmlspecialchars($item['year']); ?></span><span class="rating"><i class="fas fa-star"></i> <?php echo htmlspecialchars($item['rating'] ?? 'N/A'); ?></span></div></div>

                        </a>

                    <?php endforeach; else: ?><div class="no-content"><p>No latest series available.</p></div><?php endif; ?>

                </div>

            </div>

            <button class="slider-arrow left"><i class="fa-solid fa-chevron-left"></i></button>

            <button class="slider-arrow right"><i class="fa-solid fa-chevron-right"></i></button>

        </div>



        <div class="slider-wrapper" id="movies">

            <div class="slider-header"><h2>Popular Movies</h2><a href="browse.php?type=movie&sort=views" class="view-all-link">View All <i class="fas fa-chevron-right"></i></a></div>

            <div class="slider-container">

                <div class="slider">

                     <?php if (!empty($popularMovies)): foreach ($popularMovies as $item): ?>

                        <a href="<?php echo htmlspecialchars($item['type']); ?>.php?id=<?php echo htmlspecialchars($item['id']); ?>" class="content-card">

                            <div class="card-image"><img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>"></div>

                            <div class="card-content"><h3><?php echo htmlspecialchars($item['title']); ?></h3><div class="card-meta"><span><?php echo htmlspecialchars($item['year']); ?></span><span class="rating"><i class="fas fa-star"></i> <?php echo htmlspecialchars($item['rating'] ?? 'N/A'); ?></span></div></div>

                        </a>

                    <?php endforeach; else: ?><div class="no-content"><p>No popular movies available.</p></div><?php endif; ?>

                </div>

            </div>

            <button class="slider-arrow left"><i class="fa-solid fa-chevron-left"></i></button>

            <button class="slider-arrow right"><i class="fa-solid fa-chevron-right"></i></button>

        </div>



        <div class="slider-wrapper">

            <div class="slider-header"><h2>Latest Movies</h2><a href="browse.php?type=movie&sort=created" class="view-all-link">View All <i class="fas fa-chevron-right"></i></a></div>

            <div class="slider-container">

                <div class="slider">

                     <?php if (!empty($latestMovies)): foreach ($latestMovies as $item): ?>

                        <a href="<?php echo htmlspecialchars($item['type']); ?>.php?id=<?php echo htmlspecialchars($item['id']); ?>" class="content-card">

                            <div class="card-image"><img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>"></div>

                            <div class="card-content"><h3><?php echo htmlspecialchars($item['title']); ?></h3><div class="card-meta"><span><?php echo htmlspecialchars($item['year']); ?></span><span class="rating"><i class="fas fa-star"></i> <?php echo htmlspecialchars($item['rating'] ?? 'N/A'); ?></span></div></div>

                        </a>

                    <?php endforeach; else: ?><div class="no-content"><p>No latest movies available.</p></div><?php endif; ?>

                </div>

            </div>

            <button class="slider-arrow left"><i class="fa-solid fa-chevron-left"></i></button>

            <button class="slider-arrow right"><i class="fa-solid fa-chevron-right"></i></button>

        </div>

        

        <div class="slider-wrapper">

            <div class="slider-header"><h2>Top Rated</h2><a href="browse.php?type=serie&sort=rating" class="view-all-link">View All <i class="fas fa-chevron-right"></i></a></div>

            <div class="slider-container">

                <div class="slider">

                     <?php if (!empty($topRated)): foreach ($topRated as $item): ?>

                        <a href="<?php echo htmlspecialchars($item['type']); ?>.php?id=<?php echo htmlspecialchars($item['id']); ?>" class="content-card">

                            <div class="card-image"><img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>"></div>

                            <div class="card-content"><h3><?php echo htmlspecialchars($item['title']); ?></h3><div class="card-meta"><span><?php echo htmlspecialchars($item['year']); ?></span><span class="rating"><i class="fas fa-star"></i> <?php echo htmlspecialchars($item['rating'] ?? 'N/A'); ?></span></div></div>

                        </a>

                    <?php endforeach; else: ?><div class="no-content"><p>No top rated content available.</p></div><?php endif; ?>

                </div>

            </div>

            <button class="slider-arrow left"><i class="fa-solid fa-chevron-left"></i></button>

            <button class="slider-arrow right"><i class="fa-solid fa-chevron-right"></i></button>

        </div>



    </section>

</main>



<footer class="main-footer">

    <div class="footer-content">

        <div class="footer-column">

            <h4>Whisper Club</h4>

        </div>

        <div class="footer-column">

            <h4>Follow Us</h4>

            <div class="footer-social">

                <a href="https://facebook.com/WHISPER.DZA"><i class="fab fa-facebook-f"></i></a>

                <a href="https://t.me/WHI3PER"><i class="fab fa-telegram"></i></a>

                <a href="https://www.instagram.com/whisper_dev"><i class="fab fa-instagram"></i></a>

            </div>

        </div>

    </div>

    <div class="footer-bottom"><p>&copy; <?php echo date("Y"); ?> Whisper Club. All Rights Reserved.</p></div>

</footer>



<script>

    document.querySelectorAll('.slider-wrapper').forEach(wrapper => {

        const sliderContainer = wrapper.querySelector('.slider-container');

        const leftArrow = wrapper.querySelector('.slider-arrow.left');

        const rightArrow = wrapper.querySelector('.slider-arrow.right');



        if(sliderContainer && leftArrow && rightArrow) {

            leftArrow.addEventListener('click', () => { sliderContainer.scrollBy({ left: -sliderContainer.clientWidth * 0.8, behavior: 'smooth' }); });

            rightArrow.addEventListener('click', () => { sliderContainer.scrollBy({ left: sliderContainer.clientWidth * 0.8, behavior: 'smooth' }); });

        }

    });

</script>



</body>

</html>