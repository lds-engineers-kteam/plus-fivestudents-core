<?php 
$filepath = "9-Act-7-Comptine.pdf";
$dirpath = realpath("./images");
$imagick = new Imagick();
$imagick->setResolution(150, 150);
$imagick->readImage($filepath);
$imagick->writeImage("{$dirpath}/image.png");
// header("Content-Type: image/png");
// echo $imagick->getImageBlob();
// $imagick->setResolution(72, 72);
// $imageResolution = $imagick->getImageResolution();
// var_dump($imageResolution);
// $imagick->setImageFormat('png');
// foreach ($imagick as $key => $page) {
//     $pageResolution = $page->getImageResolution();
//     var_dump($pageResolution);

// }
// $imagick->clear();
// echo "Done";