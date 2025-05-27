<?php
session_start();
echo '<h2>SESSION DEBUG</h2>';
echo '<pre>';
print_r($_SESSION);
echo '</pre>';
?>
<p><a href="index.php">Go to Home</a></p>
<p><a href="login.php">Go to Login</a></p>
