<?php
// Array of game images to download
$images = [
    'elden.jpg' => 'https://image.api.playstation.com/vulcan/ap/rnd/202108/0410/0Jz6uJLxOK7JOMMfcfHFBi1D.png',
    'god.jpg' => 'https://image.api.playstation.com/vulcan/ap/rnd/202207/1210/4f5tXVxb6PvQpX3PAXGsB2rj.png',
    'horizon.jpg' => 'https://image.api.playstation.com/vulcan/ap/rnd/202107/3100/HO8vkO9dTbf9qHKrTKYeIIJ4.png',
    'spider.jpg' => 'https://image.api.playstation.com/vulcan/ap/rnd/202306/1219/60eca3ac155cc0b39b274f31f3d2b73d437f0115778c4624.png',
    'star.jpg' => 'https://assets.xboxservices.com/assets/1d/5b/1d5bc84f-2135-4e2f-8ca6-bb000d98f576.jpg',
    'final.jpg' => 'https://image.api.playstation.com/vulcan/ap/rnd/202211/0711/S1jCzktWD7XJSLqmYYHFBpYP.png',
    'resident.jpg' => 'https://image.api.playstation.com/vulcan/ap/rnd/202210/0706/EVWyZD63pahuh95eKloFaHwC.png',
    'diablo.jpg' => 'https://blz-contentstack-images.akamaized.net/v3/assets/blt77f4425de611b362/blt6d7b0fd8453e72b9/63093e7c27876447e1742669/d4-open-graph_001.jpg',
    'baldur.jpg' => 'https://cdn.cloudflare.steamstatic.com/steam/apps/1086940/capsule_616x353.jpg'
];

// Create images directory if it doesn't exist
if (!file_exists('images')) {
    mkdir('images', 0777, true);
}

// Download each image
foreach ($images as $filename => $url) {
    $destination = 'images/' . $filename;
    if (!file_exists($destination)) {
        if ($imageData = @file_get_contents($url)) {
            file_put_contents($destination, $imageData);
            echo "Downloaded: $filename\n";
        } else {
            echo "Failed to download: $filename\n";
        }
    } else {
        echo "Already exists: $filename\n";
    }
}

echo "Done downloading images!\n";
?>
