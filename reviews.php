<?php 
session_start();
require_once "includes/auth_check.php";
checkAuth();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Reviews - Assemble Gaming Zone</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/reviews.css">
    <!-- Choices.js CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
</head>
<body>
    <?php include 'includes/header.php'; ?>
    

    <main class="reviews-container">
        <h1>Game Reviews</h1>
        
        <div class="filters-section">
            <form method="GET" action="reviews.php" class="search-bar">
    <input type="text" id="searchInput" name="q" placeholder="Search games..." value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
    <button type="submit" id="searchButton"><i class="fas fa-search"></i></button>
</form>
            
            <div class="filter-options">
                <div class="sort-by">
    <label>Sort By:</label>
    <select id="sortSelect">
        <option value="most-recent">Most Recent</option>
        <option value="least-recent">Least Recent</option>
        <option value="score">Score</option>
        <option value="name">Name</option>
    </select>
</div>
                
                <div class="platform-filter">
                    <label>Platform:</label>
                    <div class="platform-buttons">
                        <button class="platform-btn active" data-platform="all">All</button>
                        <button class="platform-btn" data-platform="pc">PC</button>
                        <button class="platform-btn" data-platform="xbox">Xbox</button>
                        <button class="platform-btn" data-platform="Mobile">Mobile</button>
                        <button class="platform-btn" data-platform="PS">PS</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="reviews-grid" id="reviewsGrid">
            <?php
            // PHP fallback: show all reviews if JS is disabled
            require_once 'includes/db.php';
            $search = isset($_GET['q']) ? trim($_GET['q']) : '';
            if ($search !== '') {
                $stmt = $pdo->prepare("SELECT id, game_name, main_image, release_date, platforms, score FROM game_reviews_full WHERE game_name LIKE ? ORDER BY created_at DESC");
                $stmt->execute(['%' . $search . '%']);
                $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $reviews = $pdo->query('SELECT id, game_name, main_image, release_date, platforms, score FROM game_reviews_full ORDER BY created_at DESC')->fetchAll(PDO::FETCH_ASSOC);
            }
            foreach ($reviews as $review) {
                $platforms = htmlspecialchars($review['platforms']);
                $date = date('F j, Y', strtotime($review['release_date']));
                $img = htmlspecialchars($review['main_image']);
                $title = htmlspecialchars($review['game_name']);
                $score = htmlspecialchars($review['score']);
                echo <<<HTML
                <div class="review-card" data-platforms="{$platforms}" data-score="{$score}" data-date="{$review['release_date']}" data-title="{$title}">
                    <div class="review-image">
                        <img src="{$img}" alt="{$title}">
                        <div class="review-score">{$score}</div>
                    </div>
                    <div class="review-info">
                        <h3>{$title}</h3>
                        <div class="review-meta">
                            <span class="platforms">{$platforms}</span>
                            <span class="date">{$date}</span>
                        </div>
                        <a href="review-detail.php?id={$review['id']}" class="read-more">Read Full Review</a>
                    </div>
                </div>
                HTML;
            }
            ?>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const reviewsGrid = document.getElementById('reviewsGrid');
            let lastQuery = '';
            let debounceTimeout;

            searchInput.addEventListener('keyup', function() {
                const query = searchInput.value.trim();
                if (query === lastQuery) return;
                lastQuery = query;
                clearTimeout(debounceTimeout);
                debounceTimeout = setTimeout(() => {
                    fetch('search_reviews.php?q=' + encodeURIComponent(query))
                        .then(response => response.json())
                        .then(data => {
                            reviewsGrid.innerHTML = '';
                            if (data.reviews.length === 0) {
                                reviewsGrid.innerHTML = '<p style="color:white;text-align:center;margin-top:2em">No games found.</p>';
                                return;
                            }
                            data.reviews.forEach(review => {
                                const date = new Date(review.release_date);
                                const formattedDate = date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
                                reviewsGrid.innerHTML += `
                <div class="review-card" data-platforms="${review.platforms}" data-score="${review.score}" data-date="${review.release_date}" data-title="${review.game_name}">
                    <div class="review-image">
                        <img src="${review.main_image}" alt="${review.game_name}">
                        <div class="review-score">${review.score}</div>
                    </div>
                    <div class="review-info">
                        <h3>${review.game_name}</h3>
                        <div class="review-meta">
                            <span class="platforms">${review.platforms}</span>
                            <span class="date">${formattedDate}</span>
                        </div>
                        <a href="review-detail.php?id=${review.id}" class="read-more">Read Full Review</a>
                    </div>
                </div>
                                `;
                            });
                        });
                }, 200); // debounce for 200ms
            });
        });
        </script>
    </main>

    <?php include 'includes/footer.php'; ?>
    <!-- Choices.js JS -->
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
      const sortSelect = document.getElementById('sortSelect');
      if (sortSelect) {
        new Choices(sortSelect, {
          searchEnabled: false,
          itemSelectText: '',
          shouldSort: false,
          classNames: {
            containerOuter: 'choices custom-purple-dropdown'
          }
        });
      }
    });
    </script>
    <script src="js/script.js"></script>
    <script src="js/reviews.js"></script>
</body>
</html>
