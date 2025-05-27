<?php
session_start();
require_once 'includes/db.php';

// Get review ID from URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(404);
    echo 'Review not found.';
    exit;
}
$review_id = intval($_GET['id']);
$stmt = $pdo->prepare('SELECT * FROM game_reviews_full WHERE id = ?');
$stmt->execute([$review_id]);
$review = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$review) {
    http_response_code(404);
    echo 'Review not found.';
    exit;
}

// Parse gallery and buy_links fields
$gallery = json_decode($review['gallery'], true);
if (!is_array($gallery)) $gallery = [];
$buy_links = json_decode($review['buy_links'], true);
if (!is_array($buy_links)) $buy_links = [];

// Parse requirements as associative arrays if possible
$requirements_min = json_decode($review['requirements_min'], true);
if (!is_array($requirements_min)) $requirements_min = $review['requirements_min'];
$requirements_rec = json_decode($review['requirements_rec'], true);
if (!is_array($requirements_rec)) $requirements_rec = $review['requirements_rec'];

// Parse YouTube video ID from trailer_url if possible
function extract_youtube_id($url) {
    if (preg_match('~(?:youtu.be/|youtube(?:-nocookie)?\\.com/(?:embed/|v/|watch\\?v=|watch\\?.+&v=))([\w-]{11})~i', $url, $matches)) {
        return $matches[1];
    }
    return false;
}
$trailer_youtube_id = extract_youtube_id($review['trailer_url']);

// Handle comment submission (support parent_comment_id)
$comment_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    if (isset($_SESSION['user_name']) && trim($_SESSION['user_name']) !== '') {
        $user_name = $_SESSION['user_name'];
        $comment_text = trim($_POST['comment']);
        $parent_id = isset($_POST['parent_comment_id']) && $_POST['parent_comment_id'] !== '' ? intval($_POST['parent_comment_id']) : null;
        if ($comment_text !== '') {
            $stmt = $pdo->prepare('INSERT INTO game_review_comments (review_id, user_name, comment, parent_comment_id) VALUES (?, ?, ?, ?)');
            $stmt->execute([$review_id, $user_name, $comment_text, $parent_id]);
            $last_comment_id = $pdo->lastInsertId();
            // Redirect to anchor for smooth scroll
            if ($parent_id) {
                // For replies, just reload the page without anchor to prevent jumping to top
                header('Location: '.$_SERVER['REQUEST_URI']);
            } else {
                // For top-level comments, scroll to the new comment
                header('Location: '.$_SERVER['REQUEST_URI'].'#comment-'.$last_comment_id);
            }
            exit;
        } else {
            $comment_error = 'Comment cannot be empty.';
        }
    } else {
        // Do not set error message here
    }
}

// Handle delete request
if (isset($_GET['delete_comment'])) {
    $delete_id = intval($_GET['delete_comment']);
    // Fetch comment owner
    $stmt = $pdo->prepare('SELECT user_name, review_id FROM game_review_comments WHERE id = ?');
    $stmt->execute([$delete_id]);
    $comment = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($comment) {
        $can_delete = false;
        if (!empty($_SESSION['is_admin']) && $_SESSION['is_admin']) {
            $can_delete = true; // Admin can delete any
        } elseif (!empty($_SESSION['user_name']) && $_SESSION['user_name'] === $comment['user_name']) {
            $can_delete = true; // User can delete own
        }
        if ($can_delete) {
            $stmt = $pdo->prepare('DELETE FROM game_review_comments WHERE id = ? OR parent_comment_id = ?');
            $stmt->execute([$delete_id, $delete_id]); // Also delete replies
            // Redirect to comments anchor to prevent scroll-to-top
            header('Location: '.$_SERVER['PHP_SELF'].'?id=' . $comment['review_id'] . '#comments');
            exit;
        } else {
            echo '<div style="color:yellow; background:#900; padding:6px;">DEBUG: Delete failed. Admin? '.(!empty($_SESSION['is_admin']) && $_SESSION['is_admin'] ? 'YES' : 'NO').', User: '.htmlspecialchars($_SESSION['user_name'] ?? '').', Owner: '.htmlspecialchars($comment['user_name']).'</div>';
        }
    } else {
        echo '<div style="color:yellow; background:#900; padding:6px;">DEBUG: Comment not found for delete ID '.htmlspecialchars($delete_id).'</div>';
    }
}

// Handle edit request
if (isset($_POST['edit_comment_id']) && isset($_POST['edit_comment_text'])) {
    $edit_id = intval($_POST['edit_comment_id']);
    $edit_text = trim($_POST['edit_comment_text']);
    $stmt = $pdo->prepare('SELECT user_name FROM game_review_comments WHERE id = ?');
    $stmt->execute([$edit_id]);
    $comment = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($comment) {
        $can_edit = false;
        $current_email = $_SESSION['user_email'] ?? null;
        $is_admin = !empty($_SESSION['is_admin']) && $_SESSION['is_admin'] && $current_email === 'syedmhasan229@gmail.com';
        // Only allow editing own comment (admin or user)
        if (!empty($_SESSION['user_name']) && $_SESSION['user_name'] === $comment['user_name']) {
            $can_edit = true;
        }
        if ($can_edit) {
            $stmt = $pdo->prepare('UPDATE game_review_comments SET comment = ? WHERE id = ?');
            $stmt->execute([$edit_text, $edit_id]);
            header('Location: '.$_SERVER['REQUEST_URI'].'#comment-'.$edit_id);
            exit;
        }
    }
}

