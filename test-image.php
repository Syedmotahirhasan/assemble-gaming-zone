<?php
$imagePath = "images/screenshots/e1.jpg";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Image Test</title>
</head>
<body>
    <h1>Image Test</h1>
    <p>Image path: <?php echo $imagePath; ?></p>
    <p>File exists: <?php echo file_exists($imagePath) ? 'Yes' : 'No'; ?></p>
    <img src="<?php echo $imagePath; ?>" alt="Test image" style="max-width: 500px;">
</body>
</html>
