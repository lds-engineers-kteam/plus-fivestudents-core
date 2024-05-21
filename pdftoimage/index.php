<?php
use ZendPdf\PdfDocument;
require_once("vendor/autoload.php");

$pathToPdf = realpath("./filename.pdf");
echo "<pre>";
echo "pathToPdf: {$pathToPdf}\n";
$pathToWhereImageShouldBeStored = realpath("./images/");
echo "pathToWhereImageShouldBeStored: {$pathToWhereImageShouldBeStored}\n";
// $pdf = new Spatie\PdfToImage\Pdf($pathToPdf);
// echo "image extracted: 1\n";
// try {
// 	$pdf->saveAllPagesAsImages($pathToWhereImageShouldBeStored, "a_");
// } catch (Exception $e) {
// 	print_r($e);

// }
// echo "image extracted: DONE\n";



// function readPdfPageByPage($pdfFilePath) {
//     try {
//         // Load the PDF file
//         $pdf = PdfDocument::load($pdfFilePath);

//         // Get the total number of pages
//         $totalPages = count($pdf->pages);

//         // Iterate through each page
//         for ($pageNo = 1; $pageNo <= $totalPages; $pageNo++) {
//             $page = $pdf->pages[$pageNo - 1];
//             $pageText = $page->extractText();

//             echo "Page $pageNo:\n";
//             echo $pageText . "\n\n";
//         }
//     } catch (\Exception $e) {
//         echo "Error: " . $e->getMessage() . "\n";
//     }
// }

// // Replace 'your_pdf_file.pdf' with the path to your PDF file
// $pdfFilePath = realpath("./filename.pdf");

// readPdfPageByPage($pdfFilePath);


try {
$pdfFilePath = realpath("./filename.pdf");
echo "<pre>";
echo "pdfFilePath: {$pdfFilePath}\n";
$outputDirectory = realpath("./images/");
echo "outputDirectory: {$outputDirectory}\n";

    // Create an Imagick object
    $imagick = new Imagick();

    // Read the PDF file
    $imagick->readImage($pdfFilePath);

    // Set the image format to PNG
    $imagick->setImageFormat('png');

    // Create the output directory if it doesn't exist
    if (!file_exists($outputDirectory)) {
        mkdir($outputDirectory, 0777, true);
    }

    // Loop through each page and save it as an image
    foreach ($imagick as $key => $page) {
        // Save the page as an image
        $page->writeImage("$outputDirectory/pdf_page_$key.png");
    }

    // Clear the Imagick object
    $imagick->clear();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}