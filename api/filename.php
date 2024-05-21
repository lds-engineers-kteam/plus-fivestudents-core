<?php
try {
    // Create an Imagick object
    $imagick = new Imagick();

    // Read the PDF file
    $handle = fopen("https://qamoodle.fivestudents.com/pdftoimage/filename.pdf", 'rb');
    $imagick->readImageFile($handle);
    $imagick->setImageFormat('png');

    // Loop through each page and save it as an image
    foreach ($imagick as $key => $page) {
        echo "<pre>";
        echo $key;
        $ppp = $page->getImage();
        var_dump($ppp);
        // $page->writeImage("$outputDirectory/pdf_page_$key.png");
        echo "</pre>";
    }

    // Clear the Imagick object
    $imagick->clear();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    print_r($e);
}