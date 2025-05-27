<?php
// tutorial-detail.php
require_once 'includes/db.php'; // Update to your DB connection file if different

if (!isset($_GET['id'])) {
    die('Tutorial ID is missing.');
}
$id = intval($_GET['id']);
$stmt = $pdo->prepare("SELECT * FROM tutorials WHERE id = ?");
$stmt->execute([$id]);
$tutorial = $stmt->fetch();
if (!$tutorial) {
    die('Tutorial not found.');
}

// Extract YouTube ID from URL
function getYoutubeId($url) {
    if (preg_match('/(?:youtube\\.com\\/watch\\?v=|youtu\\.be\\/)([A-Za-z0-9_-]+)/', $url, $matches)) {
        return $matches[1];
    }
    return '';
}
$youtube_id = getYoutubeId($tutorial['youtube_url']);

function difficultyClass($difficulty) {
    switch (strtolower($difficulty)) {
        case 'easy': return 'difficulty-easy';
        case 'medium': return 'difficulty-medium';
        case 'hard': return 'difficulty-hard';
        default: return 'difficulty-unknown';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($tutorial['title']); ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/tutorials.css">
    <style>
        .tutorial-container {
            max-width: 900px;
            width: 96vw;
            margin: 100px auto 40px auto;
            background: rgba(32, 21, 58, 0.98);
            padding: 36px 32px 32px 32px;
            border-radius: 18px;
            color: var(--white);
            box-shadow: 0 8px 32px rgba(16,0,43,0.18);
        }
        .tutorial-title {
            font-size: 2.1em;
            margin-bottom: 18px;
            color: var(--text-light);
            font-weight: 600;
            text-align: center;
        }
        .difficulty-bar {
            margin: 20px auto 30px auto;
            padding: 10px 22px;
            border-radius: 8px;
            font-size: 1.1em;
            font-weight: bold;
            display: block;
            width: fit-content;
            text-align: center;
        }
        .difficulty-easy { background: #2e7d32; color: #fff; }
        .difficulty-medium { background: #fbc02d; color: #222; }
        .difficulty-hard { background: #c62828; color: #fff; }
        .difficulty-unknown { background: #444; color: #fff; }
        .tutorial-video {
            margin: 0 auto 32px auto;
            text-align: center;
            display: flex;
            justify-content: center;
        }
        .tutorial-video iframe {
            width: 100%;
            max-width: 800px;
            height: 450px;
            border-radius: 12px;
        }
        .tutorial-content {
            margin-top: 18px;
            font-size: 1.13em;
            line-height: 1.7;
            color: var(--white);
        }
        @media (max-width: 900px) {
            .tutorial-container { padding: 16px 5vw; }
            .tutorial-video iframe { height: 56vw; }
        }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>
<div class="tutorial-container">
    <div class="tutorial-title"><?php echo htmlspecialchars($tutorial['title']); ?></div>
    <div class="difficulty-bar <?php echo difficultyClass($tutorial['difficulty']); ?>">
        Difficulty: <?php echo htmlspecialchars($tutorial['difficulty']); ?>
    </div>
    <?php 
    // Determine which video to show based on tutorial id
    $video_id = 'TNSdhzM6zd0'; // Default: Tomb Raider
    if (isset($tutorial['id']) && $tutorial['id'] == 2) {
        $video_id = 'R-AT2hlk7Pk'; // Prince of Persia Wazir Boss
    }
    ?>
    <div class="tutorial-video">
        <iframe width="900" height="506" src="https://www.youtube.com/embed/<?php echo $video_id; ?>" frameborder="0" allowfullscreen></iframe>
    </div>
    <div class="tutorial-content"><?php echo nl2br(htmlspecialchars($tutorial['content'])); ?></div>
</div>
<?php include 'includes/footer.php'; ?>
</body>
</html>
