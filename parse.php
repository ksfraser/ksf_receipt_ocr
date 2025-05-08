<?php
require 'config.php'; // FA database connection

$extractedText = "Milk 2L - $3.49\nBread - $2.99\nEggs - $4.99"; // OCR output example
$lines = explode("\n", $extractedText);

foreach ($lines as $line) {
    preg_match('/(.*?) - \$(\d+\.\d+)/', $line, $matches);
    
    if ($matches) {
        $itemName = trim($matches[1]);
        $price = (float) $matches[2];

        // Search for item in FA database
        $stmt = $pdo->prepare("SELECT stock_id FROM fa_stock_master WHERE description LIKE ?");
        $stmt->execute(["%$itemName%"]);
        $item = $stmt->fetch();

        if ($item) {
            $stock_id = $item['stock_id'];

            // Insert purchase order
            $stmt = $pdo->prepare("INSERT INTO fa_purch_orders (supplier_id, stock_id, price, qty, status) VALUES (?, ?, ?, ?, 'Pending')");
            $stmt->execute([1, $stock_id, $price, 1]); // Assuming supplier_id = 1 for now
        }
    }
}

echo "Purchase Order Created!";
?>
