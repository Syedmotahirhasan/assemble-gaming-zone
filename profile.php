<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require_once "includes/config.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Assemble Gaming Zone</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="profile-container">
        <div class="profile-box">
            <h2><i class="fas fa-user-circle"></i> My Profile</h2>
            
            <div class="profile-info">
                <div class="info-group">
                    <label><i class="fas fa-user"></i> Username</label>
                    <p><?php echo htmlspecialchars($_SESSION["username"]); ?></p>
                </div>
                <div class="info-group">
                    <label><i class="fas fa-envelope"></i> Email</label>
                    <p><?php echo htmlspecialchars($_SESSION["email"]); ?></p>
                </div>
            </div>

            <div class="profile-actions">
                <button class="btn-danger" onclick="showDeleteConfirmation()">
                    <i class="fas fa-user-times"></i> Delete Account
                </button>
            </div>
        </div>
    </div>

    <!-- Delete Account Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-exclamation-triangle"></i> Delete Account</h3>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete your account? This action cannot be undone.</p>
                <p class="warning-text">All your data will be permanently deleted.</p>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" onclick="hideDeleteConfirmation()">
                    <i class="fas fa-times"></i> No, Keep My Account
                </button>
                <button class="btn-danger" onclick="deleteAccount()">
                    <i class="fas fa-trash-alt"></i> Yes, Delete My Account
                </button>
            </div>
        </div>
    </div>

    <!-- Goodbye Modal -->
    <div id="goodbyeModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-heart-broken"></i> We're Sad to See You Go</h3>
            </div>
            <div class="modal-body">
                <p>Your account has been successfully deleted.</p>
                <p>We hope to see you again soon!</p>
            </div>
            <div class="modal-footer">
                <button class="btn-primary" onclick="window.location.href='index.php'">
                    <i class="fas fa-home"></i> Return to Home
                </button>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <style>
    .profile-container {
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    }

    .profile-box {
        background: rgba(16, 0, 43, 0.95);
        padding: 2rem;
        border-radius: 12px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        width: 100%;
        max-width: 500px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .profile-box h2 {
        color: #fff;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .profile-info {
        margin-bottom: 2rem;
    }

    .info-group {
        margin-bottom: 1.5rem;
        padding: 1rem;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 8px;
    }

    .info-group label {
        display: block;
        color: #6c5ce7;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    .info-group p {
        color: #fff;
        font-size: 1.1rem;
        margin: 0;
    }

    .profile-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .btn-danger {
        padding: 12px 24px;
        background: #ff3b30;
        color: #fff;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 1rem;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s;
    }

    .btn-danger:hover {
        background: #ff2419;
        transform: translateY(-2px);
    }

    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        z-index: 1000;
        backdrop-filter: blur(5px);
    }

    .modal-content {
        background: rgba(16, 0, 43, 0.95);
        width: 90%;
        max-width: 500px;
        margin: 20vh auto;
        border-radius: 12px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.1);
        animation: modalSlideIn 0.3s ease;
    }

    .modal-header {
        padding: 1.5rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .modal-header h3 {
        color: #fff;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .modal-body {
        padding: 1.5rem;
        color: #fff;
    }

    .modal-footer {
        padding: 1.5rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .warning-text {
        color: #ff3b30;
        font-weight: bold;
    }

    .btn-secondary {
        padding: 12px 24px;
        background: rgba(255, 255, 255, 0.1);
        color: #fff;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 1rem;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s;
    }

    .btn-secondary:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    .btn-primary {
        padding: 12px 24px;
        background: #6c5ce7;
        color: #fff;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 1rem;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s;
    }

    .btn-primary:hover {
        background: #5849c2;
    }

    @keyframes modalSlideIn {
        from { transform: translateY(-20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    </style>

    <script>
    function showDeleteConfirmation() {
        document.getElementById('deleteModal').style.display = 'block';
    }

    function hideDeleteConfirmation() {
        document.getElementById('deleteModal').style.display = 'none';
    }

    function showGoodbyeMessage() {
        document.getElementById('deleteModal').style.display = 'none';
        document.getElementById('goodbyeModal').style.display = 'block';
    }

    function deleteAccount() {
        fetch('includes/delete_account.php', {
            method: 'POST',
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showGoodbyeMessage();
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 3000);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }

    // Close modals when clicking outside
    window.onclick = function(event) {
        if (event.target.className === 'modal') {
            event.target.style.display = 'none';
        }
    }
    </script>
</body>
</html>
