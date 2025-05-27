<?php

// List of game images to download
$gameImages = [
    'god.jpg' => 'https://image.api.playstation.com/vulcan/ap/rnd/202207/1210/4xJ8XB3bi888QTLZYdl7Oi0s.png',
    'horizon.jpg' => 'https://image.api.playstation.com/vulcan/ap/rnd/202107/3100/HO8vkO9dTbFBBgku6d31iyYk.png',
    'spider.jpg' => 'https://image.api.playstation.com/vulcan/ap/rnd/202306/1219/1c3c375cc85574e699832d7cdb380eef1644714765.jpg',
    'star.jpg' => 'https://cdn.cloudflare.steamstatic.com/steam/apps/1716740/capsule_616x353.jpg',
    'final.jpg' => 'https://image.api.playstation.com/vulcan/ap/rnd/202211/0310/Eeqgz1wgRJF0CmHmqVfGQrfA.png',
    'resident.jpg' => 'https://image.api.playstation.com/vulcan/ap/rnd/202210/0706/EVWyZD63pahHHUCSc7YwV0wq.png',
    'diablo.jpg' => 'https://blz-contentstack-images.akamaized.net/v3/assets/blt77f4425de611b362/blt6d7b0fd8453e72b9/646e720a71d0d0111a045934/d4-open-graph_001.jpg'
];

// Create images directory if it doesn't exist
if (!file_exists('images')) {
    mkdir('images', 0777, true);
}

// Download each image
foreach ($gameImages as $filename => $url) {
    $targetPath = 'images/' . $filename;
    
    // Skip if file already exists
    if (file_exists($targetPath)) {
        echo "Skipping {$filename} - already exists\n";
        continue;
    }
    
    // Try to download the image
    $imageData = @file_get_contents($url);
    if ($imageData === false) {
        echo "Failed to download {$filename}\n";
        continue;
    }
    
    // Save the image
    if (file_put_contents($targetPath, $imageData)) {
        echo "Successfully downloaded {$filename}\n";
    } else {
        echo "Failed to save {$filename}\n";
    }
}

echo "Done downloading images!\n";
?>
