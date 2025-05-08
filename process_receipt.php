<?php
require 'vendor/autoload.php';
require 'config.php';

use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use thiagoalessio\TesseractOCR\TesseractOCR;

$ocrService = $_POST['ocr_service'];
$imagePath = $_FILES['receipt']['tmp_name'];
$fileHash = hash_file('sha256', $imagePath);
$fileName = $_FILES['receipt']['name'];
$vendorName = $_POST['vendor_name'];
$receiptDate = $_POST['receipt_date'];


// Check for duplicates
$stmt = $pdo->prepare("SELECT id FROM fa_receipt_uploads WHERE file_hash = ?");
$stmt->execute([$fileHash]);
$existing = $stmt->fetch();

if ($existing) {
    die("Receipt already uploaded! No duplicate processing needed.");
}

// Insert receipt record
$stmt = $pdo->prepare("INSERT INTO fa_receipt_uploads (file_hash, file_name) VALUES (?, ?)");
$stmt->execute([$fileHash, $fileName]);

echo "Receipt uploaded successfully!";
if ($ocrService == "google") {
    // Google Cloud Vision OCR
    $apiKey = 'YOUR_GOOGLE_CLOUD_API_KEY'; // Replace with your actual API key
    $imageAnnotator = new ImageAnnotatorClient([
        'credentials' => json_encode(['api_key' => $apiKey])
    ]);
    $image = file_get_contents($imagePath);
    $response = $imageAnnotator->documentTextDetection($image);
    $textAnnotations = $response->getTextAnnotations();
    $extractedText = !empty($textAnnotations) ? $textAnnotations[0]->getDescription() : "No text detected!";
    $imageAnnotator->close();
} elseif ($ocrService == "tesseract") {
    // Tesseract OCR
    $ocr = new TesseractOCR($imagePath);
    $extractedText = $ocr->run();
} elseif ($ocrService == "microsoft") {
    // Microsoft Azure Form Recognizer OCR (Requires API setup)
    $subscriptionKey = 'YOUR_AZURE_SUBSCRIPTION_KEY';
    $endpoint = 'YOUR_AZURE_ENDPOINT';
    
    $imageData = file_get_contents($imagePath);
    $headers = [
        "Content-Type: application/octet-stream",
        "Ocp-Apim-Subscription-Key: $subscriptionKey"
    ];
    
    $ch = curl_init("$endpoint/vision/v3.0/read/analyze");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $imageData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    
    // Process Azure OCR Response
    $result = json_decode($response, true);
    $extractedText = isset($result['analyzeResult']['readResults'][0]['lines']) 
        ? implode("\n", array_column($result['analyzeResult']['readResults'][0]['lines'], 'text')) 
        : "No text detected!";
} else {
    $extractedText = "Invalid OCR selection!";
}

// Display Extracted Text
echo "<h3>Extracted Text:</h3><pre>$extractedText</pre>";







<?php
require 'config.php';

$imagePath = $_FILES['receipt']['tmp_name'];
$fileHash = hash_file('sha256', $imagePath);
$fileName = $_FILES['receipt']['name'];
$vendorName = $_POST['vendor_name'];
$receiptDate = $_POST['receipt_date'];

// Extract total amount using OCR (assuming we get "$XX.XX" from text)
$extractedText = "Vendor ABC\nMilk 2L - $3.49\nBread - $2.99\nTotal - $6.48"; // Example OCR output
preg_match('/Total - \$([\d.]+)/', $extractedText, $matches);
$totalAmount = isset($matches[1]) ? (float) $matches[1] : 0;

// Prevent duplicate uploads
$stmt = $pdo->prepare("SELECT id FROM fa_receipt_uploads WHERE file_hash = ?");
$stmt->execute([$fileHash]);
if ($stmt->fetch()) {
    die("Receipt already uploaded!");
}

// Insert receipt details into tracking table
$stmt = $pdo->prepare("INSERT INTO fa_receipt_uploads (file_hash, file_name, vendor_name, receipt_date, total) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$fileHash, $fileName, $vendorName, $receiptDate, $totalAmount]);

echo "Receipt uploaded successfully!";
?>
