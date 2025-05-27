<?php
session_start();
if (!isset($_SESSION['email']) || !in_array($_SESSION['email'], ['syedmhasan229@gmail.com', 'mr@gmail.com'])) {
    header('Location: login.php');
    exit();
}
require_once 'includes/db.php';

// Edit review: fetch data for edit mode
$edit_review = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM game_reviews_full WHERE id = ?');
    $stmt->execute([$_GET['edit']]);
    $edit_review = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Add review
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_review'])) {
    // Handle file upload for main image
    $main_image = '';
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
        $img_name = uniqid('game_', true) . '.' . pathinfo($_FILES['main_image']['name'], PATHINFO_EXTENSION);
        $img_path = 'uploads/reviews/' . $img_name;
        if (!is_dir('uploads/reviews')) mkdir('uploads/reviews', 0777, true);
        move_uploaded_file($_FILES['main_image']['tmp_name'], $img_path);
        $main_image = $img_path;
    } elseif (isset($_POST['main_image']) && !empty($_POST['main_image'])) {
        $main_image = $_POST['main_image'];
    } else if (isset($edit_review['main_image'])) {
        $main_image = $edit_review['main_image'];
    }
    // Handle gallery images
    $gallery = [];
    if (!empty($_FILES['gallery_images']['name'][0])) {
        foreach ($_FILES['gallery_images']['tmp_name'] as $idx => $tmp_name) {
            if ($_FILES['gallery_images']['error'][$idx] === UPLOAD_ERR_OK) {
                $g_name = uniqid('gallery_', true) . '.' . pathinfo($_FILES['gallery_images']['name'][$idx], PATHINFO_EXTENSION);
                $g_path = 'uploads/reviews/' . $g_name;
                move_uploaded_file($tmp_name, $g_path);
                $gallery[] = $g_path;
            }
        }
    }
    $game_name = trim($_POST['game_name']);
    $release_date = $_POST['release_date'];
    $developer = trim($_POST['developer']);
    $publisher = trim($_POST['publisher']);
    $genre = trim($_POST['genre']);
    $platforms = trim($_POST['platforms']);
    $language = trim($_POST['language']);
    $score = floatval($_POST['score']);
    $review = trim($_POST['review']);
    $trailer_url = trim($_POST['trailer_url']);
    $requirements_min = trim($_POST['requirements_min']);
    $requirements_rec = trim($_POST['requirements_rec']);
    $buy_links = [
        'steam' => [
            'url' => trim($_POST['buy_steam']),
        ],
        'epic' => [
            'url' => trim($_POST['buy_epic']),
        ],
        'Aamazon' => [
            'url' => trim($_POST['buy_amazon']),
        ]
    ];
    $gallery_json = json_encode($gallery);
    $buy_links_json = json_encode($buy_links);
    $stmt = $pdo->prepare('INSERT INTO game_reviews_full (game_name, main_image, release_date, developer, publisher, genre, platforms, language, score, review, trailer_url, gallery, requirements_min, requirements_rec, buy_links) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$game_name, $main_image, $release_date, $developer, $publisher, $genre, $platforms, $language, $score, $review, $trailer_url, $gallery_json, $requirements_min, $requirements_rec, $buy_links_json]);
    $msg = 'Review added!';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_review'])) {
    // Handle file upload for main image
    $main_image = '';
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
        $img_name = uniqid('game_', true) . '.' . pathinfo($_FILES['main_image']['name'], PATHINFO_EXTENSION);
        $img_path = 'uploads/reviews/' . $img_name;
        if (!is_dir('uploads/reviews')) mkdir('uploads/reviews', 0777, true);
        move_uploaded_file($_FILES['main_image']['tmp_name'], $img_path);
        $main_image = $img_path;
    } elseif (isset($_POST['main_image']) && !empty($_POST['main_image'])) {
        $main_image = $_POST['main_image'];
    } else if (isset($edit_review['main_image'])) {
        $main_image = $edit_review['main_image'];
    }
    // Handle gallery images
    $gallery = (isset($_POST['gallery']) && $_POST['gallery'] !== '') ? json_decode($_POST['gallery'], true) : [];
    if (!empty($_FILES['gallery_images']['name'][0])) {
        foreach ($_FILES['gallery_images']['tmp_name'] as $idx => $tmp_name) {
            if ($_FILES['gallery_images']['error'][$idx] === UPLOAD_ERR_OK) {
                $g_name = uniqid('gallery_', true) . '.' . pathinfo($_FILES['gallery_images']['name'][$idx], PATHINFO_EXTENSION);
                $g_path = 'uploads/reviews/' . $g_name;
                move_uploaded_file($tmp_name, $g_path);
                $gallery[] = $g_path;
            }
        }
    }
    if (isset($_POST['existing_gallery'])) {
        $existing_gallery = explode(',', $_POST['existing_gallery']);
        $gallery = array_merge($existing_gallery, $gallery);
    }
    $game_name = trim($_POST['game_name']);
    $release_date = $_POST['release_date'];
    $developer = trim($_POST['developer']);
    $publisher = trim($_POST['publisher']);
    $genre = trim($_POST['genre']);
    $platforms = trim($_POST['platforms']);
    $language = trim($_POST['language']);
    $score = floatval($_POST['score']);
    $review = trim($_POST['review']);
    $trailer_url = trim($_POST['trailer_url']);
    $requirements_min = trim($_POST['requirements_min']);
    $requirements_rec = trim($_POST['requirements_rec']);
    $buy_links = [
        'steam' => [
            'url' => trim($_POST['buy_steam']),
        ],
        'epic' => [
            'url' => trim($_POST['buy_epic']),
        ],
        'amazon' => [
            'url' => trim($_POST['buy_amazon']),
        ],
        'g2a' => [
            'url' => trim($_POST['buy_g2a']),
        ],
        'Play Store' => [
            'url' => trim($_POST['buy_play_store']),
        ],
        'xbox' => [
            'url' => trim($_POST['buy_xbox']),
        ]
    ];
    $gallery_json = json_encode($gallery);
    $buy_links_json = json_encode($buy_links);
    $stmt = $pdo->prepare('UPDATE game_reviews_full SET game_name = ?, main_image = ?, release_date = ?, developer = ?, publisher = ?, genre = ?, platforms = ?, language = ?, score = ?, review = ?, trailer_url = ?, gallery = ?, requirements_min = ?, requirements_rec = ?, buy_links = ? WHERE id = ?');
    $stmt->execute([$game_name, $main_image, $release_date, $developer, $publisher, $genre, $platforms, $language, $score, $review, $trailer_url, $gallery_json, $requirements_min, $requirements_rec, $buy_links_json, $_POST['edit_id']]);
    $msg = 'Review updated!';
}
// Remove review
if (isset($_GET['remove'])) {
    $stmt = $pdo->prepare('DELETE FROM game_reviews_full WHERE id = ?');
    $stmt->execute([$_GET['remove']]);
    $msg = 'Review removed!';
}
// Fetch all reviews
$reviews = $pdo->query('SELECT * FROM game_reviews_full ORDER BY created_at DESC')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Game Reviews</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 40px;
            background: #181a2a;
            color: #e0e0ff;
        }
        h1 {
            color: #7c3aed;
            margin-bottom: 24px;
            font-size: 2rem;
            text-align: center;
        }
        .main-admin-content {
            max-width: 1200px;
            margin: 0 auto 36px auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            min-height: 100vh;
        }
        .admin-review-form-card {
            background: #23244d;
            border-radius: 18px;
            box-shadow: 0 8px 40px 0 #7c3aed33;
            padding: 36px 48px 28px 48px;
            margin-bottom: 36px;
            min-width: 420px;
            width: 100%;
            max-width: 500px;
            border: 2px solid #7c3aed;
        }
        .admin-review-form-card h2 {
            text-align: center;
            color: #a594f9;
            margin-bottom: 24px;
            font-size: 2rem;
            font-weight: bold;
        }
        .admin-review-form {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }
        .admin-review-form label {
            font-weight: 500;
            color: #a594f9;
            margin-bottom: 6px;
        }
        .admin-review-form input[type="text"],
        .admin-review-form input[type="date"],
        .admin-review-form input[type="number"],
        .admin-review-form textarea {
            background: #23244d;
            border: 1.5px solid #7c3aed;
            color: #e0e0ff;
            border-radius: 7px;
            padding: 10px 12px;
            font-size: 1rem;
            margin-bottom: 2px;
            box-shadow: 0 2px 8px #7c3aed11;
        }
        .admin-review-form input[type="file"] {
            background: transparent;
            color: #a594f9;
            border: none;
            padding: 8px 0;
        }
        .admin-review-form input[type="text"]:focus,
        .admin-review-form input[type="date"]:focus,
        .admin-review-form input[type="number"]:focus,
        .admin-review-form textarea:focus {
            outline: none;
            border: 1.5px solid #a594f9;
            box-shadow: 0 0 0 2px #a594f955;
        }
        .admin-review-form textarea {
            min-height: 60px;
            resize: vertical;
        }
        .admin-review-form .form-row {
            display: flex;
            gap: 16px;
        }
        .admin-review-form .form-row > * {
            flex: 1;
        }
        .admin-review-form .form-group {
            display: flex;
            flex-direction: column;
        }
        .admin-review-form button[type="submit"] {
            background: linear-gradient(90deg, #7c3aed 60%, #a594f9 100%);
            color: #fff;
            border: none;
            border-radius: 7px;
            padding: 10px 28px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 2px 8px #7c3aed33;
            transition: background 0.2s;
            margin-top: 10px;
        }
        .admin-review-form button[type="submit"]:hover {
            background: linear-gradient(90deg, #a594f9 20%, #7c3aed 100%);
        }
        @media (max-width: 600px) {
            .admin-review-form-card, .admin-form {
                min-width: unset;
                padding: 18px 8px;
                max-width: 98vw;
            }
            .main-admin-content {
                padding: 0 2vw;
            }
            table {
                min-width: unset;
            }
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            background: #23244d;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 12px #7c3aed22;
        }
        th, td {
            border: none;
            padding: 14px 12px;
            text-align: left;
        }
        th {
            background: #1a1a2e;
            color: #7c3aed;
            font-weight: 600;
        }
        tr:nth-child(even) {
            background: #23244d;
        }
        tr:nth-child(odd) {
            background: #181a2a;
        }
        a {
            color: #fff;
            background: linear-gradient(90deg, #7c3aed 60%, #a594f9 100%);
            font-weight: bold;
            text-decoration: none;
            padding: 6px 14px;
            border-radius: 6px;
            transition: background 0.2s, color 0.2s;
        }
        a:hover {
            background: linear-gradient(90deg, #a594f9 20%, #7c3aed 100%);
            color: #fff;
        }
        .msg {
            color: #7cfa9c;
            font-weight: bold;
            margin-bottom: 18px;
            background: #23244d;
            border-radius: 8px;
            padding: 10px 18px;
            box-shadow: 0 2px 8px #7c3aed33;
            text-align: center;
        }
        img {
            max-height: 60px;
            border-radius: 6px;
            box-shadow: 0 2px 8px #7c3aed33;
        }
        .edit-btn {
            background: linear-gradient(90deg, #a594f9 60%, #7c3aed 100%);
            color: #23244d;
            font-weight: bold;
            text-decoration: none;
            padding: 6px 14px;
            border-radius: 6px;
            margin-right: 2px;
            transition: background 0.2s, color 0.2s;
            border: none;
            display: inline-block;
        }
        .edit-btn:hover {
            background: linear-gradient(90deg, #7c3aed 20%, #a594f9 100%);
            color: #fff;
        }
        .gallery-slider {
            margin-top: 12px;
            background: #181a2a;
            border-radius: 10px;
            padding: 18px 10px 10px 10px;
            box-shadow: 0 2px 12px #0003;
            max-width: 650px;
        }
        .gallery-main {
            position: relative;
            min-height: 260px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #11111a;
            border-radius: 8px;
            margin-bottom: 12px;
        }
        .gallery-main img.gallery-slide {
            max-width: 95%;
            max-height: 260px;
            border-radius: 8px;
            display: block;
            margin: 0 auto;
            box-shadow: 0 2px 10px #0004;
        }
        .gallery-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: #7c3aed;
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            font-size: 1.5rem;
            cursor: pointer;
            opacity: 0.8;
            z-index: 2;
        }
        .gallery-arrow.left { left: 10px; }
        .gallery-arrow.right { right: 10px; }
        .gallery-thumbs {
            display: flex;
            gap: 8px;
            justify-content: center;
            margin-top: 8px;
        }
        .gallery-thumb {
            width: 60px;
            height: 40px;
            object-fit: cover;
            border-radius: 5px;
            box-shadow: 0 1px 6px #0003;
            cursor: pointer;
            opacity: 0.5;
            transition: opacity 0.2s, box-shadow 0.2s;
            border: 2px solid transparent;
        }
        .gallery-thumb:hover, .gallery-thumb.active {
            opacity: 1;
            box-shadow: 0 2px 10px #7c3aed44;
            border: 2px solid #a594f9;
        }
        .delete-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .delete-modal-content {
            background: #23244d;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 12px #0003;
        }
        .delete-modal-title {
            color: #fff;
            font-size: 1.2rem;
            margin-bottom: 20px;
        }
        .delete-modal-buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
        }
        .delete-modal-button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
        }
        .delete-modal-button.cancel {
            background: #7c3aed;
            color: #fff;
        }
        .delete-modal-button.delete {
            background: #e74c3c;
            color: #fff;
        }
    </style>
    <link rel="stylesheet" href="css/gallery.css">
</head>
<body>
    <h1>Admin - Game Reviews</h1>
    <?php if (!empty($msg)) echo '<p class="msg">' . htmlspecialchars($msg) . '</p>'; ?>
    <div class="main-admin-content">
        <div class="admin-review-form-card">
            <h2><?= isset($edit_review) ? 'Edit Game Review' : 'Add Game Review' ?></h2>
            <form class="admin-review-form" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="game_name">Game Name</label>
                    <input type="text" id="game_name" name="game_name" required value="<?= htmlspecialchars($edit_review['game_name'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="main_image">Main Image</label>
                    <input type="file" id="main_image" name="main_image" accept="image/*">
                    <?php if (!empty($edit_review['main_image'])): ?>
                        <div style="margin-top:6px;"><img src="<?= htmlspecialchars($edit_review['main_image']) ?>" alt="Current Image" style="max-width:80px;max-height:60px;border-radius:6px;"></div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="release_date">Release Date</label>
                    <input type="date" id="release_date" name="release_date" required value="<?= htmlspecialchars($edit_review['release_date'] ?? '') ?>">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="developer">Developer</label>
                        <input type="text" id="developer" name="developer" value="<?= htmlspecialchars($edit_review['developer'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="publisher">Publisher</label>
                        <input type="text" id="publisher" name="publisher" value="<?= htmlspecialchars($edit_review['publisher'] ?? '') ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="genre">Genre</label>
                        <input type="text" id="genre" name="genre" value="<?= htmlspecialchars($edit_review['genre'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="platforms">Platforms</label>
                        <input type="text" id="platforms" name="platforms" value="<?= htmlspecialchars($edit_review['platforms'] ?? '') ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="language">Language</label>
                        <input type="text" id="language" name="language" value="<?= htmlspecialchars($edit_review['language'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="score">Score</label>
                        <input type="number" id="score" name="score" step="0.1" min="0" max="10" value="<?= htmlspecialchars($edit_review['score'] ?? '') ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="review">Review</label>
                    <textarea id="review" name="review" rows="6" required><?= htmlspecialchars($edit_review['review'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label for="trailer_url">Game Trailer (YouTube URL)</label>
                    <input type="text" id="trailer_url" name="trailer_url" value="<?= htmlspecialchars($edit_review['trailer_url'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="gallery_images">Photo Gallery (multiple images)</label>
                    <input type="file" id="gallery_images" name="gallery_images[]" accept="image/*" multiple>
                    <div class="gallery-preview">
                        <?php if (!empty($edit_review['gallery'])): ?>
                            <?php $gallery_imgs = json_decode($edit_review['gallery'], true); if ($gallery_imgs): ?>
                                <?php foreach ($gallery_imgs as $index => $img): ?>
                                    <div class="gallery-image-container">
                                        <img src="<?php echo htmlspecialchars($img); ?>" class="gallery-image" />
                                        <div class="delete-button" onclick="deleteImage(<?php echo $index; ?>)">×</div>
                                    </div>
                                <?php endforeach; ?>
                                <input type="hidden" name="existing_gallery" id="existing_gallery" value="<?php echo htmlspecialchars(implode(',', $gallery_imgs)); ?>">
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="requirements_min">System Requirements (Minimum)</label>
                        <textarea id="requirements_min" name="requirements_min" rows="2"><?= htmlspecialchars($edit_review['requirements_min'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="requirements_rec">System Requirements (Recommended)</label>
                        <textarea id="requirements_rec" name="requirements_rec" rows="2"><?= htmlspecialchars($edit_review['requirements_rec'] ?? '') ?></textarea>
                    </div>
                </div>
                <?php $buy_links = json_decode($edit_review['buy_links'] ?? '', true); ?>
                <div class="form-group">
                    <label for="buy_steam">Where to Buy - Steam</label>
                    <input type="text" id="buy_steam" name="buy_steam" placeholder="Steam link" value="<?= htmlspecialchars($buy_links['steam']['url'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="buy_epic">Where to Buy - Epic Games</label>
                    <input type="text" id="buy_epic" name="buy_epic" placeholder="Epic Games link" value="<?= htmlspecialchars($buy_links['epic']['url'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="buy_amazon">Where to Buy - Amazon</label>
                    <input type="text" id="buy_amazon" name="buy_amazon" placeholder="Amazon link" value="<?= htmlspecialchars($buy_links['amazon']['url'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="buy_g2a">Where to Buy - G2A</label>
                    <input type="text" id="buy_g2a" name="buy_g2a" placeholder="G2A link" value="<?= htmlspecialchars($buy_links['g2a']['url'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="buy_play_store">Where to Buy - Play Store</label>
                    <input type="text" id="buy_play_store" name="buy_play_store" placeholder="Play Store link" value="<?= htmlspecialchars($buy_links['play_store']['url'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="buy_xbox">Where to Buy - Xbox</label>
                    <input type="text" id="buy_xbox" name="buy_xbox" placeholder="Xbox link" value="<?= htmlspecialchars($buy_links['xbox']['url'] ?? '') ?>">
                </div>
                <button type="submit" name="<?= isset($edit_review) ? 'update_review' : 'add_review' ?>"><?= isset($edit_review) ? 'Update Review' : 'Add Review' ?></button>
                <?php if (isset($edit_review)): ?>
                    <input type="hidden" name="edit_id" value="<?= $edit_review['id'] ?>">
                <?php endif; ?>
            </form>
        </div>
        <h2>All Reviews</h2>
        <table>
            <tr><th>ID</th><th>Name</th><th>Release Date</th><th>Score</th><th>Actions</th></tr>
            <?php foreach ($reviews as $row): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['game_name']) ?></td>
                <td><?= htmlspecialchars($row['release_date']) ?></td>
                <td><?= htmlspecialchars($row['score']) ?></td>
                <td style="display:flex;gap:8px;">
                    <a href="?edit=<?= $row['id'] ?>" class="edit-btn">Edit</a>
                    <a href="?remove=<?= $row['id'] ?>" onclick="return confirm('Remove this review?')">Remove</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <div class="delete-modal" id="deleteModal">
        <div class="delete-modal-content">
            <div class="delete-modal-title">Are you sure you want to delete this image?</div>
            <div class="delete-modal-buttons">
                <button class="delete-modal-button cancel" onclick="closeDeleteModal()">Cancel</button>
                <button class="delete-modal-button delete" onclick="confirmDelete()">Delete</button>
            </div>
        </div>
    </div>

    <script>
    let currentImageIndex = null;

    function deleteImage(index) {
        currentImageIndex = index;
        document.getElementById('deleteModal').style.display = 'flex';
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').style.display = 'none';
    }

    function confirmDelete() {
        if (currentImageIndex !== null) {
            const containers = document.querySelectorAll('.gallery-image-container');
            containers[currentImageIndex].remove();
            
            // Update hidden input with remaining images
            const galleryInput = document.getElementById('existing_gallery');
            let images = galleryInput.value.split(',');
            images.splice(currentImageIndex, 1);
            galleryInput.value = images.join(',');
            
            closeDeleteModal();
        }
    }

    // Close modal when clicking outside
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });
    </script>
</body>
</html>
