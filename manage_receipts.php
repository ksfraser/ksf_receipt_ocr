<?php
require 'config.php';

echo "<h2>Uploaded Receipts</h2>";
$stmt = $pdo->query("SELECT * FROM fa_receipt_uploads ORDER BY uploaded_at DESC");
$receipts = $stmt->fetchAll();

echo "<table border='1'>
    <tr>
        <th>ID</th><th>File Name</th><th>Vendor</th><th>Receipt Date</th><th>Total ($)</th><th>Uploaded At</th><th>Processed</th><th>Actions</th>
    </tr>";

foreach ($receipts as $receipt) {
    echo "<tr>
        <td>{$receipt['id']}</td>
        <td>{$receipt['file_name']}</td>
        <td>{$receipt['vendor_name']}</td>
        <td>{$receipt['receipt_date']}</td>
        <td>{$receipt['total']}</td>
        <td>{$receipt['uploaded_at']}</td>
        <td>" . ($receipt['processed'] ? "Yes" : "No") . "</td>
        <td>
            <a href='process_receipt.php?id={$receipt['id']}'>Process</a> |
            <a href='delete_receipt.php?id={$receipt['id']}'>Delete</a>
        </td>
    </tr>";
}

echo "</table>";




