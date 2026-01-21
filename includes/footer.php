</main>
    
    <!-- Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3 class="footer-title"><?php echo SITE_NAME; ?></h3>
                    <p>Watch your favorite movies and TV shows anytime, anywhere.</p>
                </div>
                
                <div class="footer-section">
                    <h3 class="footer-title">Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="browse.php?type=movie">Movies</a></li>
                        <li><a href="browse.php?type=serie">Series</a></li>
                        <li><a href="search.php">Search</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3 class="footer-title">Categories</h3>
                    <ul class="footer-links">
                        <li><a href="browse.php?sort=rating">Top Rated</a></li>
                        <li><a href="browse.php?sort=views">Most Popular</a></li>
                        <li><a href="browse.php?sort=created">Latest Additions</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>
    
    <!-- JavaScript -->
    <script src="assets/js/main.js"></script>
</body>
</html>

