<?php
// Set the content type header to output an image
header('Content-Type: image/jpeg');

// Create a 400x500 image
$width = 400;
$height = 500;
$image = imagecreatetruecolor($width, $height);

// Define colors
$bg = imagecolorallocate($image, 26, 26, 46); // Dark blue background
$accent = imagecolorallocate($image, 108, 92, 231); // Purple accent
$text = imagecolorallocate($image, 255, 255, 255); // White text

// Fill background
imagefill($image, 0, 0, $bg);

// Draw a stylized pattern
for ($i = 0; $i < $width; $i += 20) {
    imageline($image, $i, 0, $i + 100, $height, $accent);
}

// Add text
$text_box = imagettfbbox(20, 0, 'arial.ttf', 'Game Image');
if ($text_box === false) {
    // If TTF not available, use basic text
    imagestring($image, 5, $width/2 - 50, $height/2 - 10, 'Game Image', $text);
} else {
    imagettftext($image, 20, 0, $width/2 - $text_box[2]/2, $height/2, $text, 'arial.ttf', 'Game Image');
}

// Save the image
imagejpeg($image, 'images/game-placeholder.jpg', 90);
imagedestroy($image);

echo "Placeholder image created successfully!";
?>
