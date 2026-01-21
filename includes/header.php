<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Watch Movies & TV Shows</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="assets/img/favicon.ico" type="image/x-icon">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        /* Header Styles */
        .site-header {
            background-color: #141414;
            padding: 10px 0;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 20;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo a {
            text-decoration: none;
        }

        .logo-text {
            font-size: 1.8rem;
            color: #e50914;
            font-weight: bold;
        }

        /* Hamburger Menu Styles */
        .hamburger-menu {
            position: relative;
        }

        .menu-toggle {
            display: none;
        }

        .hamburger {
            display: none; /* Hidden on desktop by default */
            cursor: pointer;
            padding: 10px;
        }

        .hamburger .bar {
            display: block;
            width: 25px;
            height: 3px;
            background-color: #fff;
            margin: 5px 0;
            transition: all 0.3s ease;
        }

        .main-nav {
            display: flex;
        }

        .nav-list {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            gap: 20px;
        }

        .nav-list li a {
            color: #fff;
            text-decoration: none;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: color 0.3s;
        }

        .nav-list li a:hover {
            color: #e50914;
        }

        /* Small Horizontal Menu Search Bar Styles */
        .menu-search-form {
            display: none; /* Hidden on desktop */
            margin-top: 15px;
            width: 100%; /* Matches menu width */
            max-width: 210px; /* Fits within 250px menu with padding */
        }

        .menu-search-form form {
            display: flex;
            align-items: center;
            background-color: #fff;
            border-radius: 20px;
            overflow: hidden;
        }

        .menu-search-form input {
            flex: 1;
            border: none;
            padding: 6px 10px;
            font-size: 0.9rem;
            outline: none;
            width: 100%;
        }

        .menu-search-form button {
            border: none;
            background-color: #e50914;
            color: #fff;
            padding: 6px 10px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .menu-search-form button:hover {
            background-color: #c10811;
        }

        .menu-search-form button i {
            font-size: 0.9rem;
        }

        /* Mobile Styles */
        @media (max-width: 768px) {
            .hamburger {
                display: block;
            }

            .main-nav {
                position: fixed;
                top: 60px; /* Below header */
                right: -100%; /* Hidden off-screen */
                width: 250px;
                height: 100vh;
                background-color: #141414;
                padding: 20px;
                transition: right 0.3s ease;
                z-index: 10;
            }

            .nav-list {
                flex-direction: column;
                gap: 15px;
            }

            .nav-list li a {
                font-size: 1.2rem;
            }

            .menu-search-form {
                display: block; /* Visible on mobile */
            }

            .menu-toggle:checked ~ .main-nav {
                right: 0; /* Slide in */
            }

            .menu-toggle:checked + .hamburger .bar:nth-child(1) {
                transform: rotate(45deg) translate(5px, 5px);
            }

            .menu-toggle:checked + .hamburger .bar:nth-child(2) {
                opacity: 0;
            }

            .menu-toggle:checked + .hamburger .bar:nth-child(3) {
                transform: rotate(-45deg) translate(6px, -6px);
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="site-header" id="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="index.php">
                        <span class="logo-text"><?php echo SITE_NAME; ?></span>
                    </a>
                </div>
                
                <!-- Hamburger Menu -->
                <div class="hamburger-menu">
                    <input type="checkbox" id="menu-toggle" class="menu-toggle">
                    <label for="menu-toggle" class="hamburger">
                        <span class="bar"></span>
                        <span class="bar"></span>
                        <span class="bar"></span>
                    </label>
                    
                    <!-- Navigation Menu with Small Horizontal Search Bar -->
                    <nav class="main-nav">
                        <ul class="nav-list">
                            <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
                            <li><a href="browse.php?type=movie"><i class="fas fa-film"></i> Movies</a></li>
                            <li><a href="browse.php?type=serie"><i class="fas fa-tv"></i> Series</a></li>
                        </ul>
                        <div class="menu-search-form">
                            <form action="search.php" method="GET">
                                <input type="text" name="q" placeholder="Search..." required>
                                <button type="submit"><i class="fas fa-search"></i></button>
                            </form>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Main Content -->
    <main class="main-content">