// Fetch comments for this review (with parent_comment_id)
$stmt = $pdo->prepare('SELECT id, user_name, comment, created_at, parent_comment_id FROM game_review_comments WHERE review_id = ? ORDER BY created_at ASC');
$stmt->execute([$review_id]);
$all_comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Recursive function to render multi-level nested comments
function render_comments($comments, $parent_id = null, $level = 0) {
    $current_user = $_SESSION['user_name'] ?? null;
    $current_email = $_SESSION['user_email'] ?? null;
    $is_admin = !empty($_SESSION['is_admin']) && $_SESSION['is_admin'] && $current_email === 'syedmhasan229@gmail.com';
    foreach ($comments as $comment) {
        if ($comment['parent_comment_id'] == $parent_id) {
            ?>
            <div class="comment-item" id="comment-<?= $comment['id'] ?>" style="margin-left:<?= $level * 32 ?>px; margin-bottom:18px; padding-bottom:10px; border-bottom:1px solid #35355a;">
                <div style="font-weight:bold; color:<?= $level ? '#33c96a' : '#3ee47c' ?>;">
                    <?= htmlspecialchars($comment['user_name']) ?>
                    <span style="font-weight:normal; color:#aaa; font-size:0.95em; margin-left:8px;">
                        <?= date('M d, Y H:i', strtotime($comment['created_at'])) ?>
                    </span>
                </div>
                <div style="margin-top:4px; color:#e0e0ff;">
                    <?= nl2br(htmlspecialchars($comment['comment'])) ?>
                </div>
                <a href="javascript:void(0);" class="reply-link" data-id="<?= $comment['id'] ?>" role="button">Reply</a>
                <div class="comment-actions" style="margin-top:6px;">
                <?php
                // Edit button logic
                $can_edit = ($is_admin && $comment['user_name'] === $current_user) || (!$is_admin && $comment['user_name'] === $current_user);
                if ($can_edit) {
                    echo '<button type="button" class="edit-comment-btn" data-edit-id="'.htmlspecialchars($comment['id']).'">Edit</button>';
                    // Hidden edit form
                    echo '<form method="post" class="edit-comment-form" id="edit-form-'.htmlspecialchars($comment['id']).'" style="display:none; margin-top:8px;">'
                        .'<input type="hidden" name="edit_comment_id" value="'.htmlspecialchars($comment['id']).'">'
                        .'<input type="text" name="edit_comment_text" value="'.htmlspecialchars($comment['comment']).'" required style="width:220px;">'
                        .'<button type="submit" class="save-edit-btn">Save</button>'
                        .'<button type="button" class="cancel-edit-btn" data-cancel-id="'.htmlspecialchars($comment['id']).'">Cancel</button>'
                        .'</form>';
                }
                // Delete button logic
                if (
                    ($is_admin) || // admin can delete any
                    (!$is_admin && $comment['user_name'] === $current_user) // user can delete own
                ) {
                    echo '<form method="get" class="delete-comment-form" style="display:inline; margin-left:8px;">
                        <input type="hidden" name="id" value="'.htmlspecialchars($_GET['id']).'">
                        <input type="hidden" name="delete_comment" value="'.htmlspecialchars($comment['id']).'">
                        <button type="button" class="delete-comment-btn custom-delete-btn">Delete</button>'
                        .'</form>';
                }
                ?>
                </div>
<?php
    $reply_count = 0;
    foreach ($comments as $c) {
        if ($c['parent_comment_id'] == $comment['id']) {
            $reply_count++;
        }
    }
    if ($reply_count > 0):
?>
    <span class="reply-count" style="color:#3ea6ff; cursor:pointer; margin-left:12px; font-weight:500;">
        <?= $reply_count ?> <?= $reply_count === 1 ? 'reply' : 'replies' ?>
    </span>
<?php endif; ?>
                <form class="reply-form" data-parent="<?= $comment['id'] ?>" method="post" style="display:none;margin-top:8px;">
                    <textarea name="comment" placeholder="Write a reply..." required style="height:48px;"></textarea>
                    <input type="hidden" name="parent_comment_id" value="<?= $comment['id'] ?>">
                    <div class="comment-actions">
                        <button type="submit" class="post-comment-btn">Post Reply</button>
                    </div>
                </form>
            <?php
            // Replies wrapper (hidden by default, toggled by reply-count click)
            echo '<div class="replies-wrapper" data-parent="' . $comment['id'] . '" style="display:none;">';
            render_comments($comments, $comment['id'], $level + 1);
            echo '</div>';
            ?>
            </div>
            <?php
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($review['game_name']); ?> Review - Assemble Gaming Zone</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .review-detail-header { max-width: 1100px; margin: 40px auto 24px auto; background: #23244d; border-radius: 16px; box-shadow: 0 6px 32px #7c3aed33; padding: 36px 48px; color: #e0e0ff; display: flex; gap: 36px; align-items: flex-start; }
        .review-main-image { max-width: 340px; border-radius: 12px; box-shadow: 0 2px 16px #7c3aed33; }
        .review-main-info { flex: 1; }
        .review-main-info h1 { color: #fff; font-size: 2.5rem; margin-bottom: 18px; }
        .review-meta { margin: 0 0 16px 0; color: #e0e0ff; font-size: 1.15rem; }
        .review-meta strong { color: #a594f9; width: 120px; display: inline-block; }
        .score-box { display: inline-block; background: #2ecc40; color: #fff; font-weight: bold; border-radius: 8px; padding: 8px 24px; font-size: 1.4rem; margin-top: 18px; }

        .section-container {
            max-width: 1100px;
            margin: 24px auto;
            background: #23244d;
            border-radius: 14px;
            box-shadow: 0 4px 24px #7c3aed22;
            padding: 32px 40px;
            color: #e0e0ff;
            margin-bottom: 28px;
        }
        .section-container .gallery-slider {
            max-width: 100%;
            width: 100%;
            position: static;
            left: unset;
            right: unset;
            margin-left: 0;
            margin-right: 0;
            padding-left: 0;
            padding-right: 0;
            border-radius: 10px;
        }
        .gallery-slider {
            margin-top: 12px;
            background: #181a2a;
            border-radius: 10px;
            padding: 18px 24px 18px 24px;
            box-shadow: 0 2px 12px #0003;
            max-width: 100%;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: static;
            left: unset;
            right: unset;
            margin-left: 0;
            margin-right: 0;
        }
        .gallery-main {
            min-height: 0;
            width: 100%;
            max-width: 100%;
            aspect-ratio: 16/9;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #11111a;
            border-radius: 8px;
            margin-bottom: 16px;
            position: relative;
            overflow: hidden;
        }
        .gallery-main img.gallery-slide {
            max-width: 100%;
            max-height: 100%;
            width: 100%;
            height: 100%;
            border-radius: 8px;
            display: block;
            margin: 0 auto;
            box-shadow: 0 2px 10px #0004;
            object-fit: cover;
            background: #181a2a;
        }
        .gallery-thumbs {
            width: 100%;
            max-width: 100%;
            display: flex;
            gap: 12px;
            justify-content: center;
            margin-top: 8px;
            flex-wrap: wrap;
            position: static;
            left: unset;
            right: unset;
            margin-left: 0;
            margin-right: 0;
        }
        .gallery-thumb {
            width: 90px;
            height: 54px;
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
        .gallery-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(60, 20, 120, 0.85);
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 56px;
            height: 56px;
            font-size: 2.4rem;
            cursor: pointer;
            opacity: 0.85;
            z-index: 10;
            box-shadow: 0 2px 12px #0007;
            transition: background 0.2s, opacity 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .gallery-arrow.left { left: 18px; }
        .gallery-arrow.right { right: 18px; }
        .gallery-arrow:hover {
            background: #7c3aed;
            opacity: 1;
        }
        .trailer-card { max-width: 1100px; width: 100%; margin: 24px auto; background: #181a2a; border-radius: 10px; padding: 22px 26px 28px 26px; box-shadow: 0 2px 12px #7c3aed22; }
        .trailer-header { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
        .trailer-dot { width: 16px; height: 16px; border-radius: 50%; background: #2ecc40; display: inline-block; }
        .trailer-header-title { color: #e0e0ff; font-size: 1.25rem; font-weight: bold; }
        .trailer-iframe-wrap { aspect-ratio: 16/9; width: 100%; background: #000; border-radius: 8px; overflow: hidden; }
        .trailer-iframe-wrap iframe { width: 100%; height: 100%; border: none; }
        .review-body-block { background: #181a2a; border-radius: 10px; padding: 28px 32px; font-size: 1.17rem; color: #f0f0ff; box-shadow: 0 2px 12px #7c3aed22; }
        .review-body-block p { margin: 0 0 14px 0; line-height: 1.7; }
        /* System requirements modern card */
        .section-container.system-req-section {
            max-width: 1100px;
            width: 100%;
            margin: 24px auto;
            background: #23244d;
            border-radius: 14px;
            box-shadow: 0 4px 24px #7c3aed22;
            padding: 32px 40px;
            color: #e0e0ff;
            margin-bottom: 28px;
            box-sizing: border-box;
        }
        .section-container.system-req-section h2 {
            margin-bottom: 22px;
        }
        .system-req-container {
            display: flex;
            gap: 32px;
            margin-bottom: 20px;
        }
        .system-req-box {
            display: flex;
            flex-direction: column;
            flex: 1;
            background: #23232b;
            border-radius: 16px;
            box-shadow: 0 4px 24px #7c3aed22;
            padding: 28px 24px 24px 24px;
            box-sizing: border-box;
            min-width: 320px;
        }
        .system-req-box label {
            display: none;
        }
        .sysreq-title {
            color: #fff;
            font-size: 1.3rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .sysreq-display {
            background: transparent;
            color: #fff;
            border: none;
            border-radius: 0;
            padding: 0;
            font-size: 1rem;
            min-height: 180px;
            white-space: pre-wrap;
            width: 100%;
            box-sizing: border-box;
            font-family: inherit;
        }
        .requirements-flex {
            display: flex;
            gap: 32px;
            justify-content: center;
            align-items: stretch;
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
        }
        .requirements-col {
            flex: 1;
            background: #181a2a;
            border-radius: 10px;
            padding: 32px 36px 32px 36px;
            margin: 0 18px;
            min-width: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            box-sizing: border-box;
            min-height: 420px; /* Match trailer aspect ratio visually */
            max-width: 440px;
        }
        @media (max-width: 900px) {
            .requirements-flex {
                flex-direction: column;
                max-width: 100%;
            }
            .requirements-col {
                margin: 12px 0;
                padding: 24px 12px 24px 12px;
            }
        }
        /* Game trailer card style */
        .buy-links-list { margin-top: 10px; }
        .buy-links-list div { margin-bottom: 8px; }
        iframe { border-radius: 8px; background: #181a2a; }

        /* --- Custom Where to Buy and Comments Styling --- */
        .buy-options {
            display: flex;
            gap: 28px;
            margin-top: 20px;
            width: 100%;
            padding: 0 8px;
            justify-content: center;
        }
        .buy-card {
            background: #23232b;
            border-radius: 8px;
            flex: 1;
            padding: 32px 0 24px 0;
            text-align: center;
            min-width: 0;
            box-shadow: none;
            margin: 0;
            max-width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .platform-name {
            font-size: 16px;
            color: #ccc;
            margin-bottom: 10px;
            font-weight: 500;
        }
        .platform-price {
            color: #3ee47c;
            font-size: 22px;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .post-comment-btn {
            background: #3ee47c;
            color: #19191c;
            border: none;
            border-radius: 4px;
            padding: 8px 18px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s;
        }
        .post-comment-btn:hover {
            background: #33c96a;
        }
        .comment-box {
            width: 100%;
            height: 90px;
            background: #23232b;
            border: none;
            border-radius: 6px;
            color: #fff;
            padding: 12px;
            margin-top: 16px;
            margin-bottom: 12px;
            resize: none;
            font-size: 15px;
            overflow-y: hidden;
        }
        /* --- End Custom Styling --- */

        /* Make Where to Buy section full width like System Requirements */
        .section-container.where-to-buy-section {
            max-width: 1100px;
            margin: 24px auto;
            background: #23244d;
            border-radius: 14px;
            box-shadow: 0 4px 24px #7c3aed22;
            padding: 32px 40px;
            color: #e0e0ff;
            margin-bottom: 28px;
            width: 100%;
            box-sizing: border-box;
        }
        .buy-options {
            display: flex;
            gap: 28px;
            margin-top: 20px;
            width: 100%;
            padding: 0 8px;
            justify-content: center;
        }
        .buy-card {
            background: #23232b;
            border-radius: 8px;
            flex: 1;
            padding: 32px 0 24px 0;
            text-align: center;
            min-width: 0;
            box-shadow: none;
            margin: 0;
            max-width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        /* --- Comment Section Styling --- */
        .comments-section {
            max-width: 1100px;
            width: 100%;
            margin: 24px auto;
            background: #23244d;
            border-radius: 14px;
            box-shadow: 0 4px 24px #7c3aed22;
            padding: 32px 40px 28px 40px;
            color: #e0e0ff;
            margin-bottom: 28px;
            box-sizing: border-box;
        }
        .comments-section h2 {
            margin-bottom: 18px;
        }
        .comments-form textarea {
            width: 100%;
            min-height: 60px;
            max-height: 300px;
            background: #23232b;
            border: 1px solid #44445a;
            border-radius: 6px;
            color: #e0e0ff;
            font-size: 1rem;
            padding: 12px 14px;
            resize: none;
            margin-bottom: 14px;
            overflow-y: hidden;
            transition: height 0.2s;
        }
        .post-comment-btn {
            background: #3ee47c;
            color: #23232b;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            padding: 10px 24px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .post-comment-btn:hover {
            background: #2ec96a;
        }

        .reply-link {
            color: #fff;
            background: linear-gradient(90deg, #7c3aed 60%, #33c96a 100%);
            border: none;
            border-radius: 18px;
            font-size: 1em;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            margin-top: 8px;
            margin-bottom: 4px;
            display: inline-block;
            padding: 4px 18px 4px 18px;
            box-shadow: 0 2px 8px #7c3aed18;
            transition: background 0.2s, color 0.2s, box-shadow 0.2s;
        }
        .reply-link:hover, .reply-link:focus {
            background: linear-gradient(90deg, #33c96a 60%, #7c3aed 100%);
            color: #fff;
            text-decoration: none;
            box-shadow: 0 4px 12px #33c96a33;
        }
        .reply-form {
            background: #25254a;
            border-radius: 8px;
            box-shadow: 0 2px 12px #7c3aed22;
            padding: 12px 14px 14px 14px;
            margin-top: 8px;
            margin-bottom: 10px;
            width: 100%;
            max-width: 100%;
            border: 1px solid #34345a;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .reply-form textarea {
            background: #18182a;
            border: 1px solid #44445a;
            border-radius: 6px;
            color: #e0e0ff;
            font-size: 1em;
            padding: 10px 12px;
            resize: none;
            min-height: 48px;
            max-height: 220px;
            width: 100%;
            box-sizing: border-box;
            transition: border 0.2s, box-shadow 0.2s;
        }
        .reply-form textarea:focus {
            border: 1.5px solid #a594f9;
            outline: none;
            box-shadow: 0 0 0 2px #7c3aed33;
        }
        .reply-form .post-comment-btn {
            background: linear-gradient(90deg, #7c3aed 60%, #33c96a 100%);
            color: #fff;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            padding: 8px 18px;
            cursor: pointer;
            align-self: flex-end;
            margin-top: 2px;
            transition: background 0.2s;
        }
        .reply-form .post-comment-btn:hover {
            background: linear-gradient(90deg, #33c96a 60%, #7c3aed 100%);
        }

        /* --- Action Buttons Styling --- */
        .comment-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: flex-end;
            margin-top: 6px;
        }
        /* Modern Edit/Save/Cancel/Delete styles for comment actions */
        .edit-comment-form {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #23234b;
            padding: 12px 16px;
            border-radius: 8px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.18);
            margin-top: 8px;
        }
        .edit-comment-form input[type="text"] {
            background: #18183a;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 7px 14px;
            font-size: 1rem;
            outline: none;
            transition: box-shadow 0.2s;
            box-shadow: 0 1px 4px rgba(0,0,0,0.10);
            width: 220px;
        }
        .edit-comment-form input[type="text"]:focus {
            box-shadow: 0 0 0 2px #6c63ff;
        }
        .edit-comment-btn, .save-edit-btn, .cancel-edit-btn, .delete-comment-btn {
            background: #34346e;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 8px 18px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s;
        }
        .edit-comment-btn {
            background: #4848a1;
        }
        .edit-comment-btn:hover {
            background: #6c63ff;
        }
        .save-edit-btn {
            background: #6c63ff;
        }
        .save-edit-btn:hover {
            background: #857cff;
        }
        .cancel-edit-btn {
            background: #44445d;
        }
        .cancel-edit-btn:hover {
            background: #666687;
        }
        .delete-comment-btn {
            background: #ff4d4d;
        }
        .delete-comment-btn:hover {
            background: #ff6b6b;
        }
            margin-top: 0;
            margin-bottom: 2px;
        }
        .reply-link {
            margin-right: 8px;
        }
        .post-comment-btn, .edit-comment-form .post-comment-btn {
            background: linear-gradient(90deg, #7c3aed 60%, #33c96a 100%);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            padding: 7px 18px;
            cursor: pointer;
            margin: 0;
            font-size: 1em;
            box-shadow: 0 2px 8px #7c3aed18;
            transition: background 0.2s, color 0.2s, box-shadow 0.2s;
        }
        .post-comment-btn:hover, .edit-comment-form .post-comment-btn:hover {
            background: linear-gradient(90deg, #33c96a 60%, #7c3aed 100%);
        }
        .post-comment-btn.delete {
            background: #ff6b6b;
            color: #fff;
        }
        .post-comment-btn.delete:hover {
            background: #e74c3c;
        }
        .post-comment-btn.cancel {
            background: #444;
            color: #fff;
        }
        .post-comment-btn.cancel:hover {
            background: #222;
        }
        .reply-link {
            margin-right: 0;
        }
        /* Edit/Delete button modern styles */
        .edit-comment-btn, .delete-comment-btn, .save-edit-btn, .cancel-edit-btn {
            background: #282a4d;
            color: #fff;
            border: none;
            padding: 6px 18px;
            margin-right: 6px;
            border-radius: 5px;
            font-size: 1em;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
        }
        .edit-comment-btn:hover, .save-edit-btn:hover {
            background: #3ea6ff;
            color: #181a2a;
        }
        .delete-comment-btn:hover {
            background: #ff4d4f;
            color: #fff;
        }
        .cancel-edit-btn:hover {
            background: #444;
            color: #fff;
        }
        .edit-comment-form {
            display: flex;
            align-items: center;
            gap: 8px;
        }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>
<div class="review-detail-header">
    <img class="review-main-image" src="<?php echo htmlspecialchars($review['main_image']); ?>" alt="Game Image">
    <div class="review-main-info">
        <h1><?php echo htmlspecialchars($review['game_name']); ?></h1>
        <div class="review-meta"><strong>Developer:</strong> <?php echo htmlspecialchars($review['developer']); ?></div>
        <div class="review-meta"><strong>Publisher:</strong> <?php echo htmlspecialchars($review['publisher']); ?></div>
        <div class="review-meta"><strong>Release Date:</strong> <?php echo date('F d, Y', strtotime($review['release_date'])); ?></div>
        <div class="review-meta"><strong>Genre:</strong> <?php echo htmlspecialchars($review['genre']); ?></div>
        <div class="review-meta"><strong>Platforms:</strong> <?php echo htmlspecialchars($review['platforms']); ?></div>
        <div class="review-meta"><strong>Language:</strong> <?php echo htmlspecialchars($review['language']); ?></div>
        <div class="score-box">Score: <?php echo htmlspecialchars($review['score']); ?></div>
    </div>
</div>
<div class="section-container">
    <h2>Review</h2>
    <div class="review-body-block"><?php echo nl2br(htmlspecialchars($review['review'])); ?></div>
</div>
<?php if (!empty($review['trailer_url'])): ?>
<div class="trailer-card">
    <div class="trailer-header">
        <span class="trailer-dot"></span>
        <span class="trailer-header-title">Game Trailer</span>
    </div>
    <div class="trailer-iframe-wrap">
        <?php if ($trailer_youtube_id): ?>
            <iframe src="https://www.youtube.com/embed/<?php echo htmlspecialchars($trailer_youtube_id); ?>" allowfullscreen></iframe>
        <?php else: ?>
            <iframe src="<?php echo htmlspecialchars($review['trailer_url']); ?>" allowfullscreen></iframe>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>
<?php if (count($gallery) > 0): ?>
<div class="section-container">
    <h2>Photo Gallery</h2>
    <div class="gallery-slider">
        <div class="gallery-main">
            <?php foreach ($gallery as $i => $img): ?>
                <img src="<?= htmlspecialchars($img) ?>" alt="Gallery Image" class="gallery-slide" style="display:<?= $i === 0 ? 'block' : 'none' ?>;">
            <?php endforeach; ?>
            <button type="button" class="gallery-arrow left" onclick="moveGallerySlide(-1)">&#8592;</button>
            <button type="button" class="gallery-arrow right" onclick="moveGallerySlide(1)">&#8594;</button>
        </div>
        <div class="gallery-thumbs">
            <?php foreach ($gallery as $i => $img): ?>
                <img src="<?= htmlspecialchars($img) ?>" alt="Gallery Thumb" class="gallery-thumb" onclick="showGallerySlide(<?= $i ?>)">
            <?php endforeach; ?>
        </div>
    </div>
</div>
<script>
window.addEventListener('DOMContentLoaded', function() {
    let galleryIndex = 0;
    const slides = document.querySelectorAll('.gallery-slide');
    const thumbs = document.querySelectorAll('.gallery-thumb');

    function showGallerySlide(idx) {
        galleryIndex = idx;
        slides.forEach((img, i) => img.style.display = i === idx ? 'block' : 'none');
        thumbs.forEach((img, i) => {
            img.style.opacity = i === idx ? 1 : 0.5;
            if (i === idx) img.classList.add('active');
            else img.classList.remove('active');
        });
    }
    window.showGallerySlide = showGallerySlide;
    window.moveGallerySlide = function(dir) {
        galleryIndex += dir;
        if (galleryIndex < 0) galleryIndex = slides.length - 1;
        if (galleryIndex >= slides.length) galleryIndex = 0;
        showGallerySlide(galleryIndex);
    }
    showGallerySlide(0);

    // Keyboard arrow navigation (robust, works in all browsers)
    document.addEventListener('keydown', function(e) {
        // Only trigger if the gallery is visible and no input/textarea is focused
        if (document.activeElement && (document.activeElement.tagName === 'INPUT' || document.activeElement.tagName === 'TEXTAREA' || document.activeElement.isContentEditable)) return;
        if (e.key === 'ArrowRight') {
            e.preventDefault();
            if (typeof window.moveGallerySlide === 'function') window.moveGallerySlide(1);
        } else if (e.key === 'ArrowLeft') {
            e.preventDefault();
            if (typeof window.moveGallerySlide === 'function') window.moveGallerySlide(-1);
        }
    }, true);
});
</script>
<?php endif; ?>
<?php
$has_buy_card = false;
foreach ($buy_links as $store => $item) {
    if ($item && isset($item['url'])) {
        $has_buy_card = true;
        break;
    }
}
?>
<?php if ($has_buy_card): ?>
<div class="section-container where-to-buy-section">
    <h2>Where to Buy</h2>
    <div class="buy-options">
        <?php foreach ($buy_links as $store => $item): if (empty($item['url'])) continue; ?>
            <a href="<?php echo htmlspecialchars($item['url']); ?>" target="_blank" style="text-decoration:none;flex:1;">
                <div class="buy-card">
                    <div class="platform-name"><?php echo htmlspecialchars(ucfirst($store)); ?></div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>
<?php else: ?>
<div class="section-container where-to-buy-section">
    <h2>Where to Buy</h2>
    <div style="text-align:center; padding:36px 0; color:#ccc; font-size:1.2rem;">No buying options available at this time.</div>
</div>
<?php endif; ?>
<div class="section-container system-req-section">
    <h2>System Requirements</h2>
    <div class="system-req-container">
        <div class="system-req-box">
            <div class="sysreq-title">Minimum</div>
            <pre class="sysreq-display"><?php echo htmlspecialchars($requirements_min); ?></pre>
        </div>
        <div class="system-req-box">
            <div class="sysreq-title">Recommended</div>
            <pre class="sysreq-display"><?php echo htmlspecialchars($requirements_rec); ?></pre>
        </div>
    </div>
</div>
<div class="comments-section" id="comments">
    <h2>Comments</h2>
    <!-- DEBUG: Show session username (hidden, not removed) -->
    <div style="display:none;color:#3ee47c;margin-bottom:8px;">Debug: user_name = <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'NOT SET'); ?></div>
    <form class="comments-form" method="post" autocomplete="off">
        <?php if (!empty($comment_error)): ?>
            <div style="color: #ff6b6b; margin-bottom: 10px;"> <?= htmlspecialchars($comment_error) ?> </div>
        <?php endif; ?>
        <textarea name="comment" placeholder="Share your thoughts about this game..." required></textarea>
        <input type="hidden" name="parent_comment_id" value="">
        <button type="submit" class="post-comment-btn">Post Comment</button>
    </form>
    <div class="comments-list" style="margin-top:24px;">
    <?php if ($all_comments): ?>
        <?php render_comments($all_comments); ?>
    <?php else: ?>
        <div style="color:#aaa;">No comments yet. Be the first to comment!</div>
    <?php endif; ?>
</div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Event delegation for reply buttons
    document.body.addEventListener('click', function(e) {
        if (e.target.classList.contains('reply-link')) {
            e.preventDefault();
            // Hide all reply forms
            document.querySelectorAll('.reply-form').forEach(f => f.style.display = 'none');
            // Show only the relevant reply form
            var parentId = e.target.getAttribute('data-id');
            var form = document.querySelector(`.reply-form[data-parent='${parentId}']`);
            if (form) {
                form.style.display = 'block';
                var textarea = form.querySelector('textarea');
                if (textarea) {
                    textarea.focus();
                }
            }
        }
    });
});
<?php


?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Event delegation for reply buttons
    document.body.addEventListener('click', function(e) {
        if (e.target.classList.contains('reply-link')) {
            e.preventDefault();
            // Hide all reply forms
            document.querySelectorAll('.reply-form').forEach(f => f.style.display = 'none');
            // Show only the relevant reply form
            var parentId = e.target.getAttribute('data-id');
            var form = document.querySelector(`.reply-form[data-parent='${parentId}']`);
            if (form) {
                form.style.display = 'block';
                var textarea = form.querySelector('textarea');
                if (textarea) {
                    textarea.focus();
                }
            }
        }
    });

    // Auto-expand all reply textarea as user types
    document.querySelectorAll('.reply-form textarea').forEach(textarea => {
        textarea.setAttribute('style', 'height:' + textarea.scrollHeight + 'px;overflow-y:hidden;');
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    });

    // Hide reply form when clicking outside
    document.addEventListener('mousedown', function(event) {
        if (!event.target.closest('.reply-form') && !event.target.closest('.reply-link')) {
            document.querySelectorAll('.reply-form').forEach(f => f.style.display = 'none');
        }
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle edit form for the correct comment
    document.body.addEventListener('click', function(e) {
        if (e.target.classList.contains('edit-comment-btn')) {
            const commentId = e.target.getAttribute('data-edit-id');
            document.querySelectorAll('.edit-comment-form').forEach(f => f.style.display = 'none');
            const form = document.getElementById('edit-form-' + commentId);
            if (form) form.style.display = 'flex';
        }
        if (e.target.classList.contains('cancel-edit-btn')) {
            const commentId = e.target.getAttribute('data-cancel-id');
            const form = document.getElementById('edit-form-' + commentId);
            if (form) form.style.display = 'none';
        }
    });

    // Restore scroll position after reply (existing logic)
    var scrollY = sessionStorage.getItem('scrollY');
    if (scrollY !== null) {
        setTimeout(function() {
            window.scrollTo(0, parseInt(scrollY));
            sessionStorage.removeItem('scrollY');
        }, 10);
    }
    // Save scroll position before submitting any reply form
    document.querySelectorAll('.reply-form').forEach(function(form) {
        form.addEventListener('submit', function() {
            sessionStorage.setItem('scrollY', window.scrollY);
        });
    });
});
</script>
<script>
// Custom scroll to anchor with offset for comment/reply focus
window.addEventListener('DOMContentLoaded', function() {
    if (window.location.hash.startsWith('#comment-')) {
        const el = document.querySelector(window.location.hash);
        if (el) {
            const yOffset = -40; // Adjust this offset for your header height
            const y = el.getBoundingClientRect().top + window.pageYOffset + yOffset;
            window.scrollTo({ top: y, behavior: 'smooth' });
        }
    }
});
</script>
<script>
function attachReplyListeners() {
    // Reply button show/hide logic
    document.querySelectorAll('.reply-link').forEach(link => {
        link.onclick = function(e) {
            e.preventDefault();
            document.querySelectorAll('.reply-form').forEach(f => f.style.display = 'none');
            var form = document.querySelector(`.reply-form[data-parent='${this.dataset.id}']`);
            if (form) {
                form.style.display = 'block';
                var textarea = form.querySelector('textarea');
                if (textarea) textarea.focus();
            }
        };
    });
    // Toggle replies-wrapper on reply-count click
    document.querySelectorAll('.reply-count').forEach(span => {
        span.onclick = function() {
            var parentId = this.previousElementSibling.getAttribute('data-id');
            var wrapper = document.querySelector('.replies-wrapper[data-parent="' + parentId + '"]');
            if (wrapper) {
                wrapper.style.display = (wrapper.style.display === 'none' || wrapper.style.display === '') ? 'block' : 'none';
            }
        };
    });
    // Auto-expand reply textarea
    document.querySelectorAll('.reply-form textarea').forEach(textarea => {
        textarea.setAttribute('style', 'height:' + textarea.scrollHeight + 'px;overflow-y:hidden;');
        textarea.oninput = function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        };
    });
    // Hide reply form when clicking outside
    document.addEventListener('mousedown', function(event) {
        if (!event.target.closest('.reply-form') && !event.target.closest('.reply-link')) {
            document.querySelectorAll('.reply-form').forEach(f => f.style.display = 'none');
        }
    });
}

window.addEventListener('DOMContentLoaded', function() {
    attachReplyListeners();
    document.body.addEventListener('submit', function(e) {
        if (e.target.classList.contains('reply-form')) {
            e.preventDefault();
            var form = e.target;
            var formData = new FormData(form);
            fetch(window.location.pathname + window.location.search, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(response => response.text())
            .then(html => {
                // Reload only the comments section, not the whole page
                var parser = new DOMParser();
                var doc = parser.parseFromString(html, 'text/html');
                var newComments = doc.querySelector('.comments-list');
                if (newComments) {
                    document.querySelector('.comments-list').innerHTML = newComments.innerHTML;
                    attachReplyListeners(); // re-attach listeners after AJAX update
                }
            });
        }
    });
});
</script>
<?php include 'includes/footer.php'; ?>
<style>
/* Custom Modal Styles */
#deleteConfirmModal {
  display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100vw; height: 100vh;
  background: rgba(0,0,0,0.55); align-items: center; justify-content: center;
}
#deleteConfirmModal .modal-content {
  background: #23244d; color: #fff; padding: 32px 36px; border-radius: 12px; box-shadow: 0 8px 40px #000a;
  min-width: 320px; max-width: 95vw; text-align: center;
}
#deleteConfirmModal .modal-actions {
  margin-top: 24px; display: flex; justify-content: center; gap: 18px;
}
#deleteConfirmModal .modal-btn {
  padding: 8px 28px; border-radius: 6px; border: none; font-size: 1.09em; font-weight: 500; cursor: pointer;
  transition: background 0.18s, color 0.18s;
}
#deleteConfirmModal .modal-btn.confirm { background: #ff4d4f; color: #fff; }
#deleteConfirmModal .modal-btn.confirm:hover { background: #ff7375; }
#deleteConfirmModal .modal-btn.cancel { background: #35355a; color: #fff; }
#deleteConfirmModal .modal-btn.cancel:hover { background: #44446a; }
</style>
<div id="deleteConfirmModal">
  <div class="modal-content">
    <div style="font-size:1.19em; font-weight:600; margin-bottom:18px;">Are you sure you want to delete this comment?</div>
    <div class="modal-actions">
      <button class="modal-btn confirm" id="modalDeleteConfirmBtn">Delete</button>
      <button class="modal-btn cancel" id="modalDeleteCancelBtn">Cancel</button>
    </div>
  </div>
</div>
<script src="js/toggle-replies.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Save scroll position before delete
    document.body.addEventListener('submit', function(e) {
        if (e.target.classList.contains('delete-comment-form')) {
            sessionStorage.setItem('scrollY', window.scrollY);
        }
    }, true);
    // Restore scroll position after reload
    var scrollY = sessionStorage.getItem('scrollY');
    if (scrollY !== null) {
        setTimeout(function() {
            window.scrollTo(0, parseInt(scrollY));
            sessionStorage.removeItem('scrollY');
        }, 10);
    }
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let pendingDeleteForm = null;
    document.body.addEventListener('click', function(e) {
        if (e.target.classList.contains('custom-delete-btn')) {
            e.preventDefault();
            pendingDeleteForm = e.target.closest('form');
            document.getElementById('deleteConfirmModal').style.display = 'flex';
        }
    });
    document.getElementById('modalDeleteCancelBtn').onclick = function() {
        document.getElementById('deleteConfirmModal').style.display = 'none';
        pendingDeleteForm = null;
    };
    document.getElementById('modalDeleteConfirmBtn').onclick = function() {
        if (pendingDeleteForm) {
            document.getElementById('deleteConfirmModal').style.display = 'none';
            pendingDeleteForm.submit();
            pendingDeleteForm = null;
        }
    };
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.getElementById('deleteConfirmModal').style.display = 'none';
            pendingDeleteForm = null;
        }
    });
});
</script>
</body>
</html>