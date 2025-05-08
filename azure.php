re 'vendor/autoload.php';

use GuzzleHttp\Client;

$subscriptionKey = 'YOUR_AZURE_SUBSCRIPTION_KEY';
$endpoint = 'YOUR_AZURE_ENDPOINT';
$imagePath = $_FILES['receipt']['tmp_name'];

$client = new Client();
$imageData = file_get_contents($imagePath);

$response = $client->post("$endpoint/vision/v3.0/read/analyze", [
    'headers' => [
        "Content-Type" => "application/octet-stream",
        "Ocp-Apim-Subscription-Key" => $subscriptionKey
    ],
    'body' => $imageData
]);

$result = json_decode($response->getBody(), true);

$extractedText = isset($result['analyzeResult']['readResults'][0]['lines']) 
    ? implode("\n", array_column($result['analyzeResult']['readResults'][0]['lines'], 'text')) 
    : "No text detected!";

echo "<h3>Extracted Text (Azure Form Recognizer):</h3><pre>$extractedText</pre>";
?>
