<?php
/**********************************************************
*
* LOCAL
*
require 'vendor/autoload.php';

use thiagoalessio\TesseractOCR\TesseractOCR;

$imagePath = $_FILES['receipt']['tmp_name'];

$ocr = new TesseractOCR($imagePath);
$extractedText = $ocr->run();

echo "<h3>Extracted Text (Tesseract OCR):</h3><pre>$extractedText</pre>";

*/

/**************************************************************
*
* REMOTE

<?php
$imagePath = $_FILES['receipt']['tmp_name'];
$imageData = file_get_contents($imagePath);

$url = "http://TESSERACT_SERVER_IP:5000/ocr"; // Replace with actual IP

$options = [
    "http" => [
        "header" => "Content-Type: application/octet-stream",
        "method" => "POST",
        "content" => $imageData
    ]
];

$context = stream_context_create($options);
$response = file_get_contents($url, false, $context);

echo "<h3>Extracted Text (Remote Tesseract):</h3><pre>$response</pre>";
*/

